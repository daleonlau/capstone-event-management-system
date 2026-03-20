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

      <!-- Already Submitted State -->
      <div v-else-if="alreadySubmitted" class="bg-yellow-50 border-l-4 border-yellow-500 rounded-xl p-6 text-center">
        <svg class="w-16 h-16 mx-auto text-yellow-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-lg font-medium text-yellow-800 mb-2">Already Submitted</h3>
        <p class="text-yellow-700">You have already submitted your evaluation for this event.</p>
        <p class="text-sm text-yellow-600 mt-2">Thank you for your feedback!</p>
      </div>

      <!-- Success State (after submission) -->
      <div v-else-if="submitted" class="bg-green-50 border-l-4 border-green-500 rounded-xl p-6 text-center">
        <svg class="w-16 h-16 mx-auto text-green-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-lg font-medium text-green-800 mb-2">Thank You!</h3>
        <p class="text-green-700">Your evaluation has been submitted successfully.</p>
        <p class="text-sm text-green-600 mt-2">Redirecting to thank you page...</p>
      </div>

      <!-- Main Form -->
      <div v-else>
        <!-- Header with Event Details -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
          <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-6">
            <h1 class="text-2xl font-bold text-white">{{ evaluation.title }}</h1>
            <p class="text-emerald-100 mt-1">{{ evaluation.form_number }} | {{ evaluation.revision }} | {{ evaluation.date_effectivity }}</p>
          </div>
          
          <!-- Event Basic Information -->
          <div class="p-6 border-b border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <p class="text-sm text-gray-500">Title of Event</p>
                <p class="font-medium text-gray-800">{{ basicInfo.title || evaluation.event.event_name }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Inclusive Date</p>
                <p class="font-medium text-gray-800">{{ formatDate(basicInfo.activity_date) }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Venue</p>
                <p class="font-medium text-gray-800">{{ basicInfo.venue || 'Not specified' }}</p>
              </div>
            </div>
            <div v-if="basicInfo.speaker_name" class="mt-3">
              <p class="text-sm text-gray-500">Resource Speaker</p>
              <p class="font-medium text-gray-800">{{ basicInfo.speaker_name }}</p>
            </div>
            <div v-if="basicInfo.topics && basicInfo.topics.length > 0" class="mt-3">
              <p class="text-sm text-gray-500">Topics</p>
              <div class="flex flex-wrap gap-2 mt-1">
                <span v-for="topic in basicInfo.topics" :key="topic" 
                      class="px-2 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm">
                  {{ topic }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Student Verification Section -->
        <div v-if="!verified" class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
          <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-8 py-4">
            <h2 class="text-xl font-bold text-white">Student Verification</h2>
            <p class="text-blue-100 text-sm mt-1">Please enter your Student ID to continue</p>
          </div>
          
          <div class="p-6">
            <div class="max-w-md mx-auto">
              <label class="block text-sm font-medium text-gray-700 mb-2">Student ID *</label>
              <input
                v-model="studentIdInput"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                placeholder="Enter your Student ID (e.g., CTHM-2024-0001)"
                @keyup.enter="verifyStudentId"
                :disabled="verifying"
              />
              <p v-if="verificationError" class="mt-2 text-sm text-red-600">{{ verificationError }}</p>
              <button
                @click="verifyStudentId"
                :disabled="verifying || !studentIdInput"
                class="mt-4 w-full py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all disabled:opacity-50"
              >
                <span v-if="verifying">
                  <svg class="animate-spin inline-block h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Verifying...
                </span>
                <span v-else>Verify & Continue</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Evaluation Form (only shown after verification) -->
        <div v-else>
          <form @submit.prevent="submitForm">
            <!-- Part I: Profile of Respondents -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
              <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-8 py-4">
                <h2 class="text-xl font-bold text-white">Part I: Profile of Respondents</h2>
                <p class="text-blue-100 text-sm mt-1">Please provide your personal information below. Rest assured that all information shared will be treated with utmost concern and confidentiality.</p>
              </div>
              
              <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name (Optional)</label>
                    <input v-model="form.name" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                    <input v-model="form.age" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                  </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sex</label>
                    <select v-model="form.sex" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                      <option value="">Select</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                      <option value="Prefer not to say">Prefer not to say</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <select v-model="form.title_prefix" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                      <option value="">Select</option>
                      <option value="Dr.">Dr.</option>
                      <option value="Prof.">Prof.</option>
                      <option value="Mr.">Mr.</option>
                      <option value="Ms.">Ms.</option>
                      <option value="Mx.">Mx.</option>
                    </select>
                  </div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Agency/Office/Unit/College</label>
                  <input v-model="form.agency_office" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Position/Designation</label>
                  <input v-model="form.position" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type of Respondents</label>
                    <select v-model="form.respondent_type" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                      <option value="">Select</option>
                      <option value="Student">Student</option>
                      <option value="Faculty">Faculty</option>
                      <option value="Admin Personnel">Admin Personnel</option>
                      <option value="Guest">Guest</option>
                      <option value="Not Applicable">Not Applicable</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                    <select v-model="form.department" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                      <option value="">Select Department</option>
                      <option v-for="dept in departments" :key="dept.id" :value="dept.name">{{ dept.name }}</option>
                    </select>
                  </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Course *</label>
                    <select v-model="form.course" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                      <option value="">Select Course</option>
                      <option v-for="course in courses" :key="course.id" :value="course.name">{{ course.name }}</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year Level *</label>
                    <select v-model="form.year_level" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                      <option value="">Select Year Level</option>
                      <option value="1st Year">1st Year</option>
                      <option value="2nd Year">2nd Year</option>
                      <option value="3rd Year">3rd Year</option>
                      <option value="4th Year">4th Year</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Part II: Event Evaluation -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
              <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-8 py-4">
                <h2 class="text-xl font-bold text-white">Part II: Event Evaluation</h2>
                <p class="text-purple-100 text-sm mt-1">Please rate the event based on the following criteria with 5 as the highest and 1 as the lowest.</p>
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
                  <h3 class="font-bold text-gray-800 text-lg mb-4 pb-2 border-b border-gray-200">{{ category.name }}</h3>
                  <div class="space-y-4">
                    <div v-for="question in category.questions" :key="question.id" class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 p-3 hover:bg-gray-50 rounded-lg">
                      <p class="text-gray-700 md:w-2/3">{{ question.text }}</p>
                      <div class="flex gap-2 md:gap-3">
                        <label v-for="rating in [1,2,3,4,5]" :key="rating" class="flex flex-col items-center cursor-pointer">
                          <input type="radio" :name="`q_${question.id}`" :value="rating" v-model="likertResponses[question.id]" class="w-4 h-4 text-emerald-600">
                          <span class="text-xs text-gray-500 mt-1">{{ rating }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Speaker Section (if applicable) -->
            <div v-if="hasSpeaker" class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
              <div class="bg-gradient-to-r from-orange-500 to-red-500 px-8 py-4">
                <h2 class="text-xl font-bold text-white">Resource Speaker</h2>
              </div>
              <div class="p-6 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Topic</label>
                  <input v-model="form.speaker_topic" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Name of Speaker</label>
                  <input v-model="form.speaker_name" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
              </div>
            </div>

            <!-- Comment Sections -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
              <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-8 py-4">
                <h2 class="text-xl font-bold text-white">Feedback and Comments</h2>
              </div>
              <div class="p-6 space-y-6">
                <div v-for="comment in evaluation.comments" :key="comment.id">
                  <label class="block text-sm font-medium text-gray-700 mb-2">{{ comment.text }}</label>
                  <textarea v-model="commentResponses[comment.id]" rows="4" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500" :placeholder="`Enter your ${comment.text.toLowerCase()}...`"></textarea>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
              <button type="submit" :disabled="submitting" class="px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-md hover:shadow-lg disabled:opacity-50">
                <span v-if="submitting">
                  <svg class="animate-spin inline-block h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Submitting...
                </span>
                <span v-else>Submit Evaluation</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  evaluation: {
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
  yearLevels: {
    type: Array,
    default: () => []
  }
});

// Cancel token for aborting requests
const cancelTokenSource = ref(null);

const loading = ref(false);
const error = ref(null);
const verified = ref(false);
const verifying = ref(false);
const verificationError = ref(null);
const alreadySubmitted = ref(false);
const submitting = ref(false);
const submitted = ref(false);
const studentIdInput = ref('');
const studentData = ref(null);

// Form data
const form = reactive({
  student_id: '',
  email: '',
  name: '',
  age: '',
  sex: '',
  agency_office: '',
  position: '',
  respondent_type: '',
  title_prefix: '',
  department: '',
  course: '',
  year_level: '',
  speaker_topic: '',
  speaker_name: ''
});

const likertResponses = reactive({});
const commentResponses = reactive({});

// Computed properties
const basicInfo = computed(() => props.evaluation.customizations || {});
const hasSpeaker = computed(() => {
  const type = props.evaluation.form_type;
  return type === 'type1' || type === 'type3' || type === 'type4';
});

function formatDate(date) {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

async function verifyStudentId() {
  // Don't verify if already submitted
  if (submitted.value) return;
  
  if (!studentIdInput.value.trim()) {
    verificationError.value = 'Please enter your Student ID';
    return;
  }

  // Cancel any pending request
  if (cancelTokenSource.value) {
    cancelTokenSource.value.cancel('Operation canceled by new request.');
  }
  
  cancelTokenSource.value = axios.CancelToken.source();

  verifying.value = true;
  verificationError.value = null;

  try {
    const response = await axios.post(`/evaluations/${props.evaluation.id}/verify`, {
      student_id: studentIdInput.value
    }, {
      cancelToken: cancelTokenSource.value.token
    });

    console.log('Verification response:', response.data);

    if (response.data.success) {
      studentData.value = response.data.student;
      form.student_id = studentIdInput.value;
      form.email = studentData.value.email;
      form.name = studentData.value.name;
      form.department = studentData.value.department;
      form.course = studentData.value.course;
      form.year_level = studentData.value.year_level;
      verified.value = true;
      verificationError.value = null;
    }
  } catch (error) {
    if (axios.isCancel(error)) {
      console.log('Request canceled:', error.message);
      return;
    }
    
    console.error('Verification error:', error);
    
    if (error.response?.status === 422) {
      if (error.response.data.errors) {
        const errors = error.response.data.errors;
        if (errors.student_id) {
          verificationError.value = errors.student_id[0];
        } else {
          verificationError.value = 'Invalid Student ID';
        }
      } else if (error.response.data.message) {
        verificationError.value = error.response.data.message;
      } else {
        verificationError.value = 'Student ID not found. Please check your ID.';
      }
    } else if (error.response?.data?.already_submitted) {
      alreadySubmitted.value = true;
    } else {
      verificationError.value = error.response?.data?.message || 'Failed to verify Student ID. Please try again.';
    }
  } finally {
    verifying.value = false;
    cancelTokenSource.value = null;
  }
}

async function submitForm() {
  // Prevent multiple submissions
  if (submitting.value || submitted.value) return;
  
  // Validate required fields
  if (!form.department) {
    alert('Please select your department');
    return;
  }
  if (!form.course) {
    alert('Please select your course');
    return;
  }
  if (!form.year_level) {
    alert('Please select your year level');
    return;
  }

  // Check if all likert questions are answered
  const allQuestions = [];
  props.evaluation.categories.forEach(category => {
    category.questions.forEach(question => {
      allQuestions.push(question.id);
    });
  });

  const missingQuestions = allQuestions.filter(qid => !likertResponses[qid]);
  if (missingQuestions.length > 0) {
    alert('Please answer all rating questions before submitting.');
    return;
  }

  submitting.value = true;

  try {
    const response = await axios.post(`/evaluations/${props.evaluation.id}`, {
      ...form,
      likert_responses: likertResponses,
      comment_responses: commentResponses
    });

    console.log('Submission response:', response.data);
    
    // Mark as submitted to prevent any further actions
    submitted.value = true;
    verified.value = false; // Hide the form
    
    // Redirect after a short delay
    setTimeout(() => {
      router.visit('/evaluations/thankyou');
    }, 2000);
    
  } catch (error) {
    console.error('Submission error:', error);
    
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      const firstError = Object.values(errors)[0];
      alert(firstError?.[0] || 'Please fill in all required fields correctly.');
    } else if (error.response?.data?.error) {
      alert(error.response.data.error);
    } else {
      alert('Failed to submit evaluation. Please try again.');
    }
    submitting.value = false;
  }
}

// Cleanup on component unmount
onBeforeUnmount(() => {
  if (cancelTokenSource.value) {
    cancelTokenSource.value.cancel('Component unmounted');
  }
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