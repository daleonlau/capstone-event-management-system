<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Loading State -->
      <div v-if="loading" class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Loading evaluation form...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 border-l-4 border-red-500 rounded-xl p-6">
        <p class="text-red-700">{{ error }}</p>
      </div>

      <!-- All Dates Submitted State -->
      <div v-else-if="allDatesSubmitted" class="bg-green-50 border-l-4 border-green-500 rounded-xl p-6 text-center">
        <svg class="w-16 h-16 mx-auto text-green-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-lg font-medium text-green-800 mb-2">All Evaluations Completed!</h3>
        <p class="text-green-700">You have successfully submitted evaluations for all {{ totalDates }} days of the event.</p>
        <button 
          @click="goToThankYou"
          class="mt-4 px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all"
        >
          Go to Thank You Page
        </button>
      </div>

      <!-- Success State after submission -->
      <div v-else-if="justSubmitted" class="bg-green-50 border-l-4 border-green-500 rounded-xl p-6 text-center">
        <svg class="w-16 h-16 mx-auto text-green-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-lg font-medium text-green-800 mb-2">Evaluation Submitted!</h3>
        <p class="text-green-700">{{ justSubmittedMessage }}</p>
        <div v-if="remainingDatesCount > 0" class="mt-4">
          <p class="text-sm text-green-600 mb-3">You still have {{ remainingDatesCount }} more day(s) to evaluate:</p>
          <div class="flex flex-wrap gap-2 justify-center">
            <span v-for="date in remainingDatesList" :key="date" class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
              {{ formatDate(date) }}
            </span>
          </div>
          <button 
            @click="submitAnotherResponse"
            class="mt-4 px-6 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all"
          >
            Submit Another Response →
          </button>
        </div>
        <button 
          v-else
          @click="goToThankYou"
          class="mt-4 px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all"
        >
          Go to Thank You Page
        </button>
      </div>

      <!-- Main Form -->
      <div v-else>
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
          <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-6">
            <h1 class="text-2xl font-bold text-white">{{ evaluation.title }}</h1>
            <p class="text-emerald-100 mt-1">{{ evaluation.form_number }} | {{ evaluation.revision }} | {{ evaluation.date_effectivity }}</p>
          </div>
          
          <div class="p-6 border-b border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-500">Event</p>
                <p class="font-medium">{{ evaluation.event.event_name }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Venue</p>
                <p class="font-medium">{{ basicInfo.venue || 'Not specified' }}</p>
              </div>
            </div>
            
            <!-- Event Dates Display -->
            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
              <p class="text-sm text-gray-500 mb-2">Available Dates</p>
              <div class="flex flex-wrap gap-2">
                <span v-for="(date, idx) in allDates" :key="date" 
                      class="px-3 py-1 rounded-full text-sm"
                      :class="{
                        'bg-green-100 text-green-700': submittedDatesSet.has(date),
                        'bg-yellow-100 text-yellow-700': selectedDate === date,
                        'bg-gray-100': !submittedDatesSet.has(date) && selectedDate !== date
                      }">
                  Day {{ idx + 1 }}: {{ formatDate(date) }}
                  <span v-if="submittedDatesSet.has(date)" class="ml-1">✓</span>
                </span>
              </div>
              <div class="mt-2 text-xs text-gray-500">
                {{ submittedDatesSet.size }} of {{ totalDates }} days completed
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-green-500 rounded-full h-2" :style="{ width: (submittedDatesSet.size / totalDates * 100) + '%' }"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 1: Select Date & Verify -->
        <div v-if="!verified" class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
          <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-8 py-4">
            <h2 class="text-xl font-bold text-white">Select Date & Verify</h2>
          </div>
          
          <div class="p-6 space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Select Event Date *</label>
              <select v-model="selectedDate" @change="onDateChange" class="w-full px-4 py-3 border rounded-xl">
                <option value="">-- Select a date --</option>
                <option v-for="(date, idx) in allDates" :key="date" :value="date" :disabled="submittedDatesSet.has(date)">
                  Day {{ idx + 1 }}: {{ formatDate(date) }}
                  <span v-if="submittedDatesSet.has(date)">(Already Submitted ✓)</span>
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Student ID or Guest Name <span class="text-red-500">*</span>
              </label>
              <div class="flex gap-3">
                <input 
                  v-model="studentIdInput" 
                  type="text" 
                  class="flex-1 px-4 py-3 border rounded-xl"
                  :placeholder="selectedDate ? 'Enter Student ID or Guest Name' : 'Select a date first'"
                  :disabled="!selectedDate"
                  @keyup.enter="verifyStudentId"
                />
                <button 
                  @click="verifyStudentId" 
                  :disabled="!selectedDate || !studentIdInput || verifying"
                  class="px-6 py-3 bg-emerald-600 text-white rounded-xl disabled:opacity-50 flex items-center gap-2"
                >
                  <svg v-if="verifying" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span v-else>Verify</span>
                </button>
              </div>
              <div v-if="verificationMessage" class="mt-2 text-sm" :class="verificationMessageType === 'success' ? 'text-green-600' : 'text-red-600'">
                {{ verificationMessage }}
              </div>
              <p class="text-xs text-gray-500 mt-2">
                <strong>Students:</strong> Enter your Student ID<br>
                <strong>Guests:</strong> Enter your registered name (as added by the president)
              </p>
            </div>

            <!-- Help Text -->
            <div class="bg-blue-50 rounded-lg p-4">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm text-blue-700">
                  <p class="font-medium">How it works:</p>
                  <ul class="list-disc list-inside mt-1 space-y-1">
                    <li>Select a date that you want to evaluate (already submitted dates are disabled)</li>
                    <li>Enter your Student ID or Guest Name and click Verify</li>
                    <li>After successful verification, fill out the evaluation form for that specific day</li>
                    <li>After submission, you can click "Submit Another Response" to evaluate another day</li>
                    <li>You can submit separate evaluations for each day of the event (one per day)</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 2: Evaluation Form -->
        <div v-else>
          <form @submit.prevent="submitForm">
            <div class="bg-green-50 rounded-xl p-4 mb-6">
              <p class="font-medium text-green-800">Evaluating: {{ formatDate(selectedDate) }}</p>
              <button type="button" @click="goBackToDateSelection" class="text-sm text-green-600 underline">Change Date</button>
            </div>

            <!-- Profile Section -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
              <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-8 py-4">
                <h2 class="text-xl font-bold text-white">Part I: Profile of Respondents</h2>
              </div>
              <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name (Optional)</label>
                    <input v-model="form.name" type="text" class="w-full px-4 py-2 border rounded-lg">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                    <input v-model="form.age" type="text" class="w-full px-4 py-2 border rounded-lg">
                  </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sex</label>
                    <select v-model="form.sex" class="w-full px-4 py-2 border rounded-lg">
                      <option value="">Select</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                      <option value="Prefer not to say">Prefer not to say</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <select v-model="form.title_prefix" class="w-full px-4 py-2 border rounded-lg">
                      <option value="">Select</option>
                      <option value="Dr.">Dr.</option>
                      <option value="Prof.">Prof.</option>
                      <option value="Mr.">Mr.</option>
                      <option value="Ms.">Ms.</option>
                    </select>
                  </div>
                </div>
                
                <!-- Agency/Office -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Agency/Office/Unit/College</label>
                  <input 
                    v-model="form.agency_office" 
                    type="text" 
                    class="w-full px-4 py-2 border rounded-lg bg-gray-50"
                    readonly
                    :placeholder="isGuest ? 'Auto-filled from guest registration' : 'Auto-filled from student record'"
                  />
                  <p class="text-xs text-gray-400 mt-1">
                    {{ isGuest ? 'This information is from your guest registration' : 'This is your department from student record' }}
                  </p>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Position/Designation</label>
                  <input v-model="form.position" type="text" class="w-full px-4 py-2 border rounded-lg">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type of Respondents</label>
                    <select v-model="form.respondent_type" class="w-full px-4 py-2 border rounded-lg">
                      <option value="">Select</option>
                      <option value="Student">Student</option>
                      <option value="Faculty">Faculty</option>
                      <option value="Guest">Guest</option>
                      <option value="Admin Personnel">Admin Personnel</option>
                      <option value="Not Applicable">Not Applicable</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Evaluation Section -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
              <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-8 py-4">
                <h2 class="text-xl font-bold text-white">Part II: Event Evaluation - Day {{ getDateIndex(selectedDate) }}</h2>
                <div class="flex justify-between mt-2 text-xs text-purple-100">
                  <span>1 - Very Dissatisfied</span>
                  <span>2 - Dissatisfied</span>
                  <span>3 - Neutral</span>
                  <span>4 - Satisfied</span>
                  <span>5 - Very Satisfied</span>
                </div>
              </div>
              
              <div class="p-6 space-y-8">
                <div v-for="category in evaluation.categories" :key="category.id">
                  <h3 class="font-bold text-gray-800 text-lg mb-4">{{ category.name }}</h3>
                  <div v-for="question in category.questions" :key="question.id" class="flex justify-between items-center p-3 hover:bg-gray-50">
                    <p class="text-gray-700 w-2/3">{{ question.text }}</p>
                    <div class="flex gap-3">
                      <label v-for="rating in [1,2,3,4,5]" :key="rating" class="flex flex-col items-center">
                        <input type="radio" :name="`q_${question.id}_${selectedDate}`" :value="rating" v-model="likertResponses[question.id]" class="w-4 h-4">
                        <span class="text-xs">{{ rating }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Speaker Section -->
            <div v-if="hasSpeaker" class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
              <div class="bg-gradient-to-r from-orange-500 to-red-500 px-8 py-4">
                <h2 class="text-xl font-bold text-white">Resource Speaker</h2>
              </div>
              <div class="p-6 space-y-4">
                <div><input v-model="form.speaker_topic" placeholder="Topic" class="w-full px-4 py-2 border rounded-lg" /></div>
                <div><input v-model="form.speaker_name" placeholder="Name of Speaker" class="w-full px-4 py-2 border rounded-lg" /></div>
              </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
              <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-8 py-4">
                <h2 class="text-xl font-bold text-white">Comments</h2>
              </div>
              <div class="p-6 space-y-6">
                <div v-for="comment in evaluation.comments" :key="comment.id">
                  <label class="block text-sm font-medium mb-2">{{ comment.text }}</label>
                  <textarea v-model="commentResponses[comment.id]" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-between">
              <button type="button" @click="goBackToDateSelection" class="px-6 py-3 bg-gray-200 rounded-xl">← Back</button>
              <button type="submit" :disabled="submitting" class="px-8 py-3 bg-emerald-600 text-white rounded-xl">
                {{ submitting ? 'Submitting...' : 'Submit for Day ' + getDateIndex(selectedDate) }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onBeforeUnmount, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  evaluation: { type: Object, required: true },
  departments: { type: Array, default: () => [] },
  courses: { type: Array, default: () => [] }
});

// State
const cancelTokenSource = ref(null);
const loading = ref(false);
const error = ref(null);
const verified = ref(false);
const verifying = ref(false);
const verificationMessage = ref('');
const verificationMessageType = ref('');
const submitting = ref(false);
const justSubmitted = ref(false);
const justSubmittedMessage = ref('');
const studentIdInput = ref('');
const selectedDate = ref('');
const allDates = ref([]);
const submittedDatesSet = ref(new Set());
const isGuest = ref(false);
let autoRedirectTimer = null;

// Form data
const form = reactive({
  student_id: '', email: '', name: '', age: '', sex: '', agency_office: '', position: '',
  respondent_type: '', title_prefix: '', speaker_topic: '', speaker_name: '', event_date: ''
});

const likertResponses = reactive({});
const commentResponses = reactive({});

// Computed
const basicInfo = computed(() => props.evaluation.customizations || {});
const hasSpeaker = computed(() => {
  const type = props.evaluation.form_type;
  return type === 'type1' || type === 'type3' || type === 'type4';
});
const totalDates = computed(() => allDates.value.length);
const remainingDatesList = computed(() => allDates.value.filter(date => !submittedDatesSet.value.has(date)));
const remainingDatesCount = computed(() => remainingDatesList.value.length);
const allDatesSubmitted = computed(() => totalDates.value > 0 && submittedDatesSet.value.size === totalDates.value);

function formatDate(date) {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric', month: 'long', day: 'numeric'
  });
}

function getDateIndex(date) {
  return allDates.value.findIndex(d => d === date) + 1;
}

function onDateChange() {
  if (verified.value) {
    verified.value = false;
    verificationMessage.value = '';
  }
  verificationMessage.value = '';
  verificationMessageType.value = '';
}

function goBackToDateSelection() {
  verified.value = false;
  selectedDate.value = '';
  verificationMessage.value = '';
  verificationMessageType.value = '';
  isGuest.value = false;
  Object.keys(likertResponses).forEach(key => delete likertResponses[key]);
  Object.keys(commentResponses).forEach(key => delete commentResponses[key]);
}

function submitAnotherResponse() {
  justSubmitted.value = false;
  goBackToDateSelection();
  studentIdInput.value = '';
  form.student_id = '';
  form.email = '';
  form.name = '';
  form.agency_office = '';
  form.event_date = '';
}

function goToThankYou() {
  router.visit('/evaluations/thankyou');
}

async function loadAvailableDates() {
  allDates.value = props.evaluation.event_dates || [];
  console.log('Dates loaded:', allDates.value);
}

async function loadSubmittedDates() {
  const studentId = form.student_id;
  if (!studentId) return;
  
  try {
    const response = await axios.get(`/evaluations/${props.evaluation.id}/student-submissions`, {
      params: { student_id: studentId }
    });
    const submittedArray = response.data.submitted_dates || [];
    submittedDatesSet.value = new Set(submittedArray);
    console.log('Submitted dates loaded:', submittedArray);
  } catch (err) {
    console.error('Failed to load submitted dates:', err);
  }
}

async function verifyStudentId() {
  // Check if date is selected
  if (!selectedDate.value) {
    verificationMessage.value = 'Please select a date first.';
    verificationMessageType.value = 'error';
    return;
  }
  
  // Check if ID is entered
  if (!studentIdInput.value.trim()) {
    verificationMessage.value = 'Please enter your Student ID or Guest Name';
    verificationMessageType.value = 'error';
    return;
  }

  if (cancelTokenSource.value) {
    cancelTokenSource.value.cancel('Operation canceled.');
  }
  
  cancelTokenSource.value = axios.CancelToken.source();
  verifying.value = true;
  verificationMessage.value = 'Verifying...';
  verificationMessageType.value = 'info';

  try {
    const response = await axios.post(`/evaluations/${props.evaluation.id}/verify`, {
      student_id: studentIdInput.value,
      event_date: selectedDate.value
    }, {
      cancelToken: cancelTokenSource.value.token
    });

    if (response.data.success) {
      const student = response.data.student;
      isGuest.value = response.data.is_guest || false;
      
      form.student_id = student.student_id;
      form.email = student.email;
      form.name = student.name;
      form.agency_office = student.agency_office || '';
      form.event_date = selectedDate.value;
      
      verified.value = true;
      verificationMessage.value = response.data.message || 'Verification successful!';
      verificationMessageType.value = 'success';
      
      await loadSubmittedDates();
      
      setTimeout(() => {
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
      }, 500);
    }
  } catch (error) {
    if (axios.isCancel(error)) return;
    
    // SILENTLY HANDLE 422 ERRORS - no console logging
    if (error.response?.status === 422) {
      const errorMessage = error.response.data.message;
      
      // Just update the UI message
      if (errorMessage && (errorMessage.includes('already submitted') || errorMessage.includes('already have submitted'))) {
        verificationMessage.value = errorMessage;
        verificationMessageType.value = 'error';
        submittedDatesSet.value.add(selectedDate.value);
        allDates.value = [...allDates.value];
      } else {
        verificationMessage.value = errorMessage || 'Invalid ID or Name.';
        verificationMessageType.value = 'error';
      }
      // Exit silently - no console error
      return;
    }
    
    // Only log non-422 errors
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors;
      const firstError = Object.values(errors)[0];
      verificationMessage.value = Array.isArray(firstError) ? firstError[0] : firstError;
      verificationMessageType.value = 'error';
      console.error('Validation error:', errors);
    } else {
      verificationMessage.value = 'Verification failed. Please try again.';
      verificationMessageType.value = 'error';
      console.error('Verification error:', error);
    }
  } finally {
    verifying.value = false;
    cancelTokenSource.value = null;
  }
}

async function submitForm() {
  if (submitting.value) return;
  
  if (!form.event_date) {
    alert('Please select a date.');
    goBackToDateSelection();
    return;
  }
  
  if (submittedDatesSet.value.has(form.event_date)) {
    alert('You already submitted for this date.');
    goBackToDateSelection();
    return;
  }

  const allQuestions = [];
  props.evaluation.categories.forEach(category => {
    category.questions.forEach(question => {
      allQuestions.push(question.id);
    });
  });

  const missing = allQuestions.filter(qid => !likertResponses[qid]);
  if (missing.length > 0) {
    alert(`Please answer all ${missing.length} rating questions.`);
    return;
  }

  submitting.value = true;

  try {
    const response = await axios.post(`/evaluations/${props.evaluation.id}`, {
      student_id: form.student_id,
      email: form.email,
      name: form.name,
      age: form.age,
      sex: form.sex,
      agency_office: form.agency_office,
      position: form.position,
      respondent_type: form.respondent_type,
      title_prefix: form.title_prefix,
      event_date: form.event_date,
      speaker_topic: form.speaker_topic,
      speaker_name: form.speaker_name,
      likert_responses: likertResponses,
      comment_responses: commentResponses
    });

    if (response.data.success) {
      submittedDatesSet.value.add(form.event_date);
      
      const remaining = allDates.value.filter(d => !submittedDatesSet.value.has(d));
      
      if (remaining.length > 0) {
        justSubmittedMessage.value = `✅ Submitted for ${formatDate(form.event_date)}! ${remaining.length} day(s) remaining.`;
        justSubmitted.value = true;
        verified.value = false;
        selectedDate.value = '';
        studentIdInput.value = '';
        form.event_date = '';
        isGuest.value = false;
        Object.keys(likertResponses).forEach(k => delete likertResponses[k]);
        Object.keys(commentResponses).forEach(k => delete commentResponses[k]);
        submitting.value = false;
      } else {
        justSubmittedMessage.value = `✅ Submitted for ${formatDate(form.event_date)}! All days completed!`;
        justSubmitted.value = true;
        
        if (autoRedirectTimer) clearTimeout(autoRedirectTimer);
        autoRedirectTimer = setTimeout(() => {
          router.visit('/evaluations/thankyou');
        }, 3000);
        
        submitting.value = false;
      }
    }
  } catch (error) {
    console.error('Submission error:', error);
    alert(error.response?.data?.error || 'Submission failed. Please try again.');
    submitting.value = false;
  }
}

onBeforeUnmount(() => {
  if (autoRedirectTimer) {
    clearTimeout(autoRedirectTimer);
  }
  if (cancelTokenSource.value) {
    cancelTokenSource.value.cancel('Component unmounted');
  }
});

onMounted(() => {
  loadAvailableDates();
});
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