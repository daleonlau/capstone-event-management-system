<template>
  <OrganizationUserLayout>
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Bulk Upload Students</h1>
        <p class="text-gray-500 mt-1">Import multiple students from CSV file</p>
      </div>

      <!-- Organization Info Card -->
      <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-xl p-4 mb-6">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-emerald-800">Your Organization's Allowed College & Programs</h3>
            <div class="mt-2 grid grid-cols-2 gap-4">
              <div>
                <p class="text-xs font-semibold text-emerald-700">College:</p>
                <ul class="text-xs text-emerald-600 list-disc list-inside">
                  <li v-for="dept in allowedDepartments" :key="dept">{{ dept }}</li>
                  <li v-if="allowedDepartments.length === 0" class="text-yellow-600">No college assigned</li>
                </ul>
              </div>
              <div>
                <p class="text-xs font-semibold text-emerald-700">Programs:</p>
                <ul class="text-xs text-emerald-600 list-disc list-inside">
                  <li v-for="course in allowedCourses" :key="course">{{ course }}</li>
                  <li v-if="allowedCourses.length === 0" class="text-yellow-600">No programs assigned</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Instructions Card -->
      <div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4 mb-6">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">CSV File Requirements</h3>
            <ul class="mt-2 text-sm text-blue-700 list-disc list-inside">
              <li>Format: .csv only (Max size: 5MB)</li>
              <li>First row must be headers: <code class="bg-blue-100 px-1 rounded">student_id,firstname,lastname,email,program,college,yearlevel</code></li>
              <li><span class="font-semibold">Program must match</span> one of your assigned programs (listed above)</li>
              <li><span class="font-semibold">College must match</span> one of your assigned college (listed above)</li>
              <li>Year level must be: 1st Year, 2nd Year, 3rd Year, or 4th Year</li>
              <li>Example: <code class="bg-blue-100 px-1 rounded">CTHM-2024-0001,Maria,Santos,maria@email.com,Bachelor of Science in Hospitality Management,CTHM,1st Year</code></li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Template Download -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-medium text-gray-800">Download CSV Template</h3>
            <p class="text-sm text-gray-500">Use this template to format your data correctly</p>
          </div>
          <button @click="downloadTemplate" class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Download Template.csv
          </button>
        </div>
      </div>

      <!-- Upload Form -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <form @submit.prevent="submit" enctype="multipart/form-data">
          <!-- File Upload Area -->
          <div 
            class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-emerald-500 transition cursor-pointer mb-4"
            @dragover.prevent
            @drop.prevent="handleDrop"
            @click="$refs.fileInput.click()"
          >
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            
            <p class="text-gray-600 mb-2">
              <span class="font-semibold">Click to upload</span> or drag and drop
            </p>
            <p class="text-sm text-gray-500">
              {{ fileName ? `Selected: ${fileName}` : 'CSV files only' }}
            </p>
          </div>

          <input
            ref="fileInput"
            type="file"
            @change="handleFileChange"
            accept=".csv"
            class="hidden"
          />

          <!-- Error Display -->
          <div v-if="form.errors.file" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-600">{{ form.errors.file }}</p>
          </div>

          <!-- Preview -->
          <div v-if="previewLines.length > 0" class="mt-4 bg-gray-50 rounded-xl p-4">
            <h3 class="font-medium text-gray-800 mb-2">File Preview (first 3 lines)</h3>
            <div class="font-mono text-sm">
              <div v-for="(line, index) in previewLines" :key="index" class="py-1" :class="{ 'text-emerald-600 font-semibold': index === 0 }">
                {{ line }}
              </div>
              <div v-if="previewLines.length > 3" class="text-gray-400 mt-2">...</div>
            </div>
          </div>

          <!-- Validation Info -->
          <div v-if="previewLines.length > 1" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-xs text-yellow-700">
              <span class="font-semibold">Note:</span> Only rows with valid college and program (matching your organization's assignments) will be imported.
            </p>
          </div>

          <!-- Action Buttons -->
          <div class="flex justify-end gap-3 mt-6">
            <Link
              href="/president/students"
              class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition"
            >
              Cancel
            </Link>
            <button
              type="submit"
              class="px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition disabled:opacity-50 flex items-center gap-2"
              :disabled="form.processing || !form.file"
            >
              <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ form.processing ? 'Uploading...' : 'Upload & Import' }}</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Success Modal -->
      <Transition name="modal">
        <div v-if="showSuccessModal" class="fixed inset-0 z-50 overflow-y-auto">
          <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showSuccessModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                  <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg font-medium text-gray-900">Success!</h3>
                    <div class="mt-2">
                      <p class="text-sm text-gray-500">{{ successMessage }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <Link
                  href="/president/students"
                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm"
                >
                  View Students
                </Link>
                <button
                  @click="showSuccessModal = false"
                  class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                >
                  Close
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </OrganizationUserLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';

const props = defineProps({
  allowedCourses: {
    type: Array,
    default: () => []
  },
  allowedDepartments: {
    type: Array,
    default: () => []
  }
});

const form = useForm({
  file: null
});

const fileName = ref('');
const previewLines = ref([]);
const showSuccessModal = ref(false);
const successMessage = ref('');

// Handle file selection
function handleFileChange(e) {
  const file = e.target.files[0];
  if (file) {
    // Check file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
      alert('File size must not exceed 5MB');
      return;
    }
    
    // Check file type
    if (!file.name.endsWith('.csv')) {
      alert('Please upload a CSV file');
      return;
    }
    
    form.file = file;
    fileName.value = file.name;
    previewCSV(file);
  }
}

// Handle drag and drop
function handleDrop(e) {
  const file = e.dataTransfer.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      alert('File size must not exceed 5MB');
      return;
    }
    if (!file.name.endsWith('.csv')) {
      alert('Please upload a CSV file');
      return;
    }
    form.file = file;
    fileName.value = file.name;
    previewCSV(file);
  }
}

// Preview CSV content
function previewCSV(file) {
  const reader = new FileReader();
  reader.onload = (e) => {
    const content = e.target.result;
    const lines = content.split('\n').slice(0, 4); // Get first 4 lines
    previewLines.value = lines;
  };
  reader.readAsText(file);
}

// Download template
function downloadTemplate() {
  const headers = ['student_id', 'firstname', 'lastname', 'email', 'course', 'department', 'yearlevel'];
  
  // Use first allowed values for sample data
  const sampleCourse = props.allowedCourses[0] || 'Bachelor of Science in Hospitality Management';
  const sampleDept = props.allowedDepartments[0] || 'CTHM';
  
  const sampleData = [
    ['CTHM-2024-0001', 'Maria', 'Santos', 'maria.santos@student.csu.edu.ph', sampleCourse, sampleDept, '1st Year'],
    ['CTHM-2024-0002', 'Jose', 'Reyes', 'jose.reyes@student.csu.edu.ph', sampleCourse, sampleDept, '1st Year']
  ];
  
  // Create CSV content
  let csvContent = headers.join(',') + '\n';
  sampleData.forEach(row => {
    csvContent += row.join(',') + '\n';
  });
  
  // Create download link
  const blob = new Blob([csvContent], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'student_template.csv';
  a.click();
  window.URL.revokeObjectURL(url);
}

// Submit form
function submit() {
  if (!form.file) {
    alert('Please select a file to upload.');
    return;
  }

  form.post('/president/students/bulk-upload', {
    forceFormData: true,
    onSuccess: (page) => {
      successMessage.value = page.props.flash?.success || 'Students uploaded successfully!';
      showSuccessModal.value = true;
      form.reset();
      fileName.value = '';
      previewLines.value = [];
    },
    onError: (errors) => {
      console.error('Upload failed:', errors);
      if (errors.file) {
        alert('Upload failed: ' + errors.file);
      }
    }
  });
}
</script>

<style scoped>
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
.animate-spin {
  animation: spin 1s linear infinite;
}

.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>