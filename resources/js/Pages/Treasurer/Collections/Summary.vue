<template>
    <OrganizationUserLayout>
      <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <!-- Header with Navigation -->
          <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <div class="flex items-center gap-4">
                <Link :href="`/treasurer/collections/${event.id}`" 
                      class="group flex items-center justify-center w-10 h-10 bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                  <svg class="w-5 h-5 text-gray-600 group-hover:text-emerald-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                  </svg>
                </Link>
                <div>
                  <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                      Collection Analytics
                    </h1>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">
                      {{ getEventStatus }}
                    </span>
                  </div>
                  <p class="text-gray-500 mt-1 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    {{ event.event_name }}
                  </p>
                </div>
              </div>
              
              <div class="flex gap-3">
                <button @click="printSummary" 
                        class="group relative px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all duration-300 hover:shadow-lg hover:scale-105 flex items-center gap-2 overflow-hidden">
                  <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                  </svg>
                  Print Report
                </button>
              </div>
            </div>
          </div>
  
          <!-- Key Metrics Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Students Card -->
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
              <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                  <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                  </div>
                  <span class="text-xs font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Eligible</span>
                </div>
                <p class="text-sm text-gray-500 mb-1">Total Students</p>
                <p class="text-3xl font-bold text-gray-800">{{ summary.total_students }}</p>
                <p class="text-xs text-gray-400 mt-2">Across all courses & years</p>
              </div>
            </div>
  
            <!-- Paid Students Card -->
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
              <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                  <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors">
                    <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <span class="text-xs font-medium text-green-600 bg-green-50 px-3 py-1 rounded-full">{{ ((summary.paid_students / summary.total_students) * 100).toFixed(1) }}%</span>
                </div>
                <p class="text-sm text-gray-500 mb-1">Paid Students</p>
                <p class="text-3xl font-bold text-green-600">{{ summary.paid_students }}</p>
                <div class="mt-2 h-1 bg-green-100 rounded-full overflow-hidden">
                  <div class="h-full bg-green-600 rounded-full" :style="{ width: (summary.paid_students / summary.total_students * 100) + '%' }"></div>
                </div>
              </div>
            </div>
  
            <!-- Pending Students Card -->
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
              <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                  <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center group-hover:bg-yellow-200 transition-colors">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <span class="text-xs font-medium text-yellow-600 bg-yellow-50 px-3 py-1 rounded-full">{{ ((summary.pending_students / summary.total_students) * 100).toFixed(1) }}%</span>
                </div>
                <p class="text-sm text-gray-500 mb-1">Pending</p>
                <p class="text-3xl font-bold text-yellow-600">{{ summary.pending_students }}</p>
                <div class="mt-2 h-1 bg-yellow-100 rounded-full overflow-hidden">
                  <div class="h-full bg-yellow-600 rounded-full" :style="{ width: (summary.pending_students / summary.total_students * 100) + '%' }"></div>
                </div>
              </div>
            </div>
  
            <!-- Not Paid Card -->
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
              <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                  <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-200 transition-colors">
                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <span class="text-xs font-medium text-red-600 bg-red-50 px-3 py-1 rounded-full">{{ ((summary.not_paid_students / summary.total_students) * 100).toFixed(1) }}%</span>
                </div>
                <p class="text-sm text-gray-500 mb-1">Not Paid</p>
                <p class="text-3xl font-bold text-red-600">{{ summary.not_paid_students }}</p>
                <div class="mt-2 h-1 bg-red-100 rounded-full overflow-hidden">
                  <div class="h-full bg-red-600 rounded-full" :style="{ width: (summary.not_paid_students / summary.total_students * 100) + '%' }"></div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Financial Overview & Progress Chart -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Collection Progress Circle -->
            <div class="bg-white rounded-2xl shadow-lg p-6 lg:col-span-1">
              <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                Collection Progress
              </h3>
              <div class="flex flex-col items-center justify-center">
                <div class="relative w-48 h-48">
                  <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                    <!-- Background circle -->
                    <circle
                      cx="50"
                      cy="50"
                      r="40"
                      fill="none"
                      stroke="#e5e7eb"
                      stroke-width="10"
                    />
                    <!-- Progress circle -->
                    <circle
                      cx="50"
                      cy="50"
                      r="40"
                      fill="none"
                      :stroke="progressColor"
                      stroke-width="10"
                      :stroke-dasharray="circumference"
                      :stroke-dashoffset="progressOffset"
                      stroke-linecap="round"
                      class="transition-all duration-1000"
                    />
                  </svg>
                  <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-bold" :style="{ color: progressColor }">{{ summary.collection_rate }}%</span>
                    <span class="text-xs text-gray-500">Complete</span>
                  </div>
                </div>
                
                <div class="w-full mt-6 grid grid-cols-2 gap-4 text-center">
                  <div class="bg-green-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 mb-1">Collected</p>
                    <p class="text-lg font-bold text-green-600">₱{{ formatNumber(summary.total_collected) }}</p>
                  </div>
                  <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 mb-1">Expected</p>
                    <p class="text-lg font-bold text-gray-800">₱{{ formatNumber(summary.expected_total) }}</p>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Payment Distribution -->
            <div class="bg-white rounded-2xl shadow-lg p-6 lg:col-span-2">
              <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Payment Distribution
              </h3>
              
              <div class="space-y-4">
                <!-- Paid Progress -->
                <div>
                  <div class="flex justify-between items-center mb-1">
                    <div class="flex items-center gap-2">
                      <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                      <span class="text-sm font-medium text-gray-700">Paid</span>
                    </div>
                    <span class="text-sm font-medium text-green-600">{{ summary.paid_students }} students</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-full h-3 transition-all duration-1000"
                         :style="{ width: (summary.paid_students / summary.total_students * 100) + '%' }">
                      <div class="h-full w-full bg-white/20 animate-pulse"></div>
                    </div>
                  </div>
                  <p class="text-xs text-gray-500 mt-1">₱{{ formatNumber(summary.total_collected) }} collected</p>
                </div>
  
                <!-- Pending Progress -->
                <div>
                  <div class="flex justify-between items-center mb-1">
                    <div class="flex items-center gap-2">
                      <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                      <span class="text-sm font-medium text-gray-700">Pending</span>
                    </div>
                    <span class="text-sm font-medium text-yellow-600">{{ summary.pending_students }} students</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-full h-3 transition-all duration-1000"
                         :style="{ width: (summary.pending_students / summary.total_students * 100) + '%' }">
                    </div>
                  </div>
                  <p class="text-xs text-gray-500 mt-1">Awaiting payment</p>
                </div>
  
                <!-- Not Paid Progress -->
                <div>
                  <div class="flex justify-between items-center mb-1">
                    <div class="flex items-center gap-2">
                      <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                      <span class="text-sm font-medium text-gray-700">Not Paid</span>
                    </div>
                    <span class="text-sm font-medium text-red-600">{{ summary.not_paid_students }} students</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-full h-3 transition-all duration-1000"
                         :style="{ width: (summary.not_paid_students / summary.total_students * 100) + '%' }">
                    </div>
                  </div>
                  <p class="text-xs text-gray-500 mt-1">₱{{ formatNumber(summary.expected_total - summary.total_collected) }} remaining</p>
                </div>
              </div>
  
              <!-- Quick Stats -->
              <div class="mt-6 grid grid-cols-3 gap-2 pt-4 border-t border-gray-100">
                <div class="text-center">
                  <p class="text-xs text-gray-500">Receipts</p>
                  <p class="text-lg font-bold text-gray-800">{{ summary.receipts_generated || 0 }}</p>
                </div>
                <div class="text-center">
                  <p class="text-xs text-gray-500">Collection Rate</p>
                  <p class="text-lg font-bold" :style="{ color: progressColor }">{{ summary.collection_rate }}%</p>
                </div>
                <div class="text-center">
                  <p class="text-xs text-gray-500">Target</p>
                  <p class="text-lg font-bold text-gray-800">{{ summary.total_students }}</p>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Event Details Card -->
          <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Event Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Event Name</p>
                <p class="font-medium text-gray-800">{{ event.event_name }}</p>
              </div>
              <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Event Type</p>
                <p class="font-medium text-gray-800">{{ event.event_type?.name || 'N/A' }}</p>
              </div>
              <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Date Range</p>
                <p class="font-medium text-gray-800">{{ formatDateRange(event.event_date_start, event.event_date_end) }}</p>
              </div>
              <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Event Fee</p>
                <p class="font-medium text-emerald-600 text-lg">₱{{ formatNumber(event.event_fee) }}</p>
              </div>
            </div>
  
            <!-- Course & Year Levels -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-if="event.courses?.length" class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-2">Target Courses</p>
                <div class="flex flex-wrap gap-2">
                  <span v-for="course in event.courses" :key="course" 
                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs">
                    {{ course }}
                  </span>
                </div>
              </div>
              <div v-if="event.year_levels?.length" class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-2">Target Year Levels</p>
                <div class="flex flex-wrap gap-2">
                  <span v-for="year in event.year_levels" :key="year" 
                        class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-xs">
                    Year {{ year }}
                  </span>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Payment History Chart -->
          <div v-if="summary.payments_by_date && summary.payments_by_date.length > 0" 
               class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
              </svg>
              Payment History
            </h3>
            <div style="height: 300px;">
              <canvas ref="paymentChartRef"></canvas>
            </div>
          </div>
  
          <!-- Smart Recommendations -->
          <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
              </svg>
              Smart Recommendations
            </h3>
            
            <ul class="space-y-3">
              <li v-if="summary.collection_rate < 50" 
                  class="flex items-start gap-3 p-3 bg-yellow-50 rounded-xl border border-yellow-200">
                <div class="w-6 h-6 bg-yellow-200 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-yellow-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
                <span class="text-sm text-yellow-800">Collection rate is below 50%. Consider sending payment reminders to pending students.</span>
              </li>
              
              <li v-if="summary.pending_students > 0" 
                  class="flex items-start gap-3 p-3 bg-blue-50 rounded-xl border border-blue-200">
                <div class="w-6 h-6 bg-blue-200 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <span class="text-sm text-blue-800">You have {{ summary.pending_students }} pending payments. Process them to increase collection rate.</span>
              </li>
              
              <li v-if="summary.collection_rate >= 90" 
                  class="flex items-start gap-3 p-3 bg-green-50 rounded-xl border border-green-200">
                <div class="w-6 h-6 bg-green-200 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <span class="text-sm text-green-800">Excellent collection rate! Consider closing this event and generating final reports.</span>
              </li>
  
              <li v-if="summary.not_paid_students > 10" 
                  class="flex items-start gap-3 p-3 bg-purple-50 rounded-xl border border-purple-200">
                <div class="w-6 h-6 bg-purple-200 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-4 h-4 text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <span class="text-sm text-purple-800">Many students haven't paid yet. Consider creating a follow-up campaign.</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </OrganizationUserLayout>
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3';
  import { ref, onMounted, computed } from 'vue';
  import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
  import Chart from 'chart.js/auto';
  
  const props = defineProps({
    event: {
      type: Object,
      required: true
    },
    summary: {
      type: Object,
      required: true
    }
  });
  
  const paymentChartRef = ref(null);
  let paymentChart = null;
  
  const circumference = 2 * Math.PI * 40;
  const progressOffset = computed(() => {
    const progress = (100 - props.summary.collection_rate) / 100;
    return circumference * progress;
  });
  
  const progressColor = computed(() => {
    const rate = props.summary.collection_rate;
    if (rate >= 75) return '#10b981';
    if (rate >= 50) return '#f59e0b';
    return '#ef4444';
  });
  
  const getEventStatus = computed(() => {
    const today = new Date();
    const eventDate = new Date(props.event.event_date_start);
    
    if (eventDate < today) return 'Ongoing';
    if (eventDate.toDateString() === today.toDateString()) return 'Today';
    return 'Upcoming';
  });
  
  function formatNumber(num) {
    if (num === null || num === undefined) return '0.00';
    return Number(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
  
  function formatDateRange(start, end) {
    const startDate = new Date(start).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    const endDate = new Date(end).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    return `${startDate} - ${endDate}`;
  }
  
  function printSummary() {
    window.print();
  }
  
  onMounted(() => {
    if (props.summary.payments_by_date && props.summary.payments_by_date.length > 0 && paymentChartRef.value) {
      const dates = props.summary.payments_by_date.map(p => {
        const date = new Date(p.date);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
      });
      const amounts = props.summary.payments_by_date.map(p => p.total);
      
      paymentChart = new Chart(paymentChartRef.value, {
        type: 'line',
        data: {
          labels: dates,
          datasets: [{
            label: 'Daily Collections',
            data: amounts,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return '₱' + context.raw.toLocaleString();
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return '₱' + value.toLocaleString();
                }
              },
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });
    }
  });
  </script>
  
  <style scoped>
  @media print {
    .no-print {
      display: none;
    }
  }
  
  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
  }
  .animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }
  </style>