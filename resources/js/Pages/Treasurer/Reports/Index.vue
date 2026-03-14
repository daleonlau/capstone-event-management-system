<template>
    <OrganizationUserLayout>
      <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Collection Reports</h1>
            <p class="text-gray-500 mt-1">Generate and download PDF reports for collections</p>
          </div>
        </div>
  
        <!-- Report Type Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Collection Report Card -->
          <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition">
            <div class="flex items-center gap-4 mb-4">
              <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-semibold text-gray-800">Collection Report</h2>
                <p class="text-sm text-gray-500">Detailed collection report for a specific event</p>
              </div>
            </div>
  
            <form @submit.prevent="generateCollectionReport" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Event <span class="text-red-500">*</span></label>
                <select
                  v-model="collectionForm.event_id"
                  class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                  required
                >
                  <option value="">Choose an event...</option>
                  <option v-for="event in events" :key="event.id" :value="event.id">
                    {{ event.event_name }} ({{ formatDate(event.event_date_start) }})
                  </option>
                </select>
              </div>
  
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                  <input
                    v-model="collectionForm.date_from"
                    type="date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                  <input
                    v-model="collectionForm.date_to"
                    type="date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                  />
                </div>
              </div>
  
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                <select
                  v-model="collectionForm.status"
                  class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                >
                  <option value="all">All Students</option>
                  <option value="paid">Paid Only</option>
                  <option value="pending">Pending Only</option>
                  <option value="not_paid">Not Paid Only</option>
                </select>
              </div>
  
              <div class="flex justify-end">
                <button
                  type="submit"
                  class="px-6 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition flex items-center gap-2"
                  :disabled="collectionForm.processing"
                >
                  <svg v-if="collectionForm.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                  </svg>
                  Generate PDF Report
                </button>
              </div>
            </form>
          </div>
  
          <!-- Summary Report Card -->
          <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition">
            <div class="flex items-center gap-4 mb-4">
              <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-semibold text-gray-800">Summary Report</h2>
                <p class="text-sm text-gray-500">Overview of all collections within a date range</p>
              </div>
            </div>
  
            <form @submit.prevent="generateSummaryReport" class="space-y-4">
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Date From <span class="text-red-500">*</span></label>
                  <input
                    v-model="summaryForm.date_from"
                    type="date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    required
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Date To <span class="text-red-500">*</span></label>
                  <input
                    v-model="summaryForm.date_to"
                    type="date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    required
                  />
                </div>
              </div>
  
              <div class="flex justify-end">
                <button
                  type="submit"
                  class="px-6 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition flex items-center gap-2"
                  :disabled="summaryForm.processing"
                >
                  <svg v-if="summaryForm.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                  </svg>
                  Generate Summary PDF
                </button>
              </div>
            </form>
          </div>
        </div>
  
        <!-- Recent Reports Preview (Optional) -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mt-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Report Templates Preview</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border rounded-xl p-4">
              <h3 class="font-medium text-gray-700 mb-2">Collection Report Includes:</h3>
              <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                <li>Organization and school details</li>
                <li>Event information and filters applied</li>
                <li>Summary statistics (total, paid, pending, not paid)</li>
                <li>Collection rate and financial summary</li>
                <li>Detailed student list with payment status</li>
                <li>Signature lines for Treasurer, Adviser, President</li>
              </ul>
            </div>
            <div class="border rounded-xl p-4">
              <h3 class="font-medium text-gray-700 mb-2">Summary Report Includes:</h3>
              <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                <li>Organization and school details</li>
                <li>Date range summary</li>
                <li>Overall statistics across all events</li>
                <li>Per-event breakdown with collection rates</li>
                <li>Total collections and expected amounts</li>
                <li>Signature lines for verification</li>
              </ul>
            </div>
          </div>
          <p class="text-xs text-gray-400 mt-4 text-center">
            Reports are generated as PDF files that you can save or print. The layout is customizable in the PDF templates.
          </p>
        </div>
      </div>
    </OrganizationUserLayout>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useForm } from '@inertiajs/vue3';
  import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
  
  const props = defineProps({
    events: {
      type: Array,
      default: () => []
    }
  });
  
  // Collection Report Form
  const collectionForm = useForm({
    event_id: '',
    date_from: '',
    date_to: '',
    status: 'all'
  });
  
  // Summary Report Form
  const summaryForm = useForm({
    date_from: '',
    date_to: ''
  });
  
  function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  }
  
  function generateCollectionReport() {
    // Create a form element to handle the download
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/treasurer/reports/collection';
    form.target = '_blank';
  
    // Add CSRF token
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
    form.appendChild(csrf);
  
    // Add form data
    const fields = {
      event_id: collectionForm.event_id,
      date_from: collectionForm.date_from,
      date_to: collectionForm.date_to,
      status: collectionForm.status
    };
  
    Object.keys(fields).forEach(key => {
      if (fields[key]) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = fields[key];
        form.appendChild(input);
      }
    });
  
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
  }
  
  function generateSummaryReport() {
    // Create a form element to handle the download
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/treasurer/reports/summary';
    form.target = '_blank';
  
    // Add CSRF token
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
    form.appendChild(csrf);
  
    // Add form data
    const fields = {
      date_from: summaryForm.date_from,
      date_to: summaryForm.date_to
    };
  
    Object.keys(fields).forEach(key => {
      if (fields[key]) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = fields[key];
        form.appendChild(input);
      }
    });
  
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
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