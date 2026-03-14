<template>
    <OrganizationUserLayout>
      <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Adviser Dashboard</h1>
            <p class="text-gray-500 mt-1">Welcome back, {{ user?.name }}! Here's what needs your attention.</p>
          </div>
          <div class="bg-white px-4 py-2 rounded-xl shadow-sm flex items-center gap-2">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            <span class="text-sm text-gray-600">{{ currentDate }}</span>
          </div>
        </div>
  
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Pending Approval -->
          <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-yellow-600 bg-yellow-50 px-3 py-1 rounded-full">Action Needed</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ stats.pending_approval }}</h3>
            <p class="text-sm text-gray-500 mt-1">Pending Approval</p>
            <div class="mt-4 flex items-center gap-2">
              <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-yellow-500 rounded-full" :style="{ width: `${Math.min((stats.pending_approval / 10) * 100, 100)}%` }"></div>
              </div>
              <span class="text-xs text-gray-500">{{ stats.pending_approval }} pending</span>
            </div>
          </div>
  
          <!-- Pending Document -->
          <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-orange-600 bg-orange-50 px-3 py-1 rounded-full">Documents</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ stats.pending_document }}</h3>
            <p class="text-sm text-gray-500 mt-1">Pending Documents</p>
          </div>
  
          <!-- Approved Events -->
          <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <span class="text-sm font-medium text-green-600 bg-green-50 px-3 py-1 rounded-full">Approved</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ stats.approved }}</h3>
            <p class="text-sm text-gray-500 mt-1">Approved Events</p>
          </div>
  
          <!-- Rejected Events -->
          <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </div>
              <span class="text-sm font-medium text-red-600 bg-red-50 px-3 py-1 rounded-full">Rejected</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ stats.rejected }}</h3>
            <p class="text-sm text-gray-500 mt-1">Rejected Events</p>
          </div>
        </div>
  
        <!-- Pending Approvals Section -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Events Pending Your Approval</h2>
            <Link href="/adviser/approvals" class="text-sm text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
              View All
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </Link>
          </div>
  
          <div v-if="pendingEvents.length > 0" class="divide-y divide-gray-200">
            <div v-for="event in pendingEvents" :key="event.id" class="p-6 hover:bg-gray-50 transition">
              <div class="flex items-start justify-between">
                <div class="flex items-start gap-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-lg">{{ event.event_name?.charAt(0) }}</span>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-800">{{ event.event_name }}</h3>
                    <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                      <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                        </svg>
                        {{ event.event_type?.name }}
                      </span>
                      <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ event.created_at }}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <a :href="`/storage/${event.signed_document_path}`" target="_blank" 
                     class="px-3 py-1.5 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition text-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    View Doc
                  </a>
                  <Link :href="`/adviser/approvals/${event.id}`" 
                     class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm">
                    Review
                  </Link>
                </div>
              </div>
            </div>
          </div>
  
          <div v-else class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-500">No events pending your approval</p>
            <p class="text-sm text-gray-400 mt-1">Great job! You're all caught up.</p>
          </div>
        </div>
  
        <!-- Quick Links -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <Link href="/adviser/approvals/history" 
                class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition flex items-center gap-4 group">
            <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
              <svg class="w-7 h-7 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <h3 class="font-semibold text-gray-800">Approval History</h3>
              <p class="text-sm text-gray-500">View past approvals and rejections</p>
            </div>
          </Link>
  
          <Link href="/adviser/evaluations" 
                class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition flex items-center gap-4 group">
            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
              <svg class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
              </svg>
            </div>
            <div>
              <h3 class="font-semibold text-gray-800">Event Evaluations</h3>
              <p class="text-sm text-gray-500">View feedback and ratings</p>
            </div>
          </Link>
        </div>
      </div>
    </OrganizationUserLayout>
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3';
  import { usePage } from '@inertiajs/vue3';
  import { computed } from 'vue';
  import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
  
  const page = usePage();
  const user = page.props.auth?.user;
  
  const props = defineProps({
    stats: {
      type: Object,
      default: () => ({
        pending_approval: 0,
        pending_document: 0,
        approved: 0,
        rejected: 0
      })
    },
    pendingEvents: {
      type: Array,
      default: () => []
    }
  });
  
  const currentDate = computed(() => {
    return new Date().toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  });
  </script>