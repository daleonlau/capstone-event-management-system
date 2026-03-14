<template>
  <OrganizationUserLayout>
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Event</h1>
        <p class="text-gray-500 mt-1">Update event details</p>
      </div>

      <!-- Error Display -->
      <Transition name="fade">
        <div v-if="Object.keys(form.errors).length > 0" class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
          <ul class="list-disc list-inside text-sm text-red-600">
            <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
          </ul>
        </div>
      </Transition>

      <!-- Form -->
      <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Event Name</label>
              <input v-model="form.event_name" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
              <select v-model="form.event_type_id" @change="onEventTypeChange" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required>
                <option v-for="type in eventTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Event Fee</label>
              <div class="relative">
                <span class="absolute left-3 top-3 text-gray-500">₱</span>
                <input v-model="form.event_fee" type="number" :disabled="!requiresPayment" class="w-full pl-8 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" :class="{ 'bg-gray-100': !requiresPayment }" />
              </div>
            </div>
          </div>
        </div>

        <!-- Event Dates -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Event Dates</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
              <input v-model="form.event_date_start" type="date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
              <input v-model="form.event_date_end" type="date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required />
            </div>
          </div>
        </div>

        <!-- Target Audience -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Target Audience</h2>

          <div class="space-y-6">
            <!-- Departments -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">Departments</label>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="dept in departments" :key="dept.id" class="border rounded-xl p-4">
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" :value="dept.id" v-model="form.departments" @change="toggleDepartment(dept.id)" class="w-4 h-4 text-emerald-600 rounded" />
                    <span class="font-medium">{{ dept.name }}</span>
                  </label>
                  
                  <div v-if="form.departments.includes(dept.id)" class="ml-6 mt-3 space-y-2">
                    <div v-for="course in dept.courses" :key="course.id" class="flex items-center gap-2">
                      <input type="checkbox" :value="course.id" v-model="form.courses" class="w-3 h-3 text-emerald-600 rounded" />
                      <span class="text-sm">{{ course.name }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Year Levels -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">Year Levels</label>
              <div class="flex flex-wrap gap-4">
                <label v-for="year in yearLevels" :key="year" class="flex items-center gap-2">
                  <input type="checkbox" :value="year" v-model="form.year_levels" class="w-4 h-4 text-emerald-600 rounded" />
                  <span>{{ year }}</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Current Document -->
        <div v-if="event.signed_document_path" class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Current Document</h2>
          <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-700">Signed Document</p>
              <p class="text-xs text-gray-500">Uploaded on {{ formatDate(event.updated_at) }}</p>
            </div>
            <a :href="`/storage/${event.signed_document_path}`" target="_blank" class="text-emerald-600 hover:text-emerald-700">View</a>
          </div>
        </div>

        <!-- Replace Document -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Replace Document (Optional)</h2>
          <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-emerald-500 transition cursor-pointer"
               @dragover.prevent
               @drop.prevent="handleDrop"
               @click="$refs.fileInput.click()">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            <p class="text-gray-600 mb-2">{{ fileName || 'Click to upload new document' }}</p>
            <p class="text-sm text-gray-500">Leave empty to keep current document</p>
          </div>
          <input ref="fileInput" type="file" @change="handleFileChange" accept=".pdf,.jpg,.jpeg,.png" class="hidden" />
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end gap-3">
          <Link href="/president/events" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">Cancel</Link>
          <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition disabled:opacity-50" :disabled="form.processing">
            {{ form.processing ? 'Updating...' : 'Update Event' }}
          </button>
        </div>
      </form>
    </div>
  </OrganizationUserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';

const props = defineProps({
  event: {
    type: Object,
    required: true
  },
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

const fileName = ref('');

const form = useForm({
  event_name: props.event.event_name,
  event_type_id: props.event.event_type_id,
  event_date_start: props.event.event_date_start,
  event_date_end: props.event.event_date_end,
  event_fee: props.event.event_fee,
  departments: props.event.departments || [],
  courses: props.event.courses || [],
  year_levels: props.event.year_levels || [],
  signed_document: null
});

const selectedEventType = computed(() => props.eventTypes.find(t => t.id === form.event_type_id));
const requiresPayment = computed(() => selectedEventType.value?.requires_payment || false);

function formatDate(date) {
  return new Date(date).toLocaleDateString();
}

function onEventTypeChange() {
  if (!requiresPayment.value) form.event_fee = 0;
}

function toggleDepartment(deptId) {
  if (!form.departments.includes(deptId)) {
    const dept = props.departments.find(d => d.id === deptId);
    if (dept?.courses) {
      const courseIds = dept.courses.map(c => c.id);
      form.courses = form.courses.filter(id => !courseIds.includes(id));
    }
  }
}

function handleFileChange(e) {
  const file = e.target.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      alert('File size must not exceed 5MB');
      return;
    }
    form.signed_document = file;
    fileName.value = file.name;
  }
}

function handleDrop(e) {
  const file = e.dataTransfer.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      alert('File size must not exceed 5MB');
      return;
    }
    form.signed_document = file;
    fileName.value = file.name;
  }
}

function submit() {
  form.post(`/president/events/${props.event.id}`, {
    forceFormData: true,
    method: 'put'
  });
}
</script>