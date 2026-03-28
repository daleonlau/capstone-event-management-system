<template>
    <OrganizationUserLayout>
      <div class="max-w-6xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Guest Respondents</h1>
            <p class="text-gray-500 mt-1">Manage guest participants for: {{ event.event_name }}</p>
          </div>
          <div class="flex gap-2">
            <Link :href="`/president/events/${event.id}`" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">
              Back to Event
            </Link>
            <button 
              @click="showAddModal = true"
              class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Add Guest
            </button>
            <button 
              @click="$refs.bulkFile.click()"
              class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
              </svg>
              Bulk Upload
            </button>
            <input type="file" ref="bulkFile" accept=".csv" class="hidden" @change="bulkUploadGuests" />
          </div>
        </div>
  
        <!-- Info Card -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-blue-800">
              <p class="font-medium">Guest Respondents</p>
              <p>Guests are external participants (visitors, resource persons, etc.) who can evaluate the event. They will receive a unique Guest ID to access the evaluation form.</p>
            </div>
          </div>
        </div>
  
        <!-- Guests Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Guest List</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest ID</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="guest in guests" :key="guest.id" class="hover:bg-gray-50 transition">
                  <td class="px-6 py-4">
                    <span class="font-mono text-sm">{{ guest.guest_id }}</span>
                  </td>
                  <td class="px-6 py-4 font-medium text-gray-900">{{ guest.name }}</td>
                  <td class="px-6 py-4 text-sm text-gray-500">{{ guest.email }}</td>
                  <td class="px-6 py-4">
                    <span :class="[
                      'px-2 py-1 text-xs rounded-full',
                      guest.status === 'Evaluated' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                    ]">
                      {{ guest.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(guest.created_at) }}</td>
                  <td class="px-6 py-4">
                    <button 
                      @click="deleteGuest(guest)"
                      class="text-red-600 hover:text-red-800 transition"
                      title="Delete Guest"
                    >
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </td>
                </tr>
                <tr v-if="guests.length === 0">
                  <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p>No guests added yet</p>
                    <p class="text-sm mt-1">Click "Add Guest" to invite external participants</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
  
        <!-- Download Template Button -->
        <div class="flex justify-end">
          <button 
            @click="downloadGuestTemplate"
            class="text-sm text-emerald-600 hover:text-emerald-700 flex items-center gap-1"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download Guest CSV Template
          </button>
        </div>
      </div>
  
      <!-- Add Guest Modal -->
      <Teleport to="body">
        <div v-if="showAddModal" class="fixed inset-0 z-50 overflow-y-auto">
          <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showAddModal = false"></div>
          <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
              <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
                <div class="flex items-center justify-between">
                  <h3 class="text-xl font-semibold text-white">Add Guest Respondent</h3>
                  <button @click="showAddModal = false" class="text-white/80 hover:text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>
              <form @submit.prevent="addGuest" class="p-6 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                  <input v-model="newGuest.name" type="text" required
                         class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                  <input v-model="newGuest.email" type="email" required
                         class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Agency/Office</label>
                  <input v-model="newGuest.agency_office" type="text"
                         class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                  <input v-model="newGuest.position" type="text"
                         class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex justify-end gap-3 pt-4">
                  <button type="button" @click="showAddModal = false"
                          class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                  <button type="submit" :disabled="adding"
                          class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50">
                    {{ adding ? 'Adding...' : 'Add Guest' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </Teleport>
  
      <!-- Toast Notification -->
      <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-x-full opacity-0"
        enter-to-class="translate-x-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="translate-x-full opacity-0"
      >
        <div v-if="toast.show" 
             class="fixed bottom-4 right-4 z-50 flex min-w-[320px] items-center gap-3 rounded-xl border-l-4 p-4 shadow-2xl backdrop-blur-sm"
             :class="toast.bgClass">
          <span class="flex-1">{{ toast.message }}</span>
          <button @click="toast.show = false" class="text-gray-400 hover:text-gray-600">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </Transition>
    </OrganizationUserLayout>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { Link, router } from '@inertiajs/vue3';
  import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
  import axios from 'axios';
  
  const props = defineProps({
    event: {
      type: Object,
      required: true
    },
    guests: {
      type: Array,
      default: () => []
    }
  });
  
  const showAddModal = ref(false);
  const adding = ref(false);
  const toast = ref({ show: false, message: '', type: 'success', bgClass: '' });
  const bulkFile = ref(null);
  
  const newGuest = ref({
    name: '',
    email: '',
    agency_office: '',
    position: ''
  });
  
  function formatDate(date) {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  }
  
  function showToast(message, type = 'success') {
    const colors = {
      success: 'border-green-500 bg-green-50 text-green-800',
      error: 'border-red-500 bg-red-50 text-red-800',
      info: 'border-blue-500 bg-blue-50 text-blue-800'
    };
    
    toast.value = { 
      show: true, 
      message, 
      type,
      bgClass: colors[type] || colors.success
    };
    
    setTimeout(() => toast.value.show = false, 3000);
  }
  
  async function addGuest() {
    adding.value = true;
    try {
      const response = await axios.post(`/president/events/${props.event.id}/guests`, newGuest.value);
      if (response.data.success) {
        showToast('Guest added successfully', 'success');
        showAddModal.value = false;
        newGuest.value = { name: '', email: '', agency_office: '', position: '' };
        router.reload();
      }
    } catch (error) {
      showToast(error.response?.data?.error || 'Failed to add guest', 'error');
    } finally {
      adding.value = false;
    }
  }
  
  async function deleteGuest(guest) {
    if (!confirm(`Remove ${guest.name} from guest list?`)) return;
    
    try {
      const response = await axios.delete(`/president/events/${props.event.id}/guests/${guest.id}`);
      if (response.data.success) {
        showToast('Guest removed successfully', 'success');
        router.reload();
      }
    } catch (error) {
      showToast('Failed to remove guest', 'error');
    }
  }
  
  function downloadGuestTemplate() {
    window.open(`/president/events/${props.event.id}/guests/template`, '_blank');
  }
  
  async function bulkUploadGuests(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    const formData = new FormData();
    formData.append('csv_file', file);
    
    try {
      const response = await axios.post(`/president/events/${props.event.id}/guests/bulk`, formData);
      if (response.data.success) {
        showToast(response.data.message, 'success');
        router.reload();
      }
    } catch (error) {
      showToast('Failed to upload guests', 'error');
    }
    
    event.target.value = '';
  }
  </script>