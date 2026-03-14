<template>
  <OrganizationUserLayout>
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Event Details</h1>
          <p class="text-gray-500 mt-1">{{ event.event_name }}</p>
        </div>
        <div class="flex gap-2">
          <Link href="/president/events" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back
          </Link>
          <Link :href="`/president/events/${event.id}/edit`" class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit
          </Link>
          <!-- Mark as Finished Button -->
          <button 
            v-if="stats.can_be_finished"
            @click="confirmMarkFinished"
            class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Mark as Finished
          </button>
        </div>
      </div>

      <!-- Status Alert -->
      <div v-if="event.approval_status === 'pending_document'" class="bg-yellow-50 border-l-4 border-yellow-500 rounded-xl p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-yellow-700">
              <span class="font-medium">Action Required:</span> This event needs a signed document before it can be sent for adviser approval.
            </p>
          </div>
        </div>
      </div>

      <div v-if="event.approval_status === 'pending_approval'" class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-blue-700">
              <span class="font-medium">Pending Approval:</span> Document uploaded. Waiting for adviser review.
            </p>
          </div>
        </div>
      </div>

      <div v-if="event.approval_status === 'approved'" class="bg-green-50 border-l-4 border-green-500 rounded-xl p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-green-700">
              <span class="font-medium">Approved!</span> This event has been approved by the adviser.
            </p>
          </div>
        </div>
      </div>

      <div v-if="event.approval_status === 'rejected'" class="bg-red-50 border-l-4 border-red-500 rounded-xl p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-red-700">
              <span class="font-medium">Rejected:</span> {{ event.rejection_reason || 'No reason provided' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Finished Alert -->
      <div v-if="event.status === 'Finished'" class="bg-purple-50 border-l-4 border-purple-500 rounded-xl p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-purple-700">
              <span class="font-medium">Event Finished!</span> This event has been marked as finished. You can now create evaluations and generate QR codes.
            </p>
          </div>
        </div>
      </div>

      <!-- Event Header Card -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-emerald-500 to-emerald-700"></div>
        <div class="px-8 pb-8">
          <div class="flex items-end -mt-12 mb-6">
            <div class="w-24 h-24 bg-white rounded-2xl shadow-lg p-1">
              <div class="w-full h-full bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center">
                <span class="text-white text-3xl font-bold">{{ event.event_name?.charAt(0) }}</span>
              </div>
            </div>
            <div class="ml-6 flex-1">
              <h2 class="text-2xl font-bold text-gray-800">{{ event.event_name }}</h2>
              <div class="flex items-center gap-3 mt-2">
                <span :class="statusBadgeClass(event.status)" class="px-3 py-1 text-xs rounded-full">{{ event.status }}</span>
                <span :class="approvalBadgeClass(event.approval_status)" class="px-3 py-1 text-xs rounded-full">{{ formatApprovalStatus(event.approval_status) }}</span>
              </div>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">Event Type</p>
              <p class="text-lg font-semibold text-gray-800">{{ event.event_type?.name }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">Start Date</p>
              <p class="text-lg font-semibold text-gray-800">{{ formatDate(event.event_date_start) }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">End Date</p>
              <p class="text-lg font-semibold text-gray-800">{{ formatDate(event.event_date_end) }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">Event Fee</p>
              <p class="text-lg font-semibold text-gray-800">{{ event.payment === 'Payment' ? `₱${event.event_fee}` : 'Free' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Evaluation Section - Show when event is finished -->
      <div v-if="event.status === 'Finished'" class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Event Evaluation
        </h2>
        
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <h3 class="font-medium text-gray-800 mb-1">Student Feedback Form</h3>
              <p class="text-sm text-gray-600">
                {{ stats.evaluations > 0 
                  ? 'Evaluation form has been created for this event.' 
                  : 'Create an evaluation form to gather feedback from students.' }}
              </p>
            </div>
            
            <div class="flex gap-3">
              <Link
                v-if="stats.evaluations > 0"
                :href="`/president/evaluations/${stats.evaluation_id}`"
                class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition flex items-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                View Results
              </Link>
              
              <Link
                v-if="stats.evaluations === 0"
                :href="`/president/evaluations/create?event_id=${event.id}`"
                class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition flex items-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Evaluation
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Target Audience -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Target Audience</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Departments -->
          <div>
            <h3 class="font-medium text-gray-700 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              Departments
            </h3>
            <div class="flex flex-wrap gap-2">
              <template v-if="event.departments && event.departments.length > 0">
                <span 
                  v-for="deptId in event.departments" 
                  :key="deptId" 
                  class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm"
                >
                  {{ getDepartmentName(deptId) }}
                </span>
              </template>
              <p v-else class="text-gray-400 text-sm">No departments selected</p>
            </div>
          </div>

          <!-- Courses -->
          <div>
            <h3 class="font-medium text-gray-700 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              Courses
            </h3>
            <div class="flex flex-wrap gap-2">
              <template v-if="event.courses && event.courses.length > 0">
                <span 
                  v-for="courseId in event.courses" 
                  :key="courseId" 
                  class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm"
                >
                  {{ getCourseName(courseId) }}
                </span>
              </template>
              <p v-else class="text-gray-400 text-sm">No courses selected</p>
            </div>
          </div>

          <!-- Year Levels -->
          <div>
            <h3 class="font-medium text-gray-700 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
              </svg>
              Year Levels
            </h3>
            <div class="flex flex-wrap gap-2">
              <template v-if="event.year_levels && event.year_levels.length > 0">
                <span 
                  v-for="year in event.year_levels" 
                  :key="year" 
                  class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm"
                >
                  {{ year }}
                </span>
              </template>
              <p v-else class="text-gray-400 text-sm">No year levels selected</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Document Upload Section -->
      <div v-if="!event.has_document" class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Upload Signed Document</h2>
        
        <form @submit.prevent="uploadDocument" enctype="multipart/form-data" class="space-y-4">
          <div 
            class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-emerald-500 transition cursor-pointer"
            @dragover.prevent
            @drop.prevent="handleDocumentDrop"
            @click="$refs.documentInput.click()"
          >
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            
            <p class="text-gray-600 mb-2">
              <span class="font-semibold">Click to upload</span> or drag and drop
            </p>
            <p class="text-sm text-gray-500">
              {{ documentFileName || 'PDF, JPG, or PNG (Max 5MB)' }}
            </p>
          </div>

          <input
            ref="documentInput"
            type="file"
            @change="handleDocumentFile"
            accept=".pdf,.jpg,.jpeg,.png"
            class="hidden"
          />

          <div v-if="uploadForm.errors.signed_document" class="text-sm text-red-600">
            {{ uploadForm.errors.signed_document }}
          </div>

          <div class="flex justify-end">
            <button
              type="submit"
              class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition disabled:opacity-50 flex items-center gap-2"
              :disabled="uploadForm.processing || !uploadForm.signed_document"
            >
              <svg v-if="uploadForm.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ uploadForm.processing ? 'Uploading...' : 'Upload Document' }}</span>
            </button>
          </div>
        </form>

        <p class="text-sm text-gray-500 mt-4">
          After uploading the document, the event will be sent to the adviser for approval.
        </p>
      </div>

      <!-- Document Display -->
      <div v-else class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Signed Document</h2>
        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
          <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <div class="flex-1">
            <p class="text-sm font-medium text-gray-700">Document uploaded</p>
            <p class="text-xs text-gray-500">Uploaded on {{ formatDate(event.updated_at) }}</p>
          </div>
          <a :href="`/storage/${event.signed_document_path}`" target="_blank" 
             class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            View Document
          </a>
        </div>
      </div>

      <!-- Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 rounded-xl p-4">
          <p class="text-sm text-blue-600 font-semibold">Total Students</p>
          <p class="text-2xl font-bold text-blue-700">{{ stats?.total_students || 0 }}</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4">
          <p class="text-sm text-green-600 font-semibold">Paid</p>
          <p class="text-2xl font-bold text-green-700">{{ stats?.paid || 0 }}</p>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4">
          <p class="text-sm text-yellow-600 font-semibold">Pending</p>
          <p class="text-2xl font-bold text-yellow-700">{{ stats?.pending || 0 }}</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-4">
          <p class="text-sm text-purple-600 font-semibold">Evaluations</p>
          <p class="text-2xl font-bold text-purple-700">{{ stats?.evaluations || 0 }}</p>
        </div>
      </div>

      <!-- Mark as Finished Confirmation Modal -->
      <Teleport to="body">
        <div v-if="showFinishedModal" class="fixed inset-0 z-50 overflow-y-auto">
          <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="showFinishedModal = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
              <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 mb-4">
                  <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Mark Event as Finished</h3>
                <p class="text-sm text-gray-500 mb-4">
                  Are you sure you want to mark <span class="font-semibold">{{ event.event_name }}</span> as finished?
                </p>
                <p class="text-xs text-gray-400 mb-6">
                  Note: Collections can still continue after marking as finished. This will make the event available for evaluation creation and QR code generation.
                </p>
                <div class="flex justify-end gap-3">
                  <button @click="showFinishedModal = false" 
                          class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                  </button>
                  <button @click="markAsFinished" 
                          class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
                          :disabled="finishedProcessing">
                    <span v-if="finishedProcessing">Processing...</span>
                    <span v-else>Mark as Finished</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </Teleport>
    </div>
  </OrganizationUserLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
import axios from 'axios';

const props = defineProps({
  event: {
    type: Object,
    required: true
  },
  departments: {
    type: Array,
    default: () => []
  },
  courses: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({})
  }
});

// Document upload form
const uploadForm = useForm({
  signed_document: null
});
const documentFileName = ref('');
const showFinishedModal = ref(false);
const finishedProcessing = ref(false);

function handleDocumentFile(e) {
  const file = e.target.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      alert('File size must not exceed 5MB');
      return;
    }
    uploadForm.signed_document = file;
    documentFileName.value = file.name;
  }
}

function handleDocumentDrop(e) {
  const file = e.dataTransfer.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      alert('File size must not exceed 5MB');
      return;
    }
    uploadForm.signed_document = file;
    documentFileName.value = file.name;
  }
}

function uploadDocument() {
  if (!uploadForm.signed_document) {
    alert('Please select a file to upload.');
    return;
  }

  uploadForm.post(`/president/events/${props.event.id}/upload-document`, {
    forceFormData: true,
    onSuccess: () => {
      uploadForm.reset();
      documentFileName.value = '';
      router.reload();
    }
  });
}

function confirmMarkFinished() {
  showFinishedModal.value = true;
}

async function markAsFinished() {
  finishedProcessing.value = true;
  
  try {
    await axios.post(`/president/events/${props.event.id}/mark-finished`);
    showFinishedModal.value = false;
    router.reload();
  } catch (error) {
    console.error('Error marking as finished:', error);
    alert('Failed to mark event as finished. Please try again.');
  } finally {
    finishedProcessing.value = false;
  }
}

function getDepartmentName(deptId) {
  if (!props.departments || !Array.isArray(props.departments)) return `Dept ID: ${deptId}`;
  const dept = props.departments.find(d => d.id === deptId);
  return dept ? dept.name : `Dept ID: ${deptId}`;
}

function getCourseName(courseId) {
  if (!props.courses || !Array.isArray(props.courses)) return `Course ID: ${courseId}`;
  const course = props.courses.find(c => c.id === courseId);
  return course ? course.name : `Course ID: ${courseId}`;
}

function formatDate(date) {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

function formatApprovalStatus(status) {
  if (!status) return 'N/A';
  return status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
}

function statusBadgeClass(status) {
  const base = 'px-3 py-1 text-xs rounded-full font-medium';
  switch(status) {
    case 'Pending': return `${base} bg-yellow-100 text-yellow-700`;
    case 'Approved': return `${base} bg-green-100 text-green-700`;
    case 'Finished': return `${base} bg-purple-100 text-purple-700`;
    default: return `${base} bg-gray-100 text-gray-700`;
  }
}

function approvalBadgeClass(status) {
  const base = 'px-3 py-1 text-xs rounded-full font-medium';
  switch(status) {
    case 'pending_document': return `${base} bg-yellow-100 text-yellow-700`;
    case 'pending_approval': return `${base} bg-blue-100 text-blue-700`;
    case 'approved': return `${base} bg-green-100 text-green-700`;
    case 'rejected': return `${base} bg-red-100 text-red-700`;
    default: return `${base} bg-gray-100 text-gray-700`;
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