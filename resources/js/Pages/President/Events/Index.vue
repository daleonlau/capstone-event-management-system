<template>
  <OrganizationUserLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Events Management</h1>
          <p class="text-gray-500 mt-1">Create and manage your organization's events</p>
        </div>
        <Link
          href="/president/events/create"
          class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all duration-300 transform hover:scale-[1.02] flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Create Event</span>
        </Link>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500">Total Events</p>
              <p class="text-2xl font-bold text-gray-800">{{ totalEvents }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500">Pending Document</p>
              <p class="text-2xl font-bold text-gray-800">{{ pendingDocument }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500">Pending Approval</p>
              <p class="text-2xl font-bold text-gray-800">{{ pendingApproval }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500">Approved</p>
              <p class="text-2xl font-bold text-gray-800">{{ approvedEvents }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-2xl shadow-lg p-4">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Search Events</label>
            <input 
              v-model="filters.search" 
              type="text" 
              placeholder="Search by event name..."
              class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
              @keyup.enter="applyFilters"
            />
          </div>
          <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select v-model="filters.status" @change="applyFilters" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
              <option value="">All Status</option>
              <option value="Pending">Pending</option>
              <option value="Approved">Approved</option>
              <option value="Finished">Finished</option>
            </select>
          </div>
          <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Approval Status</label>
            <select v-model="filters.approval" @change="applyFilters" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
              <option value="">All</option>
              <option value="pending_document">Pending Document</option>
              <option value="pending_approval">Pending Approval</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
          <div class="flex items-end">
            <button @click="resetFilters" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition">
              Reset
            </button>
          </div>
        </div>
      </div>

      <!-- Events Table -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval</th>
                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="event in filteredEvents" :key="event.id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mr-3">
                      <span class="text-white font-bold">{{ event.event_name?.charAt(0) }}</span>
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">{{ event.event_name }}</p>
                      <p class="text-xs text-gray-500">ID: {{ event.id }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ event.event_type?.name || 'N/A' }}</td>
                <td class="px-6 py-4">
                  <div class="text-sm">
                    <p class="text-gray-900">{{ formatDate(event.event_date_start) }}</p>
                    <p class="text-xs text-gray-500">{{ formatDate(event.event_date_end) }}</p>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm">
                    <p class="text-gray-900">{{ event.payment }}</p>
                    <p v-if="event.payment === 'Payment'" class="text-xs text-emerald-600">₱{{ event.event_fee }}</p>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span :class="statusBadgeClass(event.status)" class="px-3 py-1 text-xs rounded-full">
                    {{ event.status }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span :class="documentBadgeClass(event.has_document)" class="px-3 py-1 text-xs rounded-full">
                    {{ event.has_document ? 'Uploaded' : 'Missing' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span :class="approvalBadgeClass(event.approval_status)" class="px-3 py-1 text-xs rounded-full">
                    {{ formatApprovalStatus(event.approval_status) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right space-x-3">
                  <Link :href="`/president/events/${event.id}`" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</Link>
                  <button @click="openEditModal(event)" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">Edit</button>
                  <button @click="confirmDelete(event)" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                </td>
              </tr>
              <tr v-if="filteredEvents.length === 0">
                <td colspan="8" class="px-6 py-8 text-center">
                  <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-gray-500">No events found. Click "Create Event" to get started.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Edit Event Modal -->
      <Teleport to="body">
        <Transition name="modal-fade">
          <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" @click.self="closeEditModal">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-md"></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-100">
              <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Edit Event</h3>
                <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600 transition">
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <div class="px-6 py-6">
                <div v-if="Object.keys(editForm.errors).length > 0" class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                  <ul class="list-disc list-inside text-sm text-red-600">
                    <li v-for="(error, field) in editForm.errors" :key="field">{{ error }}</li>
                  </ul>
                </div>

                <form @submit.prevent="updateEvent" class="space-y-6">
                  <!-- Basic Information -->
                  <div class="bg-gray-50 rounded-xl p-5">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Name</label>
                        <input v-model="editForm.event_name" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
                        <select v-model="editForm.event_type_id" @change="onEventTypeChange" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required>
                          <option v-for="type in eventTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                        </select>
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Fee</label>
                        <div class="relative">
                          <span class="absolute left-3 top-3 text-gray-500">₱</span>
                          <input v-model="editForm.event_fee" type="number" :disabled="!requiresPayment" class="w-full pl-8 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" :class="{ 'bg-gray-100': !requiresPayment }" />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Event Dates -->
                  <div class="bg-gray-50 rounded-xl p-5">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Event Dates</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input v-model="editForm.event_date_start" type="date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required />
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input v-model="editForm.event_date_end" type="date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required />
                      </div>
                    </div>
                  </div>

                  <!-- Target Audience -->
                  <div class="bg-gray-50 rounded-xl p-5">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Target Audience</h2>

                    <div class="space-y-6">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Departments (from your organization's assigned departments)</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div v-for="dept in departments" :key="dept.id" class="border rounded-xl p-4 bg-white">
                            <label class="flex items-center gap-2 cursor-pointer">
                              <input type="checkbox" :value="dept.id" v-model="editForm.departments" @change="toggleDepartment(dept.id)" class="w-4 h-4 text-emerald-600 rounded" />
                              <span class="font-medium">{{ dept.name }}</span>
                            </label>
                            
                            <div v-if="editForm.departments.includes(dept.id)" class="ml-6 mt-3 space-y-2">
                              <div v-for="course in dept.courses" :key="course.id" class="flex items-center gap-2">
                                <input type="checkbox" :value="course.id" v-model="editForm.courses" class="w-3 h-3 text-emerald-600 rounded" />
                                <span class="text-sm">{{ course.name }}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <p v-if="departments.length === 0" class="text-sm text-yellow-600 mt-2">
                          No departments assigned to your organization. Please contact QUAMS admin.
                        </p>
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Year Levels</label>
                        <div class="flex flex-wrap gap-4">
                          <label v-for="year in yearLevels" :key="year" class="flex items-center gap-2">
                            <input type="checkbox" :value="year" v-model="editForm.year_levels" class="w-4 h-4 text-emerald-600 rounded" />
                            <span>{{ year }}</span>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Current Document -->
                  <div v-if="selectedEvent?.signed_document_path" class="bg-gray-50 rounded-xl p-5">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Current Document</h2>
                    <div class="flex items-center gap-4 p-4 bg-white rounded-xl border">
                      <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      <div class="flex-1">
                        <p class="text-sm font-medium text-gray-700">Signed Document</p>
                        <p class="text-xs text-gray-500">Uploaded</p>
                      </div>
                      <a :href="`/storage/${selectedEvent.signed_document_path}`" target="_blank" class="text-emerald-600 hover:text-emerald-700">View</a>
                    </div>
                  </div>

                  <!-- Replace Document -->
                  <div class="bg-gray-50 rounded-xl p-5">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Replace Document (Optional)</h2>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-emerald-500 transition cursor-pointer bg-white"
                         @dragover.prevent
                         @drop.prevent="handleDrop"
                         @click="$refs.fileInput.click()">
                      <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                      </svg>
                      <p class="text-gray-600 mb-2">{{ editFileName || 'Click to upload new document' }}</p>
                      <p class="text-sm text-gray-500">Leave empty to keep current document</p>
                    </div>
                    <input ref="fileInput" type="file" @change="handleFileChange" accept=".pdf,.jpg,.jpeg,.png" class="hidden" />
                  </div>

                  <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="closeEditModal" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition disabled:opacity-50" :disabled="editForm.processing">
                      {{ editForm.processing ? 'Updating...' : 'Update Event' }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </Transition>
      </Teleport>

      <!-- Delete Confirmation Modal -->
      <Teleport to="body">
        <Transition name="modal-fade">
          <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="closeDeleteModal">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-md"></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-100">
              <div class="bg-white px-6 pt-6 pb-4">
                <div class="flex items-center gap-4">
                  <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900">Delete Event</h3>
                    <p class="text-sm text-gray-500 mt-1">
                      Are you sure you want to delete <span class="font-semibold text-gray-700">{{ selectedEvent?.event_name }}</span>?
                    </p>
                    <p class="text-sm text-red-500 mt-1">This action cannot be undone.</p>
                  </div>
                </div>
              </div>
              
              <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                <button @click="closeDeleteModal" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-100 transition">
                  Cancel
                </button>
                <button @click="deleteEvent" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition">
                  Delete Event
                </button>
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
import { Link, router, useForm } from '@inertiajs/vue3';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';

const props = defineProps({
  events: {
    type: Array,
    default: () => []
  },
  departments: {
    type: Array,
    default: () => []
  },
  courses: {
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
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

// Stats
const totalEvents = computed(() => props.events.length);
const pendingDocument = computed(() => props.events.filter(e => e.approval_status === 'pending_document').length);
const pendingApproval = computed(() => props.events.filter(e => e.approval_status === 'pending_approval').length);
const approvedEvents = computed(() => props.events.filter(e => e.approval_status === 'approved').length);

// Filters
const filters = ref({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  approval: props.filters?.approval || ''
});

// Modal states
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedEvent = ref(null);
const editFileName = ref('');

// Edit Form
const editForm = useForm({
  event_name: '',
  event_type_id: '',
  event_date_start: '',
  event_date_end: '',
  event_fee: 0,
  departments: [],
  courses: [],
  year_levels: [],
  signed_document: null
});

// Computed for edit form
const selectedEventType = computed(() => props.eventTypes.find(t => t.id === editForm.event_type_id));
const requiresPayment = computed(() => selectedEventType.value?.requires_payment || false);

// Filtered events
const filteredEvents = computed(() => {
  let filtered = props.events;
  
  if (filters.value.search) {
    const search = filters.value.search.toLowerCase();
    filtered = filtered.filter(e => 
      e.event_name.toLowerCase().includes(search)
    );
  }
  
  if (filters.value.status) {
    filtered = filtered.filter(e => e.status === filters.value.status);
  }
  
  if (filters.value.approval) {
    filtered = filtered.filter(e => e.approval_status === filters.value.approval);
  }
  
  return filtered;
});

// Helper functions
function formatDate(date) {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
}

function formatApprovalStatus(status) {
  if (!status) return 'N/A';
  return status.split('_').map(word => 
    word.charAt(0).toUpperCase() + word.slice(1)
  ).join(' ');
}

function statusBadgeClass(status) {
  const base = 'px-3 py-1 text-xs rounded-full font-medium';
  switch(status) {
    case 'Pending': return `${base} bg-yellow-100 text-yellow-700`;
    case 'Approved': return `${base} bg-green-100 text-green-700`;
    case 'Finished': return `${base} bg-gray-100 text-gray-700`;
    default: return `${base} bg-gray-100 text-gray-700`;
  }
}

function documentBadgeClass(hasDocument) {
  const base = 'px-3 py-1 text-xs rounded-full font-medium';
  return hasDocument 
    ? `${base} bg-green-100 text-green-700`
    : `${base} bg-red-100 text-red-700`;
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

// Filter functions
function applyFilters() {
  router.get('/president/events', filters.value, { preserveState: true });
}

function resetFilters() {
  filters.value = { search: '', status: '', approval: '' };
  applyFilters();
}

// Edit modal functions
function openEditModal(event) {
  selectedEvent.value = event;
  editForm.event_name = event.event_name;
  editForm.event_type_id = event.event_type_id;
  editForm.event_date_start = event.event_date_start;
  editForm.event_date_end = event.event_date_end;
  editForm.event_fee = event.event_fee;
  editForm.departments = event.departments || [];
  editForm.courses = event.courses || [];
  editForm.year_levels = event.year_levels || [];
  editForm.signed_document = null;
  editFileName.value = '';
  showEditModal.value = true;
}

function closeEditModal() {
  showEditModal.value = false;
  selectedEvent.value = null;
  editForm.reset();
  editFileName.value = '';
}

function onEventTypeChange() {
  if (!requiresPayment.value) editForm.event_fee = 0;
}

function toggleDepartment(deptId) {
  if (!editForm.departments.includes(deptId)) {
    const dept = props.departments.find(d => d.id === deptId);
    if (dept?.courses) {
      const courseIds = dept.courses.map(c => c.id);
      editForm.courses = editForm.courses.filter(id => !courseIds.includes(id));
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
    editForm.signed_document = file;
    editFileName.value = file.name;
  }
}

function handleDrop(e) {
  const file = e.dataTransfer.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      alert('File size must not exceed 5MB');
      return;
    }
    editForm.signed_document = file;
    editFileName.value = file.name;
  }
}

function updateEvent() {
  if (!selectedEvent.value) return;
  
  // Use PUT method - this is the fix for the error
  editForm.put(`/president/events/${selectedEvent.value.id}`, {
    onSuccess: () => {
      closeEditModal();
    }
  });
}

// Delete modal functions
function confirmDelete(event) {
  selectedEvent.value = event;
  showDeleteModal.value = true;
}

function closeDeleteModal() {
  showDeleteModal.value = false;
  selectedEvent.value = null;
}

function deleteEvent() {
  if (!selectedEvent.value) return;
  
  router.delete(`/president/events/${selectedEvent.value.id}`, {
    onSuccess: () => {
      closeDeleteModal();
    }
  });
}
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>