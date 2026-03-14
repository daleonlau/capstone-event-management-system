<template>
    <div class="bg-white rounded-2xl shadow-lg p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
          <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
          </svg>
          Bulk Upload Student Responses
        </h3>
        <button 
          @click="downloadTemplate"
          class="px-3 py-1 text-sm bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition flex items-center gap-1"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          Download CSV Template
        </button>
      </div>
  
      <div class="text-sm text-gray-600 mb-4">
        <p>Upload a CSV file with student responses to quickly reach the 75% threshold. The CSV must match the template format.</p>
        <ul class="list-disc list-inside mt-2 text-xs text-gray-500">
          <li>First row must be column headers</li>
          <li>Student ID must exist in the system</li>
          <li>Ratings must be 1-5</li>
          <li>Email and department are required</li>
        </ul>
      </div>
  
      <!-- Upload Area -->
      <div 
        class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-emerald-500 transition cursor-pointer"
        :class="{ 'bg-emerald-50': isDragging }"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop"
        @click="$refs.fileInput.click()"
      >
        <input
          ref="fileInput"
          type="file"
          accept=".csv"
          class="hidden"
          @change="handleFileSelect"
        />
  
        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        
        <p v-if="!selectedFile" class="text-gray-600 mb-2">
          <span class="font-semibold">Click to upload</span> or drag and drop
        </p>
        <p v-else class="text-emerald-600 font-semibold mb-2">
          Selected: {{ selectedFile.name }}
        </p>
        <p class="text-sm text-gray-500">CSV files only (Max 10MB)</p>
      </div>
  
      <!-- Upload Progress -->
      <div v-if="uploading" class="mt-4">
        <div class="flex justify-between text-sm mb-1">
          <span>Uploading...</span>
          <span>{{ uploadProgress }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="bg-emerald-600 rounded-full h-2 transition-all" :style="{ width: uploadProgress + '%' }"></div>
        </div>
      </div>
  
      <!-- Results -->
      <div v-if="results" class="mt-4 p-4 rounded-lg" :class="results.success > 0 ? 'bg-green-50' : 'bg-yellow-50'">
        <div class="flex items-center gap-2 mb-2">
          <svg v-if="results.success > 0" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-else class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <span class="font-medium" :class="results.success > 0 ? 'text-green-800' : 'text-yellow-800'">
            {{ results.message }}
          </span>
        </div>
        
        <div v-if="results.stats" class="text-sm space-y-1">
          <p>✅ Successfully imported: <span class="font-bold">{{ results.stats.success }}</span></p>
          <p v-if="results.stats.errors > 0">❌ Failed: <span class="font-bold">{{ results.stats.errors }}</span></p>
          <div v-if="results.stats.error_details?.length > 0" class="mt-2 max-h-40 overflow-y-auto">
            <p class="font-medium text-red-600 mb-1">Errors:</p>
            <ul class="list-disc list-inside text-xs text-red-600">
              <li v-for="(error, idx) in results.stats.error_details.slice(0, 10)" :key="idx">
                {{ error }}
              </li>
              <li v-if="results.stats.error_details.length > 10">... and {{ results.stats.error_details.length - 10 }} more</li>
            </ul>
          </div>
        </div>
      </div>
  
      <!-- Error Display -->
      <div v-if="error" class="mt-4 p-3 bg-red-50 rounded-lg text-red-700 text-sm">
        {{ error }}
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import axios from 'axios';
  
  const props = defineProps({
    evaluationId: {
      type: Number,
      required: true
    }
  });
  
  const emit = defineEmits(['upload-complete']);
  
  const isDragging = ref(false);
  const selectedFile = ref(null);
  const uploading = ref(false);
  const uploadProgress = ref(0);
  const results = ref(null);
  const error = ref(null);
  
  function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
      selectedFile.value = file;
      uploadFile(file);
    }
  }
  
  function handleDrop(event) {
    isDragging.value = false;
    const file = event.dataTransfer.files[0];
    if (file) {
      selectedFile.value = file;
      uploadFile(file);
    }
  }
  
  async function downloadTemplate() {
    try {
      const response = await axios.get(`/president/evaluations/${props.evaluationId}/download-template`, {
        responseType: 'blob'
      });
      
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `evaluation_${props.evaluationId}_template.csv`);
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (err) {
      error.value = 'Failed to download template';
    }
  }
  
  async function uploadFile(file) {
    uploading.value = true;
    uploadProgress.value = 0;
    results.value = null;
    error.value = null;
  
    const formData = new FormData();
    formData.append('csv_file', file);
  
    try {
      const response = await axios.post(`/president/evaluations/${props.evaluationId}/bulk-upload`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        },
        onUploadProgress: (progressEvent) => {
          uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
        }
      });
  
      results.value = response.data;
      emit('upload-complete', response.data);
      
      // Clear file input
      selectedFile.value = null;
      document.querySelector('input[type="file"]').value = '';
      
    } catch (err) {
      if (err.response?.data?.details) {
        error.value = err.response.data.details.join('\n');
      } else {
        error.value = err.response?.data?.error || 'Upload failed';
      }
    } finally {
      uploading.value = false;
      uploadProgress.value = 0;
    }
  }
  </script>