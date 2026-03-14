<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      
      <!-- Header with Form Number -->
      <div class="bg-white rounded-t-2xl shadow-lg p-6 border-b-2 border-emerald-200">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ evaluation.title }}</h1>
            <p class="text-sm text-gray-500">{{ evaluation.form_number }}, {{ evaluation.revision }}, {{ evaluation.date_effectivity }}</p>
          </div>
          <div class="text-right">
            <p class="text-lg font-semibold text-emerald-700">{{ evaluation.event.event_name }}</p>
            <p class="text-sm text-gray-600">{{ formatDate(evaluation.event.event_date_start) }}</p>
            <p class="text-sm text-gray-600">{{ evaluation.event.venue || 'CSUCC Gymnasium' }}</p>
          </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg text-sm text-gray-600">
          <p>To help us plan events that remain relevant to you and as the basis for further improvement, please accomplish this evaluation form objectively.</p>
        </div>
      </div>

      <!-- Student Verification Alert -->
      <div v-if="!isVerified" class="bg-yellow-50 px-6 py-4 border-b border-gray-200">
        <div class="flex items-center gap-3">
          <svg class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-sm text-yellow-800">Please enter your Student ID to verify you're part of this event.</p>
        </div>
      </div>

      <!-- Student ID Verification -->
      <div class="bg-white px-6 py-4 border-b border-gray-200">
        <div class="flex items-end gap-4">
          <div class="flex-1">
            <label class="block font-medium text-gray-800 mb-2">
              Student ID <span class="text-red-500">*</span>
            </label>
            <input 
              type="text" 
              v-model="studentId"
              @keyup.enter="verifyStudent"
              class="w-full max-w-md px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
              placeholder="Enter your Student ID"
              :disabled="isVerified"
            />
          </div>
          <button 
            v-if="!isVerified"
            @click="verifyStudent"
            class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition"
            :disabled="verifying"
          >
            <span v-if="verifying">Verifying...</span>
            <span v-else>Verify</span>
          </button>
          <div v-if="isVerified" class="flex items-center gap-2 text-green-600">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>Verified</span>
          </div>
        </div>
        <p v-if="verificationError" class="mt-2 text-sm text-red-600">{{ verificationError }}</p>
      </div>

      <form v-if="isVerified" @submit.prevent="submit" class="space-y-0">
        <!-- Email -->
        <div class="bg-white px-6 py-4 border-b border-gray-200">
          <label class="block font-medium text-gray-800 mb-2">
            Email <span class="text-red-500">*</span>
          </label>
          <input 
            type="email" 
            v-model="form.email" 
            class="w-full max-w-md px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            placeholder="your.email@example.com"
            required
          />
        </div>

        <!-- Name -->
        <div class="bg-white px-6 py-4 border-b border-gray-200">
          <label class="block font-medium text-gray-800 mb-2">
            Name:
          </label>
          <input 
            type="text" 
            v-model="form.name" 
            class="w-full max-w-md px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            placeholder="Your full name (optional)"
          />
        </div>

        <!-- Department (Filtered by Event) -->
        <div class="bg-white px-6 py-4 border-b border-gray-200">
          <label class="block font-medium text-gray-800 mb-2">
            Department <span class="text-red-500">*</span>
          </label>
          <select 
            v-model="form.department" 
            class="w-full max-w-md px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            required
          >
            <option value="">Select Department</option>
            <option v-for="dept in departments" :key="dept.id" :value="dept.name">
              {{ dept.name }}
            </option>
          </select>
        </div>

        <!-- Course (Filtered by Event) -->
        <div class="bg-white px-6 py-4 border-b border-gray-200">
          <label class="block font-medium text-gray-800 mb-2">
            Course <span class="text-red-500">*</span>
          </label>
          <select 
            v-model="form.course" 
            class="w-full max-w-md px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            required
          >
            <option value="">Select Course</option>
            <option v-for="course in courses" :key="course.id" :value="course.name">
              {{ course.name }}
            </option>
          </select>
        </div>

        <!-- Year Level (Filtered by Event) -->
        <div class="bg-white px-6 py-4 border-b border-gray-200">
          <label class="block font-medium text-gray-800 mb-2">
            Year Level <span class="text-red-500">*</span>
          </label>
          <select 
            v-model="form.year_level" 
            class="w-full max-w-md px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            required
          >
            <option value="">Select Year Level</option>
            <option v-for="year in yearLevels" :key="year" :value="year">
              {{ year }}
            </option>
          </select>
        </div>

        <!-- Likert Scale Categories -->
        <div v-for="category in evaluation.categories" :key="category.id" class="bg-white px-6 py-6 border-b border-gray-200">
          <h2 class="text-lg font-bold text-gray-800 mb-4">{{ category.name }}</h2>
          
          <!-- Likert Scale Header -->
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b-2 border-gray-300">
                  <th class="py-2 text-left text-sm font-medium text-gray-600 w-1/2"></th>
                  <th class="py-2 text-center text-sm font-medium text-gray-600 w-10">1</th>
                  <th class="py-2 text-center text-sm font-medium text-gray-600 w-10">2</th>
                  <th class="py-2 text-center text-sm font-medium text-gray-600 w-10">3</th>
                  <th class="py-2 text-center text-sm font-medium text-gray-600 w-10">4</th>
                  <th class="py-2 text-center text-sm font-medium text-gray-600 w-10">5</th>
                </tr>
                <tr class="text-xs text-gray-400">
                  <th></th>
                  <th class="text-center">Poor</th>
                  <th class="text-center">Fair</th>
                  <th class="text-center">Good</th>
                  <th class="text-center">Very Good</th>
                  <th class="text-center">Excellent</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="question in category.questions" :key="question.id" class="border-b border-gray-100">
                  <td class="py-3 text-sm text-gray-700">
                    {{ question.text }}
                    <span v-if="question.required" class="text-red-500 ml-1">*</span>
                  </td>
                  <td class="py-3 text-center">
                    <input 
                      type="radio" 
                      :name="`q_${question.id}`"
                      :value="1"
                      v-model="form.likert_responses[question.id]"
                      class="w-4 h-4 text-emerald-600 focus:ring-emerald-500"
                      :required="question.required && !form.likert_responses[question.id]"
                    />
                  </td>
                  <td class="py-3 text-center">
                    <input 
                      type="radio" 
                      :name="`q_${question.id}`"
                      :value="2"
                      v-model="form.likert_responses[question.id]"
                      class="w-4 h-4 text-emerald-600 focus:ring-emerald-500"
                    />
                  </td>
                  <td class="py-3 text-center">
                    <input 
                      type="radio" 
                      :name="`q_${question.id}`"
                      :value="3"
                      v-model="form.likert_responses[question.id]"
                      class="w-4 h-4 text-emerald-600 focus:ring-emerald-500"
                    />
                  </td>
                  <td class="py-3 text-center">
                    <input 
                      type="radio" 
                      :name="`q_${question.id}`"
                      :value="4"
                      v-model="form.likert_responses[question.id]"
                      class="w-4 h-4 text-emerald-600 focus:ring-emerald-500"
                    />
                  </td>
                  <td class="py-3 text-center">
                    <input 
                      type="radio" 
                      :name="`q_${question.id}`"
                      :value="5"
                      v-model="form.likert_responses[question.id]"
                      class="w-4 h-4 text-emerald-600 focus:ring-emerald-500"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Comment Sections -->
        <div v-for="comment in evaluation.comments" :key="comment.id" class="bg-white px-6 py-6 border-b border-gray-200">
          <h2 class="text-lg font-bold text-gray-800 mb-4">{{ comment.text }}</h2>
          <textarea 
            v-model="form.comment_responses[comment.id]"
            rows="4"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            :placeholder="`Enter your ${comment.text.toLowerCase()}...`"
            :required="comment.required"
          ></textarea>
        </div>

        <!-- Submit Button -->
        <div class="bg-white px-6 py-6 rounded-b-2xl shadow-lg">
          <div class="flex justify-end">
            <button 
              type="submit"
              class="px-8 py-4 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all hover:shadow-lg font-medium flex items-center gap-2"
              :disabled="form.processing"
            >
              <svg v-if="form.processing" class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
              <span>{{ form.processing ? 'Submitting...' : 'Submit Evaluation' }}</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
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

const studentId = ref('');
const isVerified = ref(false);
const verifying = ref(false);
const verificationError = ref('');
const showDebug = ref(true); // Debug panel visible by default
const lastResponse = ref('');

const form = useForm({
  student_id: '',
  email: '',
  name: '',
  department: '',
  course: '',
  year_level: '',
  likert_responses: {},
  comment_responses: {}
});

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }).toUpperCase();
}

async function verifyStudent() {
  // Clear previous state
  verificationError.value = '';
  lastResponse.value = '';

  if (!studentId.value.trim()) {
    verificationError.value = 'Please enter your Student ID.';
    return;
  }

  verifying.value = true;

  try {
    // Show what we're sending
    lastResponse.value = `📤 SENDING REQUEST:\n` +
      `URL: POST /evaluations/verify-student\n` +
      `Event ID: ${props.evaluation.event.id}\n` +
      `Student ID: "${studentId.value}"`;

    const response = await axios.post('/evaluations/verify-student', {
      event_id: props.evaluation.event.id,
      student_id: studentId.value
    });

    // Show successful response
    lastResponse.value += `\n\n📥 RESPONSE RECEIVED:\n` +
      `Status: ${response.status}\n` +
      `Data: ${JSON.stringify(response.data, null, 2)}`;

    if (response.data.valid) {
      isVerified.value = true;
      form.student_id = studentId.value;
      if (response.data.student) {
        form.name = response.data.student.name || '';
        form.email = response.data.student.email || '';
      }
    } else {
      verificationError.value = response.data.message || 'Student ID not found in event participants.';
    }
  } catch (error) {
    // Show error details
    lastResponse.value += `\n\n❌ ERROR:\n`;
    
    if (error.response) {
      // The request was made and the server responded with a status code
      lastResponse.value += `Status: ${error.response.status}\n`;
      
      // Check if response is HTML (string starting with <!DOCTYPE)
      if (typeof error.response.data === 'string' && error.response.data.trim().startsWith('<!DOCTYPE')) {
        lastResponse.value += `⚠️ Received HTML instead of JSON!\n`;
        lastResponse.value += `This means the route might be wrong or not returning JSON.\n`;
        lastResponse.value += `First 200 chars: ${error.response.data.substring(0, 200)}...`;
        verificationError.value = 'Server configuration error. Please contact administrator.';
      } else {
        lastResponse.value += `Data: ${JSON.stringify(error.response.data, null, 2)}`;
        verificationError.value = error.response.data?.message || 'Verification failed.';
      }
    } else if (error.request) {
      // The request was made but no response was received
      lastResponse.value += `No response received from server. Network error?`;
      verificationError.value = 'Network error. Please check your connection.';
    } else {
      // Something happened in setting up the request
      lastResponse.value += `Error: ${error.message}`;
      verificationError.value = 'Request failed. Please try again.';
    }
  } finally {
    verifying.value = false;
  }
}

function submit() {
  form.post(`/evaluations/${props.evaluation.id}`);
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