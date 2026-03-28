<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="mx-auto h-12 w-12 rounded-full bg-gradient-to-r from-emerald-500 to-teal-600 flex items-center justify-center">
          <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          Student Verification
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Please select the date and enter your Student ID to access the evaluation form
        </p>
      </div>

      <!-- Event Info Card -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="border-b border-gray-200 pb-4 mb-4">
          <h3 class="text-lg font-semibold text-gray-800">{{ evaluation.title }}</h3>
          <p class="text-sm text-gray-500">{{ evaluation.event.event_name }}</p>
        </div>
        
        <!-- Event Dates Display -->
        <div class="mb-4">
          <p class="text-sm font-medium text-gray-700 mb-2">Event Dates:</p>
          <div class="flex flex-wrap gap-2">
            <span v-for="(date, idx) in eventDates" :key="date" 
                  class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
              Day {{ idx + 1 }}: {{ formatDate(date) }}
            </span>
          </div>
        </div>
        
        <div class="space-y-2 text-sm">
          <div class="flex">
            <span class="w-24 text-gray-500">Form Number:</span>
            <span class="text-gray-700">{{ evaluation.form_number }}</span>
          </div>
          <div class="flex">
            <span class="w-24 text-gray-500">Revision:</span>
            <span class="text-gray-700">{{ evaluation.revision }}</span>
          </div>
          <div class="flex">
            <span class="w-24 text-gray-500">Effectivity:</span>
            <span class="text-gray-700">{{ evaluation.date_effectivity }}</span>
          </div>
        </div>
      </div>

      <!-- Verification Form with Date Selection -->
      <form @submit.prevent="verifyStudent" class="mt-8 space-y-6">
        <!-- Date Selection FIRST -->
        <div>
          <label for="event_date" class="block text-sm font-medium text-gray-700 mb-2">
            Select Event Date <span class="text-red-500">*</span>
          </label>
          <select
            id="event_date"
            v-model="selectedDate"
            required
            class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
          >
            <option value="">-- Select a date --</option>
            <option 
              v-for="(date, idx) in eventDates" 
              :key="date" 
              :value="date"
              :disabled="submittedDates.includes(date)"
              :class="{ 'text-green-600': submittedDates.includes(date) }"
            >
              Day {{ idx + 1 }}: {{ formatDate(date) }}
              <span v-if="submittedDates.includes(date)" class="text-green-600 ml-2">(Already Submitted ✓)</span>
            </option>
          </select>
          <p v-if="form.errors.event_date" class="mt-2 text-sm text-red-600">{{ form.errors.event_date }}</p>
        </div>

        <!-- Student ID Second -->
        <div>
          <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
            Student/Guest ID <span class="text-red-500">*</span>
          </label>
          <input
            id="student_id"
            v-model="form.student_id"
            type="text"
            required
            :disabled="!selectedDate || submittedDates.includes(selectedDate)"
            class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent disabled:bg-gray-100"
            placeholder="Enter your Student ID or Guest ID"
            autofocus
          />
          <p v-if="form.errors.student_id" class="mt-2 text-sm text-red-600">{{ form.errors.student_id }}</p>
          <p v-if="selectedDate && submittedDates.includes(selectedDate)" class="mt-2 text-sm text-green-600">
            ✓ You have already submitted for this date. Please select another date.
          </p>
        </div>

        <div>
          <button
            type="submit"
            :disabled="form.processing || !selectedDate || !form.student_id || submittedDates.includes(selectedDate)"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all disabled:opacity-50"
          >
            <span v-if="form.processing" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            <span v-else class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="h-5 w-5 text-emerald-300 group-hover:text-emerald-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </span>
            {{ form.processing ? 'Verifying...' : 'Verify & Continue' }}
          </button>
        </div>
      </form>

      <!-- Progress Note -->
      <div v-if="submittedDates.length > 0" class="bg-green-50 rounded-xl p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-green-700">
              You have already submitted for: <strong>{{ submittedDates.map(d => formatDate(d)).join(', ') }}</strong>
            </p>
            <p class="text-xs text-green-600 mt-1">
              {{ eventDates.length - submittedDates.length }} day(s) remaining to evaluate.
            </p>
          </div>
        </div>
      </div>

      <!-- Note -->
      <div class="bg-blue-50 rounded-xl p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-blue-700">
              Only registered participants can access the evaluation form. You can submit one evaluation per day of the event.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  evaluation: {
    type: Object,
    required: true
  }
});

const selectedDate = ref('');
const eventDates = ref([]);
const submittedDates = ref([]);

const form = useForm({
  student_id: '',
  event_date: ''
});

function formatDate(date) {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

async function loadSubmittedDates() {
  if (!form.student_id) return;
  
  try {
    const response = await axios.get(`/evaluations/${props.evaluation.id}/student-submissions`, {
      params: { student_id: form.student_id }
    });
    submittedDates.value = response.data.submitted_dates || [];
  } catch (err) {
    console.error('Failed to load submitted dates:', err);
    submittedDates.value = [];
  }
}

async function verifyStudent() {
  // Validate date selection
  if (!selectedDate.value) {
    form.errors.event_date = 'Please select a date.';
    return;
  }
  
  // Check if already submitted for this date
  if (submittedDates.value.includes(selectedDate.value)) {
    form.errors.student_id = `You have already submitted for ${formatDate(selectedDate.value)}. Please select another date.`;
    return;
  }
  
  // Set the event_date in the form
  form.event_date = selectedDate.value;
  
  form.post(`/evaluations/${props.evaluation.id}/verify`, {
    preserveState: true,
    onSuccess: () => {
      // The response will redirect to the form page with the selected date
    },
    onError: (errors) => {
      console.error('Verification failed:', errors);
    }
  });
}

// Watch for student_id changes to load submitted dates
watch(() => form.student_id, async (newId) => {
  if (newId) {
    await loadSubmittedDates();
  }
});

onMounted(() => {
  // Get event dates from props
  eventDates.value = props.evaluation.event_dates || [];
  console.log('Event dates loaded:', eventDates.value);
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