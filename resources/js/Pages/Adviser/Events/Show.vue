<template>
    <OrganizationUserLayout>
      <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Event Details</h1>
            <p class="text-gray-500 mt-1">{{ event.event_name }}</p>
          </div>
          <Link href="/adviser/events" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to List
          </Link>
        </div>
  
        <!-- Event Header Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="h-32 bg-gradient-to-r from-emerald-500 to-emerald-700"></div>
          <div class="px-8 pb-8">
            <div class="flex items-end -mt-12 mb-6">
              <div class="w-24 h-24 bg-white rounded-2xl shadow-lg p-1">
                <div class="w-full h-full bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center">
                  <span class="text-white text-3xl font-bold">{{ event.event_name?.charAt(0) }}</span>
                </div>
              </div>
              <div class="ml-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ event.event_name }}</h2>
                <p class="text-gray-500">Created by {{ event.created_by }} • {{ formatDate(event.created_at) }}</p>
              </div>
            </div>
  
            <!-- Quick Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-sm text-gray-500">Event Type</p>
                <p class="text-lg font-semibold text-gray-800">{{ event.event_type?.name }}</p>
              </div>
              <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-sm text-gray-500">Date Range</p>
                <p class="text-lg font-semibold text-gray-800">{{ formatDateRange(event.event_date_start, event.event_date_end) }}</p>
              </div>
              <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-sm text-gray-500">Payment</p>
                <p class="text-lg font-semibold" :class="event.payment === 'Payment' ? 'text-emerald-600' : 'text-gray-800'">
                  {{ event.payment === 'Payment' ? `₱${event.event_fee}` : 'Free Event' }}
                </p>
              </div>
              <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-sm text-gray-500">Status</p>
                <span :class="statusBadgeClass(event.status)" class="px-3 py-1 text-xs rounded-full">
                  {{ event.status }}
                </span>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Target Audience -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Target Audience</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Departments -->
            <div>
              <h3 class="font-medium text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Departments
              </h3>
              <div class="flex flex-wrap gap-2">
                <span v-for="dept in event.department_names" :key="dept" 
                      class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm">
                  {{ dept }}
                </span>
                <p v-if="!event.department_names?.length" class="text-gray-400 text-sm">No departments selected</p>
              </div>
            </div>
  
            <!-- Courses -->
            <div>
              <h3 class="font-medium text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Courses
              </h3>
              <div class="flex flex-wrap gap-2">
                <span v-for="course in event.course_names" :key="course" 
                      class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                  {{ course }}
                </span>
                <p v-if="!event.course_names?.length" class="text-gray-400 text-sm">No courses selected</p>
              </div>
            </div>
  
            <!-- Year Levels -->
            <div>
              <h3 class="font-medium text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                </svg>
                Year Levels
              </h3>
              <div class="flex flex-wrap gap-2">
                <span v-for="year in event.year_levels" :key="year" 
                      class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">
                  {{ year }}
                </span>
                <p v-if="!event.year_levels?.length" class="text-gray-400 text-sm">No year levels selected</p>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Signed Document -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Signed Document</h2>
          <div v-if="event.has_document && document_url" class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
            <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-700">Document uploaded</p>
            </div>
            <a :href="document_url" target="_blank" 
               class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 text-sm">
              View Document
            </a>
          </div>
          <p v-else class="text-gray-500">No document uploaded yet.</p>
        </div>
  
        <!-- Rejection Reason (if rejected) -->
        <div v-if="event.approval_status === 'rejected' && event.rejection_reason" class="bg-red-50 border-l-4 border-red-500 rounded-xl p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">Rejection Reason</h3>
              <p class="text-sm text-red-700 mt-1">{{ event.rejection_reason }}</p>
            </div>
          </div>
        </div>
      </div>
    </OrganizationUserLayout>
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3';
  import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
  
  const props = defineProps({
    event: {
      type: Object,
      required: true
    },
    document_url: {
      type: String,
      default: null
    }
  });
  
  function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }
  
  function formatDateRange(start, end) {
    const startDate = new Date(start).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    const endDate = new Date(end).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    return `${startDate} - ${endDate}`;
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
  </script>