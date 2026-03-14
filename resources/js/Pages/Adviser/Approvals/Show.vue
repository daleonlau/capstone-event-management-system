<template>
  <OrganizationUserLayout>
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Review Event</h1>
          <p class="text-gray-500 mt-1">{{ event.event_name }}</p>
        </div>
        <div class="flex gap-2">
          <Link href="/adviser/approvals" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to List
          </Link>
        </div>
      </div>

      <!-- Event Details Card -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-emerald-500 to-emerald-700"></div>
        <div class="px-8 pb-8">
          <div class="flex items-end -mt-12 mb-6">
            <div class="w-24 h-24 bg-white rounded-2xl shadow-lg p-1">
              <div class="w-full h-full bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center">
                <span class="text-white text-3xl font-bold">{{ event.event_name?.charAt(0) }}</span>
              </div>
            </div>
            <div class="ml-6">
              <h2 class="text-2xl font-bold text-gray-800">{{ event.event_name }}</h2>
              <p class="text-gray-500">Submitted by {{ event.created_by }} • {{ formatDate(event.created_at) }}</p>
            </div>
          </div>

          <!-- Quick Info Grid -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">Event Type</p>
              <p class="text-lg font-semibold text-gray-800">{{ event.event_type?.name }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">Date Range</p>
              <p class="text-lg font-semibold text-gray-800">{{ formatDateRange(event.event_date_start, event.event_date_end) }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">Payment</p>
              <p class="text-lg font-semibold" :class="event.payment === 'Payment' ? 'text-emerald-600' : 'text-gray-800'">
                {{ event.payment === 'Payment' ? `₱${event.event_fee}` : 'Free Event' }}
              </p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">Status</p>
              <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">
                Pending Review
              </span>
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
              <span v-for="dept in event.department_names" :key="dept" 
                    class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm">
                {{ dept }}
              </span>
              <p v-if="!event.department_names?.length" class="text-gray-400 text-sm">No departments selected</p>
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
              <span v-for="course in event.course_names" :key="course" 
                    class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                {{ course }}
              </span>
              <p v-if="!event.course_names?.length" class="text-gray-400 text-sm">No courses selected</p>
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
              <span v-for="year in event.year_levels" :key="year" 
                    class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">
                {{ year }}
              </span>
              <p v-if="!event.year_levels?.length" class="text-gray-400 text-sm">No year levels selected</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Signed Document -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Signed Document</h2>
        <div v-if="document_url" class="border rounded-xl overflow-hidden">
          <iframe v-if="isPDF" :src="document_url" class="w-full h-[500px]"></iframe>
          <div v-else class="p-8 text-center bg-gray-50">
            <img :src="document_url" alt="Signed Document" class="max-w-full max-h-96 mx-auto rounded-lg shadow-lg" />
          </div>
          <div class="p-4 bg-gray-50 border-t flex justify-end">
            <a :href="document_url" download
               class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              Download Document
            </a>
          </div>
        </div>
        <p v-else class="text-gray-500">No document uploaded.</p>
      </div>

      <!-- Approval Actions -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Approval Decision</h2>
        
        <!-- Rejection Form -->
        <div v-if="action === 'reject'" class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason <span class="text-red-500">*</span></label>
          <textarea
            v-model="rejectionReason"
            rows="4"
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500"
            placeholder="Please provide a reason for rejection..."
          ></textarea>
          <p v-if="form.errors.rejection_reason" class="mt-1 text-sm text-red-600">{{ form.errors.rejection_reason }}</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4">
          <button
            @click="action = 'approve'"
            class="flex-1 py-3 rounded-xl font-medium transition"
            :class="action === 'approve' 
              ? 'bg-emerald-600 text-white shadow-lg' 
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
          >
            <div class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Approve Event
            </div>
          </button>
          
          <button
            @click="action = 'reject'"
            class="flex-1 py-3 rounded-xl font-medium transition"
            :class="action === 'reject' 
              ? 'bg-red-600 text-white shadow-lg' 
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
          >
            <div class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Reject Event
            </div>
          </button>
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex justify-end">
          <button
            @click="submitDecision"
            :disabled="!canSubmit"
            class="px-8 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          >
            <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ form.processing ? 'Submitting...' : 'Submit Decision' }}
          </button>
        </div>
      </div>

      <!-- Confirmation Modal - FIXED with blurry backdrop matching Show.vue -->
      <Teleport to="body">
        <Transition
          enter-active-class="transition duration-300 ease-out"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="transition duration-200 ease-in"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div v-if="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto">
            <!-- Blurry backdrop - matching Show.vue -->
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="showConfirmModal = false"></div>
            
            <!-- Modal container - matching Show.vue style -->
            <div class="flex min-h-full items-center justify-center p-4">
              <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
                <!-- Header with gradient based on action -->
                <div class="bg-gradient-to-r px-6 py-4" 
                     :class="action === 'approve' ? 'from-emerald-600 to-emerald-700' : 'from-red-600 to-red-700'">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg v-if="action === 'approve'" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg v-else class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </div>
                      <h3 class="text-xl font-semibold text-white">
                        {{ action === 'approve' ? 'Approve Event' : 'Reject Event' }}
                      </h3>
                    </div>
                    <button @click="showConfirmModal = false" class="text-white/80 hover:text-white transition-colors">
                      <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-4">
                  <!-- Warning/Info Alert -->
                  <div class="rounded-lg p-4 mb-4" :class="action === 'approve' ? 'bg-emerald-50' : 'bg-red-50'">
                    <div class="flex">
                      <div class="flex-shrink-0">
                        <svg v-if="action === 'approve'" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg v-else class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                      </div>
                      <div class="ml-3">
                        <p class="text-sm" :class="action === 'approve' ? 'text-emerald-700' : 'text-red-700'">
                          {{ action === 'approve' 
                            ? 'Are you sure you want to approve this event? This will make it available for collection and automatically add all eligible students to the event.'
                            : 'Are you sure you want to reject this event? This action cannot be undone.' }}
                        </p>
                      </div>
                    </div>
                  </div>

                  <!-- Event Summary Card -->
                  <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <div class="flex items-center gap-3 mb-3">
                      <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white text-lg font-bold">{{ event.event_name?.charAt(0) }}</span>
                      </div>
                      <div>
                        <h4 class="font-semibold text-gray-900">{{ event.event_name }}</h4>
                        <p class="text-sm text-gray-500">{{ event.event_type?.name }}</p>
                      </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                      <div>
                        <span class="text-gray-500">Date:</span>
                        <span class="ml-1 font-medium text-gray-900">{{ formatDate(event.event_date_start) }}</span>
                      </div>
                      <div>
                        <span class="text-gray-500">Students:</span>
                        <span class="ml-1 font-medium text-gray-900">{{ event.eligible_students_count || 0 }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Rejection Reason Display (if rejecting) -->
                  <div v-if="action === 'reject'" class="bg-red-50 rounded-xl p-4">
                    <p class="text-sm font-medium text-red-800 mb-1">Rejection Reason:</p>
                    <p class="text-sm text-red-700">{{ rejectionReason }}</p>
                  </div>
                </div>

                <!-- Modal Footer - matching Show.vue -->
                <div class="flex justify-end gap-3 bg-gray-50 px-6 py-4">
                  <button
                    @click="showConfirmModal = false"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                  >
                    Cancel
                  </button>
                  <button
                    @click="confirmAction"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-white focus:outline-none focus:ring-2 disabled:opacity-50"
                    :class="action === 'approve' 
                      ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800' 
                      : 'bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800'"
                  >
                    {{ action === 'approve' ? 'Confirm Approval' : 'Confirm Rejection' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </Transition>
      </Teleport>
    </div>
  </OrganizationUserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';

const props = defineProps({
  event: {
    type: Object,
    required: true
  },
  document_url: {
    type: String,
    default: null
  },
  departments: {
    type: Array,
    default: () => []
  },
  courses: {
    type: Array,
    default: () => []
  }
});

const action = ref('');
const rejectionReason = ref('');
const showConfirmModal = ref(false);

const form = useForm({
  rejection_reason: ''
});

const isPDF = computed(() => {
  return props.document_url?.toLowerCase().endsWith('.pdf');
});

const canSubmit = computed(() => {
  if (action.value === 'approve') return true;
  if (action.value === 'reject') return rejectionReason.value.trim().length > 0;
  return false;
});

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

function formatDateRange(start, end) {
  const startDate = new Date(start).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
  const endDate = new Date(end).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  return `${startDate} - ${endDate}`;
}

function submitDecision() {
  if (!action.value) {
    alert('Please select an action (Approve or Reject)');
    return;
  }
  
  if (action.value === 'reject' && !rejectionReason.value) {
    alert('Please provide a reason for rejection');
    return;
  }
  
  showConfirmModal.value = true;
}

function confirmAction() {
  if (action.value === 'approve') {
    form.post(`/adviser/approvals/${props.event.id}/approve`, {
      onSuccess: () => {
        router.visit('/adviser/approvals');
      }
    });
  } else {
    form.rejection_reason = rejectionReason.value;
    form.post(`/adviser/approvals/${props.event.id}/reject`, {
      onSuccess: () => {
        router.visit('/adviser/approvals');
      }
    });
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