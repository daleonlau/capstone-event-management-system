  <template>
    <OrganizationUserLayout>
      <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-gray-800">Create New Event</h1>
          <p class="text-gray-500 mt-1">Fill in the details to create a new event proposal</p>
        </div>

        <!-- Debug Info - Remove in production -->
        <div class="bg-gray-100 p-4 rounded-lg mb-4 text-xs">
          <p><strong>Debug:</strong> Form Processing: {{ form.processing ? 'Yes' : 'No' }}</p>
          <p><strong>Form Errors:</strong> {{ JSON.stringify(form.errors) }}</p>
        </div>

        <!-- Error Display -->
        <Transition name="fade">
          <div v-if="Object.keys(form.errors).length > 0" class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
            <ul class="list-disc list-inside text-sm text-red-600">
              <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
            </ul>
          </div>
        </Transition>

        <!-- Form -->
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Basic Information -->
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <span class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </span>
              Basic Information
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Event Name <span class="text-red-500">*</span>
                </label>
                <input 
                  v-model="form.event_name" 
                  type="text" 
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                  :class="{ 'border-red-500': form.errors.event_name }"
                  placeholder="e.g., Freshmen Orientation 2024"
                  required
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Event Type <span class="text-red-500">*</span>
                </label>
                <select 
                  v-model="form.event_type_id" 
                  @change="onEventTypeChange"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                  :class="{ 'border-red-500': form.errors.event_type_id }"
                  required
                >
                  <option value="">Select Type</option>
                  <option v-for="type in eventTypes" :key="type.id" :value="type.id">
                    {{ type.name }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Event Fee
                </label>
                <div class="relative">
                  <span class="absolute left-3 top-3 text-gray-500">₱</span>
                  <input 
                    v-model="form.event_fee" 
                    type="number" 
                    min="0" 
                    step="0.01"
                    :disabled="!requiresPayment"
                    class="w-full pl-8 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    :class="{ 'bg-gray-100': !requiresPayment, 'border-red-500': form.errors.event_fee }"
                    placeholder="0.00"
                  />
                </div>
                <p v-if="!requiresPayment" class="text-xs text-gray-500 mt-1">Fee not required for this event type</p>
              </div>
            </div>
          </div>

          <!-- Event Dates -->
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                </svg>
              </span>
              Event Dates
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Start Date <span class="text-red-500">*</span>
                </label>
                <input 
                  v-model="form.event_date_start" 
                  type="date" 
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                  :class="{ 'border-red-500': form.errors.event_date_start }"
                  required
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  End Date <span class="text-red-500">*</span>
                </label>
                <input 
                  v-model="form.event_date_end" 
                  type="date" 
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                  :class="{ 'border-red-500': form.errors.event_date_end }"
                  required
                />
              </div>
            </div>
          </div>

          <!-- Target Audience -->
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <span class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </span>
              Target Audience <span class="text-red-500">*</span>
            </h2>

            <div class="space-y-6">
              <!-- Departments -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Departments</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div v-for="dept in departments" :key="dept.id" class="border rounded-xl p-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                      <input 
                        type="checkbox" 
                        :value="dept.id" 
                        v-model="form.departments"
                        @change="toggleDepartment(dept.id)"
                        class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500"
                      />
                      <span class="font-medium">{{ dept.name }}</span>
                    </label>
                    
                    <div v-if="form.departments.includes(dept.id)" class="ml-6 mt-3 space-y-2">
                      <div v-for="course in dept.courses" :key="course.id" class="flex items-center gap-2">
                        <input 
                          type="checkbox" 
                          :value="course.id" 
                          v-model="form.courses"
                          class="w-3 h-3 text-emerald-600 rounded focus:ring-emerald-500"
                        />
                        <span class="text-sm">{{ course.name }}</span>
                      </div>
                    </div>
                  </div>
                </div>
                <p v-if="form.errors.departments" class="mt-2 text-sm text-red-600">{{ form.errors.departments }}</p>
              </div>

              <!-- Year Levels -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Year Levels</label>
                <div class="flex flex-wrap gap-4">
                  <label v-for="year in yearLevels" :key="year" class="flex items-center gap-2">
                    <input 
                      type="checkbox" 
                      :value="year" 
                      v-model="form.year_levels"
                      class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500"
                    />
                    <span>{{ year }}</span>
                  </label>
                </div>
                <p v-if="form.errors.year_levels" class="mt-2 text-sm text-red-600">{{ form.errors.year_levels }}</p>
              </div>
            </div>
          </div>

          <!-- Info Alert - Document NOT required at creation -->
          <div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-blue-700">
                  <span class="font-medium">Note:</span> You can upload the signed document later. After creating the event, you'll be able to upload the document from the event details page.
                </p>
              </div>
            </div>
          </div>

          <!-- Submit Buttons -->
          <div class="flex justify-end gap-3">
            <Link 
              href="/president/events" 
              class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition"
            >
              Cancel
            </Link>
            <button 
              type="submit" 
              class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all duration-300 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              :disabled="form.processing"
            >
              <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ form.processing ? 'Creating...' : 'Create Event' }}</span>
            </button>
          </div>
        </form>
      </div>
    </OrganizationUserLayout>
  </template>

  <script setup>
  import { ref, computed, watch, onMounted } from 'vue';
  import { Link, useForm } from '@inertiajs/vue3';
  import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';

  const props = defineProps({
    departments: {
      type: Array,
      default: () => []
    },
    eventTypes: {
      type: Array,
      default: () => []
    },
    yearLevels: {
      type: Array,
      default: () => ['1st Year', '2nd Year', '3rd Year', '4th Year']
    }
  });

  // Initialize form with default values
  const form = useForm({
    event_name: '',
    event_type_id: '',
    event_date_start: '',
    event_date_end: '',
    event_fee: 0,
    departments: [],
    courses: [],
    year_levels: []
  });

  // Debug on mount
  onMounted(() => {
    console.log('Create form mounted');
    console.log('Form initial state:', form);
    console.log('Form processing:', form.processing);
    console.log('Departments available:', props.departments?.length);
    console.log('Event types available:', props.eventTypes?.length);
  });

  // Watch for form changes
  watch(() => form, (newVal) => {
    console.log('Form changed:', newVal);
  }, { deep: true });

  const selectedEventType = computed(() => {
    return props.eventTypes.find(t => t.id === form.event_type_id);
  });

  const requiresPayment = computed(() => {
    return selectedEventType.value?.requires_payment || false;
  });

  function onEventTypeChange() {
    if (!requiresPayment.value) {
      form.event_fee = 0;
    }
  }

  function toggleDepartment(deptId) {
    if (!form.departments.includes(deptId)) {
      const dept = props.departments.find(d => d.id === deptId);
      if (dept && dept.courses) {
        const courseIds = dept.courses.map(c => c.id);
        form.courses = form.courses.filter(id => !courseIds.includes(id));
      }
    }
  }

  function submit() {
    console.log('Submit button clicked!');
    console.log('Form data:', form.data());
    
    // Validate required fields manually
    if (form.departments.length === 0) {
      form.errors.departments = 'Please select at least one department.';
      return;
    }
    
    if (form.courses.length === 0) {
      form.errors.courses = 'Please select at least one course.';
      return;
    }
    
    if (form.year_levels.length === 0) {
      form.errors.year_levels = 'Please select at least one year level.';
      return;
    }
    
    // Clear any previous errors
    form.errors = {};
    
    // Submit the form
    form.post('/president/events', {
      onSuccess: () => {
        console.log('Event created successfully!');
        form.reset();
      },
      onError: (errors) => {
        console.error('Validation errors:', errors);
      },
      onFinish: () => {
        console.log('Request finished');
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

  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
  }
  .fade-enter-from, .fade-leave-to {
    opacity: 0;
  }
  </style>