<template>
    <AdminLayout>
      <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <!-- Header -->
          <div class="mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              Evaluation Reports
            </h1>
            <p class="text-gray-500 mt-1">Generate and send evaluation reports to organizations</p>
          </div>
  
          <!-- Stats Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Closed Evaluations</p>
                  <p class="text-2xl font-bold text-gray-800">{{ evaluations.length }}</p>
                </div>
              </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Reports Generated</p>
                  <p class="text-2xl font-bold text-green-600">{{ generatedCount }}</p>
                </div>
              </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Reports Sent</p>
                  <p class="text-2xl font-bold text-purple-600">{{ sentCount }}</p>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Reports Table -->
          <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-emerald-500">
                  <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Event</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Organization</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Responses</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="evaluation in evaluations" :key="evaluation.id" class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                      <div>
                        <p class="font-medium text-gray-900">{{ evaluation.event_name }}</p>
                        <p class="text-xs text-gray-500">{{ formatDate(evaluation.event_date) }}</p>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs">
                        {{ evaluation.organization_name }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <span class="text-sm text-gray-600">{{ evaluation.responses_count }} responses</span>
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex flex-col gap-1">
                        <span v-if="evaluation.report_generated_at" class="text-xs text-green-600 flex items-center gap-1">
                          <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                          </svg>
                          Generated: {{ formatDateShort(evaluation.report_generated_at) }}
                        </span>
                        <span v-else class="text-xs text-yellow-600">Not generated</span>
                        <span v-if="evaluation.report_sent_at" class="text-xs text-blue-600 flex items-center gap-1">
                          <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                          </svg>
                          Sent: {{ formatDateShort(evaluation.report_sent_at) }}
                        </span>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex gap-2 flex-wrap">
                        <!-- Generate Button -->
                        <button
                          v-if="!evaluation.report_generated_at"
                          @click="generateReport(evaluation)"
                          :disabled="generating[evaluation.id]"
                          class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm flex items-center gap-1"
                        >
                          <svg v-if="generating[evaluation.id]" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                          <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                          </svg>
                          Generate
                        </button>
                        
                        <!-- Regenerate Button -->
                        <button
                          v-if="evaluation.report_generated_at"
                          @click="regenerateReport(evaluation)"
                          :disabled="regenerating[evaluation.id]"
                          class="px-3 py-1.5 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition text-sm flex items-center gap-1"
                        >
                          <svg v-if="regenerating[evaluation.id]" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                          <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                          </svg>
                          Regenerate
                        </button>
                        
                        <!-- View Button -->
                        <a
                          v-if="evaluation.report_path"
                          :href="`/admin/reports/${evaluation.id}/view`"
                          target="_blank"
                          class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm flex items-center gap-1"
                        >
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                          </svg>
                          View
                        </a>
                        
                        <!-- Download Button -->
                        <a
                          v-if="evaluation.report_path"
                          :href="`/admin/reports/${evaluation.id}/download`"
                          class="px-3 py-1.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm flex items-center gap-1"
                        >
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                          </svg>
                          Download
                        </a>
                        
                        <!-- Send Button -->
                        <button
                          v-if="evaluation.report_path && !evaluation.report_sent_at"
                          @click="sendReport(evaluation)"
                          :disabled="sending[evaluation.id]"
                          class="px-3 py-1.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm flex items-center gap-1"
                        >
                          <svg v-if="sending[evaluation.id]" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                          <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                          </svg>
                          Send to Org
                        </button>
                        
                        <!-- Sent Status Badge -->
                        <span v-if="evaluation.report_sent_at" class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-sm flex items-center gap-1">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                          </svg>
                          Sent
                        </span>
                      </div>
                    </td>
                  </tr>
                  
                  <tr v-if="evaluations.length === 0">
                    <td colspan="5" class="px-6 py-12 text-center">
                      <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      <p class="text-gray-500">No closed evaluations available for report generation.</p>
                      <p class="text-sm text-gray-400 mt-1">Evaluations will appear here once they are closed and have responses.</p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Toast Notification -->
      <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-x-full opacity-0"
        enter-to-class="translate-x-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="translate-x-full opacity-0"
      >
        <div v-if="toast.show" 
             class="fixed bottom-4 right-4 z-50 flex min-w-[320px] items-center gap-3 rounded-xl border-l-4 p-4 shadow-2xl backdrop-blur-sm"
             :class="toast.bgClass">
          <span class="flex-1">{{ toast.message }}</span>
          <button @click="toast.show = false" class="text-gray-400 hover:text-gray-600">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </Transition>
    </AdminLayout>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue';
  import { router } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';
  import axios from 'axios';
  
  const props = defineProps({
    evaluations: {
      type: Array,
      default: () => []
    }
  });
  
  const generating = ref({});
  const regenerating = ref({});
  const sending = ref({});
  const toast = ref({ show: false, message: '', type: 'success', bgClass: '' });
  
  const generatedCount = computed(() => {
    return props.evaluations.filter(e => e.report_generated_at).length;
  });
  
  const sentCount = computed(() => {
    return props.evaluations.filter(e => e.report_sent_at).length;
  });
  
  function formatDate(date) {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }
  
  function formatDateShort(date) {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric'
    });
  }
  
  function showToast(message, type = 'success') {
    const colors = {
      success: 'border-green-500 bg-green-50 text-green-800',
      error: 'border-red-500 bg-red-50 text-red-800',
      info: 'border-blue-500 bg-blue-50 text-blue-800'
    };
    
    toast.value = { 
      show: true, 
      message, 
      type,
      bgClass: colors[type] || colors.success
    };
    
    setTimeout(() => toast.value.show = false, 5000);
  }
  
  async function generateReport(evaluation) {
    if (generating.value[evaluation.id]) return;
    
    generating.value[evaluation.id] = true;
    showToast('Generating report...', 'info');
    
    try {
      const response = await axios.post(`/admin/reports/${evaluation.id}/generate`);
      
      if (response.data.success) {
        showToast('✅ Report generated successfully!', 'success');
        setTimeout(() => router.reload(), 1500);
      } else {
        showToast(response.data.error || 'Failed to generate report', 'error');
        generating.value[evaluation.id] = false;
      }
    } catch (error) {
      console.error('Generate error:', error);
      const errorMessage = error.response?.data?.error || error.message || 'Failed to generate report';
      showToast(errorMessage, 'error');
      generating.value[evaluation.id] = false;
    }
  }
  
  async function regenerateReport(evaluation) {
    if (regenerating.value[evaluation.id]) return;
    
    if (!confirm('Are you sure you want to regenerate this report? This will update the report with the latest data.')) return;
    
    regenerating.value[evaluation.id] = true;
    showToast('Regenerating report with latest data...', 'info');
    
    try {
      const response = await axios.post(`/admin/reports/${evaluation.id}/regenerate`);
      
      if (response.data.success) {
        showToast('✅ Report regenerated successfully with latest data!', 'success');
        setTimeout(() => router.reload(), 1500);
      } else {
        showToast(response.data.error || 'Failed to regenerate report', 'error');
        regenerating.value[evaluation.id] = false;
      }
    } catch (error) {
      console.error('Regenerate error:', error);
      let errorMessage = 'Failed to regenerate report';
      
      if (error.response?.data?.error) {
        errorMessage = error.response.data.error;
      } else if (error.message) {
        errorMessage = error.message;
      }
      
      showToast(errorMessage, 'error');
      regenerating.value[evaluation.id] = false;
    }
  }
  
  async function sendReport(evaluation) {
    if (sending.value[evaluation.id]) return;
    
    if (!confirm(`Send evaluation report to ${evaluation.organization_name}?`)) return;
    
    sending.value[evaluation.id] = true;
    showToast('Sending report...', 'info');
    
    try {
      const response = await axios.post(`/admin/reports/${evaluation.id}/send`);
      
      if (response.data.success) {
        showToast(`✅ Report sent successfully to ${response.data.sent_to}!`, 'success');
        setTimeout(() => router.reload(), 1500);
      } else {
        showToast(response.data.error || 'Failed to send report', 'error');
        sending.value[evaluation.id] = false;
      }
    } catch (error) {
      console.error('Send error:', error);
      showToast(error.response?.data?.error || 'Failed to send report', 'error');
      sending.value[evaluation.id] = false;
    }
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
  </style>