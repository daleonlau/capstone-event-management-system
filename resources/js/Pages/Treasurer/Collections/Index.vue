<template>
  <OrganizationUserLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header with Welcome Message -->
        <div class="mb-8">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                Collections Dashboard
              </h1>
              <p class="text-gray-500 mt-1 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
              </p>
            </div>
            
            <!-- Quick Stats Pills -->
            <div class="flex gap-2">
              <div class="px-4 py-2 bg-white rounded-xl shadow-md flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-sm text-gray-600">{{ events.length }} Active Events</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Animated Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <!-- Active Events Card -->
          <div class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                  <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Active</span>
              </div>
              <p class="text-sm text-gray-500 mb-1">Active Events</p>
              <p class="text-3xl font-bold text-gray-800">{{ events.length }}</p>
              <div class="mt-4 flex items-center gap-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span>{{ getUpcomingEvents }} upcoming</span>
              </div>
            </div>
          </div>

          <!-- Total Collected Card -->
          <div class="group bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
            <div class="p-6 text-white">
              <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl backdrop-blur-sm flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <span class="text-xs font-medium text-white bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                  Lifetime Collections
                </span>
              </div>
              <p class="text-sm text-white/80 mb-1">Total Collected</p>
              <p class="text-3xl font-bold">₱{{ formatNumber(totalCollected) }}</p>
              <div class="mt-4 flex items-center gap-2 text-sm text-white/80">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Across {{ events.length }} events</span>
              </div>
            </div>
          </div>

          <!-- Total Students Card -->
          <div class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                  <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </div>
                <span class="text-xs font-medium text-purple-600 bg-purple-50 px-3 py-1 rounded-full">Eligible</span>
              </div>
              <p class="text-sm text-gray-500 mb-1">Total Students</p>
              <p class="text-3xl font-bold text-gray-800">{{ formatNumber(totalStudents) }}</p>
              <div class="mt-4 flex items-center gap-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>{{ events.length }} participating orgs</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-4 mb-8">
          <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[300px]">
              <div class="relative group">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search events by name..."
                  class="w-full pl-12 pr-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-300 outline-none"
                  @keyup.enter="applySearch"
                />
                <svg class="w-5 h-5 absolute left-4 top-3.5 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <button v-if="searchQuery" @click="clearSearch" class="absolute right-4 top-3.5 text-gray-400 hover:text-gray-600">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Sort Options -->
            <select v-model="sortBy" class="px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-300 outline-none text-gray-700">
              <option value="date">Sort by Date</option>
              <option value="name">Sort by Name</option>
              <option value="progress">Sort by Progress</option>
              <option value="collected">Sort by Collected</option>
            </select>

            <button @click="refreshList" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Refresh
            </button>
          </div>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="event in sortedEvents" :key="event.id" 
               class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-[1.02] cursor-pointer overflow-hidden"
               @click="goToEvent(event.id)">
            
            <!-- Event Header with Gradient -->
            <div class="h-32 bg-gradient-to-r from-emerald-500 to-teal-600 p-4 relative overflow-hidden">
              <!-- Decorative Circles -->
              <div class="absolute -right-8 -top-8 w-24 h-24 bg-white/10 rounded-full"></div>
              <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-white/10 rounded-full"></div>
              
              <div class="relative z-10 flex justify-between items-start">
                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white rounded-full text-xs flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                  </svg>
                  {{ formatDate(event.event_date_start) }}
                </span>
                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white rounded-full text-xs flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  ₱{{ formatNumber(event.event_fee) }}
                </span>
              </div>
            </div>
            
            <!-- Event Details -->
            <div class="p-6">
              <div class="flex items-start justify-between mb-3">
                <div>
                  <h3 class="text-lg font-semibold text-gray-800 group-hover:text-emerald-600 transition-colors line-clamp-1">
                    {{ event.event_name }}
                  </h3>
                  <p class="text-sm text-gray-500 mt-1">{{ event.event_type?.name || 'General Event' }}</p>
                </div>
                
                <!-- Status Badge -->
                <span class="px-2 py-1 text-xs font-medium rounded-full"
                      :class="getEventStatus(event).class">
                  {{ getEventStatus(event).label }}
                </span>
              </div>
              
              <!-- Progress Stats -->
              <div class="space-y-3">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Collection Progress</span>
                  <span class="font-medium text-emerald-600">{{ event.paid_count }}/{{ event.target_count || 0 }}</span>
                </div>
                
                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                  <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full h-2.5 transition-all duration-1000 relative"
                       :style="{ width: event.progress + '%' }">
                    <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                  </div>
                </div>
                
                <!-- Collection Stats -->
                <div class="grid grid-cols-2 gap-2 pt-2">
                  <div class="bg-gray-50 rounded-lg p-2 text-center">
                    <p class="text-xs text-gray-500">Collected</p>
                    <p class="text-sm font-bold text-emerald-600">₱{{ formatNumber(event.collected_amount) }}</p>
                  </div>
                  <div class="bg-gray-50 rounded-lg p-2 text-center">
                    <p class="text-xs text-gray-500">Progress</p>
                    <p class="text-sm font-bold text-gray-700">{{ event.progress }}%</p>
                  </div>
                </div>
              </div>
              
              <!-- Action Footer -->
              <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="flex -space-x-2">
                    <div class="w-6 h-6 bg-emerald-100 rounded-full border-2 border-white flex items-center justify-center">
                      <span class="text-[10px] font-bold text-emerald-700">{{ event.paid_count }}</span>
                    </div>
                    <div class="w-6 h-6 bg-yellow-100 rounded-full border-2 border-white flex items-center justify-center">
                      <span class="text-[10px] font-bold text-yellow-700">{{ event.target_count - event.paid_count }}</span>
                    </div>
                  </div>
                  <span class="text-xs text-gray-500">paid/left</span>
                </div>
                
                <span class="text-emerald-600 text-sm font-medium inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                  Manage
                  <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </span>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="events.length === 0" class="col-span-3">
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
              <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              </div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">No Events Available</h3>
              <p class="text-gray-500 mb-4">There are no approved events requiring payment collection at the moment.</p>
              <div class="inline-flex items-center gap-2 text-sm text-gray-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Events will appear here once approved by the adviser</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Tips -->
        <div class="mt-8 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-100">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <h4 class="font-semibold text-gray-800 mb-1">Quick Tips</h4>
              <ul class="text-sm text-gray-600 space-y-1">
                <li>• Click on any event card to manage collections</li>
                <li>• Use the search bar to quickly find events</li>
                <li>• Sort events by date, name, or progress using the dropdown</li>
                <li>• View detailed analytics by clicking "View Summary" in each event</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </OrganizationUserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';

const props = defineProps({
  events: {
    type: Array,
    default: () => []
  }
});

// State
const searchQuery = ref('');
const sortBy = ref('date');

// Computed
const totalCollected = computed(() => {
  return props.events.reduce((sum, event) => sum + (event.collected_amount || 0), 0);
});

const totalStudents = computed(() => {
  return props.events.reduce((sum, event) => sum + (event.target_count || 0), 0);
});

const getUpcomingEvents = computed(() => {
  const today = new Date();
  return props.events.filter(event => new Date(event.event_date_start) > today).length;
});

const filteredEvents = computed(() => {
  if (!searchQuery.value) return props.events;
  
  const query = searchQuery.value.toLowerCase();
  return props.events.filter(event => 
    event.event_name.toLowerCase().includes(query) ||
    (event.event_type?.name || '').toLowerCase().includes(query)
  );
});

const sortedEvents = computed(() => {
  const events = [...filteredEvents.value];
  
  switch(sortBy.value) {
    case 'name':
      return events.sort((a, b) => a.event_name.localeCompare(b.event_name));
    case 'progress':
      return events.sort((a, b) => (b.progress || 0) - (a.progress || 0));
    case 'collected':
      return events.sort((a, b) => (b.collected_amount || 0) - (a.collected_amount || 0));
    case 'date':
    default:
      return events.sort((a, b) => new Date(b.event_date_start) - new Date(a.event_date_start));
  }
});

// Methods
function formatNumber(num) {
  if (num === null || num === undefined) return '0.00';
  return Number(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
}

function getEventStatus(event) {
  const today = new Date();
  const eventDate = new Date(event.event_date_start);
  
  if (eventDate < today) {
    return { label: 'Ongoing', class: 'bg-blue-100 text-blue-700' };
  } else if (eventDate.toDateString() === today.toDateString()) {
    return { label: 'Today', class: 'bg-green-100 text-green-700' };
  } else {
    return { label: 'Upcoming', class: 'bg-purple-100 text-purple-700' };
  }
}

function goToEvent(eventId) {
  router.visit(`/treasurer/collections/${eventId}`);
}

function applySearch() {
  // Search is reactive, no need for additional logic
}

function clearSearch() {
  searchQuery.value = '';
}

function refreshList() {
  router.reload({ preserveState: true });
}
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>