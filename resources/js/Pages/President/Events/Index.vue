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
                  <Link :href="`/president/events/${event.id}/edit`" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">Edit</Link>
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

      <!-- Delete Confirmation Modal -->
      <Transition name="modal">
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
          <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                  </div>
                  <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg font-medium text-gray-900">Delete Event</h3>
                    <div class="mt-2">
                      <p class="text-sm text-gray-500">
                        Are you sure you want to delete <span class="font-semibold">{{ selectedEvent?.event_name }}</span>? 
                        This action cannot be undone and will remove all associated data.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button @click="deleteEvent" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Delete</button>
                <button @click="showDeleteModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </OrganizationUserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';

const props = defineProps({
  events: {
    type: Array,
    default: () => []
  }
});

// Stats
const totalEvents = computed(() => props.events.length);
const pendingDocument = computed(() => props.events.filter(e => e.approval_status === 'pending_document').length);
const pendingApproval = computed(() => props.events.filter(e => e.approval_status === 'pending_approval').length);
const approvedEvents = computed(() => props.events.filter(e => e.approval_status === 'approved').length);

// Filters
const filters = ref({
  search: '',
  status: '',
  approval: ''
});

const showDeleteModal = ref(false);
const selectedEvent = ref(null);

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

function formatDate(date) {
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

function applyFilters() {
  router.get('/president/events', filters.value, { preserveState: true });
}

function resetFilters() {
  filters.value = { search: '', status: '', approval: '' };
  applyFilters();
}

function confirmDelete(event) {
  selectedEvent.value = event;
  showDeleteModal.value = true;
}

function deleteEvent() {
  if (selectedEvent.value) {
    router.delete(`/president/events/${selectedEvent.value.id}`, {
      onSuccess: () => {
        showDeleteModal.value = false;
        selectedEvent.value = null;
      }
    });
  }
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>