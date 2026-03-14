<template>
    <OrganizationUserLayout>
      <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Approval History</h1>
            <p class="text-gray-500 mt-1">View all past approvals and rejections</p>
          </div>
          <Link href="/adviser/approvals" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Pending
          </Link>
        </div>
  
        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-4">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Search Events</label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search by event name..."
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
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>
          </div>
        </div>
  
        <!-- History Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Decision</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
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
                  <td class="px-6 py-4">
                    <span :class="decisionBadgeClass(event.approval_status)" class="px-3 py-1 text-xs rounded-full">
                      {{ event.approval_status === 'approved' ? 'Approved' : 'Rejected' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-500">{{ event.approved_at || event.updated_at }}</td>
                  <td class="px-6 py-4 max-w-xs">
                    <p class="text-sm text-gray-600 truncate" :title="event.rejection_reason">
                      {{ event.rejection_reason || '—' }}
                    </p>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <Link :href="`/president/events/${event.id}`" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Event</Link>
                  </td>
                </tr>
                <tr v-if="events.data.length === 0">
                  <td colspan="6" class="px-6 py-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-500">No approval history found</p>
                    <p class="text-sm text-gray-400 mt-1">Events you approve or reject will appear here</p>
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
  import { ref, reactive } from 'vue';
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
  
  const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || ''
  });
  
  function decisionBadgeClass(status) {
    const base = 'px-3 py-1 text-xs rounded-full font-medium';
    switch(status) {
      case 'approved': return `${base} bg-green-100 text-green-700`;
      case 'rejected': return `${base} bg-red-100 text-red-700`;
      default: return `${base} bg-gray-100 text-gray-700`;
    }
  }
  
  function applyFilters() {
    router.get('/adviser/approvals/history', filters, { preserveState: true });
  }
  
  function goToPage(url) {
    if (url) router.visit(url, { preserveState: true });
  }
  </script>