<template>
    <OrganizationUserLayout>
      <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">All Events</h1>
            <p class="text-gray-500 mt-1">View all events in your organization (Read-only)</p>
          </div>
        </div>
  
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500">Total Events</p>
                <p class="text-2xl font-bold text-gray-800">{{ events.total || 0 }}</p>
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
                <p class="text-sm text-gray-500">Pending</p>
                <p class="text-2xl font-bold text-gray-800">{{ pendingCount }}</p>
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
                <p class="text-sm text-gray-500">Approved</p>
                <p class="text-2xl font-bold text-gray-800">{{ approvedCount }}</p>
              </div>
            </div>
          </div>
  
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500">Rejected</p>
                <p class="text-2xl font-bold text-gray-800">{{ rejectedCount }}</p>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-4">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Search Events</label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search by event name or ID..."
                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                @keyup.enter="applyFilters"
              />
            </div>
  
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
              <select
                v-model="filters.status"
                @change="applyFilters"
                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
                <option value="">All Status</option>
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
                <option value="Finished">Finished</option>
              </select>
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
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                  <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="event in events.data" :key="event.id" class="hover:bg-gray-50 transition">
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
                  <td class="px-6 py-4 text-sm text-gray-600">{{ event.event_type?.name }}</td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ formatDateRange(event.event_date_start, event.event_date_end) }}</td>
                  <td class="px-6 py-4">
                    <span v-if="event.payment === 'Payment'" class="text-sm text-emerald-600 font-medium">
                      ₱{{ event.event_fee }}
                    </span>
                    <span v-else class="text-sm text-gray-500">Free</span>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="statusBadgeClass(event.status)" class="px-3 py-1 text-xs rounded-full">
                      {{ event.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="approvalBadgeClass(event.approval_status)" class="px-3 py-1 text-xs rounded-full">
                      {{ formatApprovalStatus(event.approval_status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="documentBadgeClass(event.has_document)" class="px-3 py-1 text-xs rounded-full">
                      {{ event.has_document ? 'Uploaded' : 'Missing' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <Link :href="`/adviser/events/${event.id}`" 
                          class="inline-flex items-center gap-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      View
                    </Link>
                  </td>
                </tr>
                <tr v-if="events.data.length === 0">
                  <td colspan="8" class="px-6 py-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500">No events found</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
  
          <!-- Pagination -->
          <div v-if="events.links && events.links.length > 3" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-500">
              Showing {{ events.from }} to {{ events.to }} of {{ events.total }} entries
            </div>
            <div class="flex gap-2">
              <button
                v-for="(link, index) in events.links"
                :key="index"
                v-html="link.label"
                @click="goToPage(link.url)"
                class="px-3 py-1 border rounded-lg hover:bg-gray-50 transition disabled:opacity-50"
                :class="{ 'bg-emerald-600 text-white hover:bg-emerald-700': link.active }"
                :disabled="!link.url"
              ></button>
            </div>
          </div>
        </div>
      </div>
    </OrganizationUserLayout>
  </template>
  
  <script setup>
  import { ref, reactive, computed } from 'vue';
  import { Link, router } from '@inertiajs/vue3';
  import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
  
  const props = defineProps({
    events: {
      type: Object,
      default: () => ({ data: [], links: [] })
    },
    filters: {
      type: Object,
      default: () => ({})
    }
  });
  
  // Stats
  const pendingCount = computed(() => props.events.data?.filter(e => e.status === 'Pending').length || 0);
  const approvedCount = computed(() => props.events.data?.filter(e => e.status === 'Approved').length || 0);
  const rejectedCount = computed(() => props.events.data?.filter(e => e.approval_status === 'rejected').length || 0);
  
  const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || ''
  });
  
  function formatDateRange(start, end) {
    const startDate = new Date(start).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    const endDate = new Date(end).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    return `${startDate} - ${endDate}`;
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
  
  function documentBadgeClass(hasDocument) {
    const base = 'px-3 py-1 text-xs rounded-full font-medium';
    return hasDocument 
      ? `${base} bg-green-100 text-green-700`
      : `${base} bg-red-100 text-red-700`;
  }
  
  function applyFilters() {
    router.get('/adviser/events', filters, { preserveState: true });
  }
  
  function goToPage(url) {
    if (url) router.visit(url, { preserveState: true });
  }
  </script>