<template>
    <AdminLayout>
      <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <!-- Header -->
          <div class="mb-8">
            <Link 
              href="/admin/evaluations" 
              class="inline-flex items-center gap-2 text-gray-500 hover:text-emerald-600 transition mb-4 group"
            >
              <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Back to Evaluations
            </Link>
            
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <div>
                <div class="flex items-center gap-3 mb-2">
                  <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 shadow-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </div>
                  <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Create Evaluation Form
                  </h1>
                </div>
                <p class="text-gray-500 ml-15">Create a custom evaluation form based on the organization's request</p>
              </div>
            </div>
          </div>
  
          <!-- Error Alert -->
          <div v-if="form.errors.error" class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-xl p-4">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <p class="text-red-700">{{ form.errors.error }}</p>
            </div>
          </div>
  
          <!-- Request Information Card -->
          <div v-if="selectedRequest" class="mb-8">
            <div class="bg-gradient-to-r from-purple-50 via-white to-pink-50 rounded-2xl shadow-lg overflow-hidden border border-purple-100">
              <div class="px-6 py-5 border-b border-purple-100 bg-white/50">
                <div class="flex items-center gap-2">
                  <span class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></span>
                  <h2 class="text-lg font-semibold text-gray-800">Evaluation Request Details</h2>
                </div>
              </div>
              
              <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                  <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                      <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 shadow-md flex items-center justify-center">
                        <span class="text-white text-xl font-bold">{{ selectedRequest.organization_name?.charAt(0) || 'O' }}</span>
                      </div>
                      <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ selectedRequest.title }}</h3>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                          <span class="px-2.5 py-1 bg-purple-100 text-purple-700 rounded-lg text-xs font-medium">
                            {{ selectedRequest.organization_name }}
                          </span>
                          <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs">
                            Event: {{ selectedRequest.event_name }}
                          </span>
                        </div>
                      </div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                      <div class="flex items-center gap-2 p-2 bg-white rounded-xl shadow-sm">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                        </svg>
                        <div>
                          <p class="text-xs text-gray-500">Date</p>
                          <p class="text-sm font-medium text-gray-700">{{ selectedRequest.activity_date }}</p>
                        </div>
                      </div>
                      <div class="flex items-center gap-2 p-2 bg-white rounded-xl shadow-sm">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div>
                          <p class="text-xs text-gray-500">Venue</p>
                          <p class="text-sm font-medium text-gray-700">{{ selectedRequest.venue }}</p>
                        </div>
                      </div>
                      <div class="flex items-center gap-2 p-2 bg-white rounded-xl shadow-sm">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <div>
                          <p class="text-xs text-gray-500">Speaker</p>
                          <p class="text-sm font-medium text-gray-700">{{ selectedRequest.speaker_name }}</p>
                        </div>
                      </div>
                      <div class="flex items-center gap-2 p-2 bg-white rounded-xl shadow-sm">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                          <p class="text-xs text-gray-500">Food</p>
                          <p class="text-sm font-medium text-gray-700">{{ selectedRequest.has_food ? 'With Food' : 'No Food' }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
  
          <div v-else-if="!loading && !selectedRequest" class="bg-yellow-50 border-l-4 border-yellow-500 rounded-xl p-6 mb-8">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <div>
                <p class="font-medium text-yellow-800">No Request Selected</p>
                <p class="text-sm text-yellow-700">Please select a pending request from the evaluation management page.</p>
                <Link href="/admin/evaluations" class="inline-flex items-center gap-2 mt-3 text-sm text-purple-600 hover:text-purple-700">
                  Go to Pending Requests
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </Link>
              </div>
            </div>
          </div>
  
          <!-- Evaluation Form Creation -->
          <div v-if="selectedRequest" class="space-y-6">
            <form @submit.prevent="submit" class="space-y-6">
              <!-- Form Type Selection -->
              <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-gray-100">
                  <h2 class="text-lg font-semibold text-gray-800">Select Form Type *</h2>
                  <p class="text-sm text-gray-500 mt-1">Choose the appropriate evaluation template for this event</p>
                </div>
                <div class="p-6">
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div 
                      v-for="(name, type) in formTypes" 
                      :key="type"
                      @click="selectFormType(type)"
                      class="relative cursor-pointer group"
                    >
                      <div class="p-4 rounded-xl border-2 transition-all duration-200 hover:shadow-lg"
                           :class="form.form_type === type 
                             ? 'border-emerald-500 bg-emerald-50 shadow-md' 
                             : 'border-gray-200 hover:border-emerald-300'">
                        <div class="flex items-center justify-between mb-2">
                          <div class="w-10 h-10 rounded-lg" :class="form.form_type === type ? 'bg-emerald-500' : 'bg-gray-100'">
                            <div class="w-full h-full flex items-center justify-center">
                              <svg class="w-5 h-5" :class="form.form_type === type ? 'text-white' : 'text-gray-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                              </svg>
                            </div>
                          </div>
                          <div v-if="form.form_type === type" class="w-5 h-5 bg-emerald-500 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                          </div>
                        </div>
                        <h3 class="font-medium text-gray-800 mb-1">{{ name }}</h3>
                        <p class="text-xs text-gray-500">{{ getFormTypeDescription(type) }}</p>
                      </div>
                    </div>
                  </div>
                  <p v-if="form.errors.form_type" class="mt-2 text-sm text-red-600">{{ form.errors.form_type }}</p>
                </div>
              </div>
  
              <!-- Basic Information -->
              <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                  <h2 class="text-lg font-semibold text-gray-800">Basic Information</h2>
                  <p class="text-sm text-gray-500 mt-1">Fill in the evaluation form details</p>
                </div>
                <div class="p-6 space-y-5">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Evaluation Title *</label>
                    <input 
                      v-model="form.title" 
                      type="text" 
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                      placeholder="e.g., EVENT EVALUATION FORM"
                      required
                    />
                    <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Form Number *</label>
                      <input 
                        v-model="form.form_number" 
                        type="text" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                        placeholder="F-EEF-018a"
                        required
                      />
                      <p v-if="form.errors.form_number" class="mt-1 text-sm text-red-600">{{ form.errors.form_number }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Revision *</label>
                      <input 
                        v-model="form.revision" 
                        type="text" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                        placeholder="Rev. 0"
                        required
                      />
                      <p v-if="form.errors.revision" class="mt-1 text-sm text-red-600">{{ form.errors.revision }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Date Effectivity *</label>
                      <input 
                        v-model="form.date_effectivity" 
                        type="text" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                        placeholder="04-28-2025"
                        required
                      />
                      <p v-if="form.errors.date_effectivity" class="mt-1 text-sm text-red-600">{{ form.errors.date_effectivity }}</p>
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Available From</label>
                      <input 
                        v-model="form.available_from" 
                        type="datetime-local" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Available Until</label>
                      <input 
                        v-model="form.available_until" 
                        type="datetime-local" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                      />
                    </div>
                  </div>
                </div>
              </div>
  
              <!-- Form Template Preview -->
              <div v-if="form.form_type" class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-100">
                  <h2 class="text-lg font-semibold text-gray-800">Form Template Preview</h2>
                  <p class="text-sm text-gray-500 mt-1">This template will be used for the evaluation form</p>
                </div>
                <div class="p-6 max-h-96 overflow-y-auto">
                  <div v-for="category in templateCategories" :key="category.name" class="mb-6">
                    <h3 class="font-semibold text-gray-800 mb-3">{{ category.name }}</h3>
                    <ul class="space-y-2 ml-4">
                      <li v-for="(question, idx) in category.questions" :key="idx" class="text-sm text-gray-600 flex items-start gap-2">
                        <span class="text-emerald-500 mt-0.5">•</span>
                        <span>{{ question.text }}</span>
                      </li>
                    </ul>
                  </div>
                  <div v-if="templateComments.length > 0" class="mt-4 pt-4 border-t border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-3">Comment Sections</h3>
                    <ul class="space-y-2 ml-4">
                      <li v-for="(comment, idx) in templateComments" :key="idx" class="text-sm text-gray-600 flex items-start gap-2">
                        <span class="text-purple-500 mt-0.5">💬</span>
                        <span>{{ comment.text }}</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
  
              <!-- Validation Errors -->
              <div v-if="Object.keys(form.errors).length > 0 && !form.errors.error" class="bg-red-50 border-l-4 border-red-500 rounded-xl p-4">
                <div class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <div>
                    <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
                    <ul class="list-disc list-inside text-sm text-red-600">
                      <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
                    </ul>
                  </div>
                </div>
              </div>
  
              <!-- Action Buttons -->
              <div class="flex justify-end gap-4">
                <Link 
                  href="/admin/evaluations" 
                  class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all"
                >
                  Cancel
                </Link>
                <button 
                  type="submit" 
                  class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-md hover:shadow-lg flex items-center gap-2"
                  :disabled="form.processing"
                >
                  <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  <span>{{ form.processing ? 'Creating...' : 'Create Evaluation Form' }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- Debug Info (remove after testing) -->
<div class="bg-gray-100 p-4 rounded-lg mb-4 text-xs font-mono">
  <p><strong>Debug:</strong></p>
  <p>Selected Request ID from props: {{ selectedRequestId }}</p>
  <p>Selected Request ID from URL: {{ getUrlParameter('request_id') }}</p>
  <p>Pending Requests Count: {{ pendingRequests.length }}</p>
  <p>Found Selected Request: {{ selectedRequest ? 'Yes' : 'No' }}</p>
  <p v-if="selectedRequest">Request ID: {{ selectedRequest.id }}</p>
</div>
    </AdminLayout>
  </template>
  
  <script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  pendingRequests: {
    type: Array,
    default: () => []
  },
  formTypes: {
    type: Object,
    default: () => ({})
  },
  selectedRequestId: {
    type: String,
    default: null
  }
});

const loading = ref(true);
const selectedRequest = ref(null);

// Function to get URL parameter
function getUrlParameter(name) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(name);
}

// Find the selected request
onMounted(() => {
  // Try to get request_id from props first, then from URL
  let requestId = props.selectedRequestId;
  
  if (!requestId) {
    requestId = getUrlParameter('request_id');
  }
  
  console.log('Request ID from URL/props:', requestId);
  console.log('All pending requests:', props.pendingRequests);
  
  if (requestId && props.pendingRequests.length > 0) {
    selectedRequest.value = props.pendingRequests.find(r => r.id == requestId);
    console.log('Found selected request:', selectedRequest.value);
    
    if (selectedRequest.value) {
      // Set default title based on request
      form.title = `EVENT EVALUATION FORM - ${selectedRequest.value.event_name}`;
      form.evaluation_request_id = selectedRequest.value.id;
    } else {
      console.log('Request not found with ID:', requestId);
    }
  } else {
    console.log('No request ID found or no pending requests');
  }
  
  loading.value = false;
});

const form = useForm({
  evaluation_request_id: selectedRequest.value?.id || '',
  form_type: '',
  title: '',
  form_number: '',
  revision: '',
  date_effectivity: '',
  available_from: '',
  available_until: ''
});

function selectFormType(type) {
  form.form_type = type;
  
  // Auto-fill form details based on selected type
  const formDetails = getFormDetails(type);
  if (formDetails) {
    form.form_number = formDetails.form_number;
    form.revision = formDetails.revision;
    form.date_effectivity = formDetails.date_effectivity;
  }
}

function getFormDetails(type) {
  const details = {
    type1: { form_number: 'F-EEF-018a', revision: 'Rev. 0', date_effectivity: '04-28-2025' },
    type2: { form_number: 'F-EEF-018d', revision: 'Rev. 0', date_effectivity: '04-28-2025' },
    type3: { form_number: 'F-EEF-018e', revision: 'Rev. 0', date_effectivity: '06-16-2025' },
    type4: { form_number: 'F-EEF-018b', revision: 'Rev. 0', date_effectivity: '04-28-2025' },
    type5: { form_number: 'F-EEF-018c', revision: 'Rev. 0', date_effectivity: '04-28-2025' }
  };
  return details[type];
}

function getFormTypeDescription(type) {
  const descriptions = {
    type1: '7 Quality Dimension - Standard event feedback with 7 quality dimensions',
    type2: '5 Quality Dimension - Standard event feedback with 5 quality dimensions',
    type3: '8 Quality Dimension - Comprehensive event feedback including food, speaker, and traffic management',
    type4: '6 Quality Dimension without Meals - Includes speaker evaluation, no food section',
    type5: '6 Quality Dimension without Speaker - Includes food evaluation, no speaker section'
  };
  return descriptions[type] || '';
}

// Template Categories for Preview
const templateCategories = computed(() => {
  if (!form.form_type) return [];
  const templates = {
    type1: [
      { name: 'I. Information Dissemination', questions: [
        { text: 'Timeliness of sending invites' },
        { text: 'Adequacy of information dissemination' }
      ]},
      { name: 'II. Design of the Event', questions: [
        { text: 'Program / Order of activities' },
        { text: 'Relevance of the activities' },
        { text: 'Time allotment / pacing' }
      ]},
      { name: 'III. Outcomes of the Event', questions: [
        { text: 'Attendance of participants' },
        { text: 'Participation to activities' },
        { text: 'Interaction' },
        { text: 'Teamwork' }
      ]},
      { name: 'IV. Secretariat', questions: [
        { text: 'Sensitivity in providing assistance/needs to the participants' },
        { text: 'Management on the entire activities' },
        { text: 'Provision of information/feedback to the participants in a clear, concise manner' }
      ]},
      { name: 'V. Facilities', questions: [
        { text: 'Overall appearance of the venue' },
        { text: 'Cleanliness and orderliness' },
        { text: 'Availability and functionality of applicable equipment' }
      ]},
      { name: 'VI. Food', questions: [
        { text: 'Quality of food and beverages' },
        { text: 'Food and beverages presentation/setup' },
        { text: 'Timelines of delivery of food' },
        { text: 'Quality of service provided' },
        { text: 'Sufficiency of foods' },
        { text: 'Quantity/Serving of food provided' }
      ]},
      { name: 'VII. Resource Speaker', questions: [
        { text: 'Methods/strategy employed' },
        { text: 'Mastery of the subject matter' },
        { text: 'Ability to draw and maintain interest and participation' },
        { text: 'Relevancy and applicability of the topic/content discussed' }
      ]}
    ],
    type2: [
      { name: 'I. Information Dissemination', questions: [
        { text: 'Timelines of sending invites' },
        { text: 'Adequacy of information dissemination' }
      ]},
      { name: 'II. Design of the Event', questions: [
        { text: 'Program / Order of activities' },
        { text: 'Relevance of the activities' },
        { text: 'Time allotment / pacing' }
      ]},
      { name: 'III. Outcomes of the Event', questions: [
        { text: 'Attendance of participants' },
        { text: 'Participation to activities' },
        { text: 'Interaction' },
        { text: 'Teamwork' }
      ]},
      { name: 'IV. Secretariat', questions: [
        { text: 'Sensitivity in providing assistance/needs to the participants' },
        { text: 'Management on the entire activities' },
        { text: 'Provision of information/feedback to the participants in a clear, concise manner' }
      ]},
      { name: 'V. Facilities', questions: [
        { text: 'Overall appearance of the venue' },
        { text: 'Cleanliness and orderliness' },
        { text: 'Availability and functionality of applicable equipment' }
      ]}
    ],
    type3: [
      { name: 'I. Information Dissemination', questions: [
        { text: 'Timelines of sending invites' },
        { text: 'Adequacy of information dissemination' }
      ]},
      { name: 'II. Design of the Event', questions: [
        { text: 'Program / Order of activities' },
        { text: 'Relevance of the activities' },
        { text: 'Time allotment / pacing' }
      ]},
      { name: 'III. Outcomes of the Event', questions: [
        { text: 'Attendance of participants' },
        { text: 'Participation to activities' },
        { text: 'Timeliness and orderliness of the overall event' },
        { text: 'Execution of awarding and recognition of graduates' }
      ]},
      { name: 'IV. Secretariat', questions: [
        { text: 'Sensitivity in providing assistance to the participants' },
        { text: 'Management of the entire activities' },
        { text: 'Provision of information/feedback to the participants in a clear, concise manner' }
      ]},
      { name: 'V. Venue and other Facilities', questions: [
        { text: 'Overall appearance of the venue' },
        { text: 'Cleanliness and orderliness' },
        { text: 'Comfortability of room temperature and ventilation' },
        { text: 'Functionality and quality of audio-visual equipment' },
        { text: 'Suitability of the venue for the number of participants/guests' }
      ]},
      { name: 'VI. Food (For Students, Guests, Faculty and Working Committee)', questions: [
        { text: 'Quality of foods and beverages' },
        { text: 'Food and beverages presentation/setup' },
        { text: 'Timeliness in the delivery of food' },
        { text: 'Quality of services provided' },
        { text: 'Sufficiency of foods' },
        { text: 'Quantity/Serving of food provided' }
      ]},
      { name: 'VII. Resource Speaker', questions: [
        { text: 'Methods/strategy employed' },
        { text: 'Mastery of the subject matter' },
        { text: 'Ability to draw and maintain interest and participation' },
        { text: 'Relevance and applicability of the topic/content discussed' }
      ]},
      { name: 'VIII. Traffic Management', questions: [
        { text: 'Traffic control management' },
        { text: 'Clarity of signs and instruction' },
        { text: 'Traffic capacity and safety' }
      ]}
    ],
    type4: [
      { name: 'I. Information Dissemination', questions: [
        { text: 'Timeliness of sending invites' },
        { text: 'Adequacy of information dissemination' }
      ]},
      { name: 'II. Design of the Event', questions: [
        { text: 'Program / Order of activities' },
        { text: 'Relevance of the activities' },
        { text: 'Time allotment / pacing' }
      ]},
      { name: 'III. Outcomes of the Event', questions: [
        { text: 'Attendance of participants' },
        { text: 'Participation to activities' },
        { text: 'Interaction' },
        { text: 'Teamwork' }
      ]},
      { name: 'IV. Secretariat', questions: [
        { text: 'Sensitivity in providing assistance/needs to the participants' },
        { text: 'Management on the entire activities' },
        { text: 'Provision of information/feedback to the participants in a clear, concise manner' }
      ]},
      { name: 'V. Facilities', questions: [
        { text: 'Overall appearance of the venue' },
        { text: 'Cleanliness and orderliness' },
        { text: 'Availability and functionality of applicable equipment' }
      ]},
      { name: 'VI. Resource Speaker', questions: [
        { text: 'Methods/strategy employed' },
        { text: 'Mastery of the subject matter' },
        { text: 'Ability to draw and maintain interest and participation' },
        { text: 'Relevancy and applicability of the topic/content discussed' }
      ]}
    ],
    type5: [
      { name: 'I. Information Dissemination', questions: [
        { text: 'Timeliness of sending invites' },
        { text: 'Adequacy of information dissemination' }
      ]},
      { name: 'II. Design of the Event', questions: [
        { text: 'Program / Order of activities' },
        { text: 'Relevance of the activities' },
        { text: 'Time allotment / pacing' }
      ]},
      { name: 'III. Outcomes of the Event', questions: [
        { text: 'Attendance of participants' },
        { text: 'Participation to activities' },
        { text: 'Interaction' },
        { text: 'Teamwork' }
      ]},
      { name: 'IV. Secretariat', questions: [
        { text: 'Sensitivity in providing assistance/needs to the participants' },
        { text: 'Management on the entire activities' },
        { text: 'Provision of information/feedback to the participants in a clear, concise manner' }
      ]},
      { name: 'V. Facilities', questions: [
        { text: 'Overall appearance of the venue' },
        { text: 'Cleanliness and orderliness' },
        { text: 'Availability and functionality of applicable equipment' }
      ]},
      { name: 'VI. Food', questions: [
        { text: 'Quality of food and beverages' },
        { text: 'Food and beverages presentation/setup' },
        { text: 'Timelines of delivery of food' },
        { text: 'Quality of service provided' },
        { text: 'Sufficiency of foods' },
        { text: 'Quantity/Serving of food provided' }
      ]}
    ]
  };
  return templates[form.form_type] || [];
});

const templateComments = computed(() => {
  if (!form.form_type) return [];
  const comments = {
    type1: [
      { text: 'VIII. Positive Comments' },
      { text: 'IX. Suggestions/Recommendations for Improvement' }
    ],
    type2: [
      { text: 'VI. Positive Comments' },
      { text: 'VII. Suggestions/Recommendations for Improvement' }
    ],
    type3: [
      { text: 'IX. What went well?' },
      { text: 'X. What went not-so-well?' },
      { text: 'XI. What should we change for the next time we hold this event?' },
      { text: 'XII. Any recommendations for improvement?' }
    ],
    type4: [
      { text: 'VII. Positive Comments' },
      { text: 'VIII. Suggestions/Recommendations for Improvement' }
    ],
    type5: [
      { text: 'VII. Positive Comments' },
      { text: 'VIII. Suggestions/Recommendations for Improvement' }
    ]
  };
  return comments[form.form_type] || [];
});

function submit() {
  console.log('Submitting form:', form.data());
  
  if (!form.form_type) {
    alert('Please select a form type.');
    return;
  }
  
  if (!form.evaluation_request_id) {
    alert('No evaluation request selected.');
    return;
  }
  
  form.post('/admin/evaluations', {
    onSuccess: () => {
      console.log('Form submitted successfully');
    },
    onError: (errors) => {
      console.error('Form errors:', errors);
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
  </style>