<template>
  <OrganizationUserLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header with Welcome Message -->
        <div class="mb-8">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                Collection Reports
              </h1>
              <p class="text-gray-500 mt-1 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Generate and manage collection reports for events
              </p>
            </div>
            
            <!-- Quick Stats Pills -->
            <div class="flex gap-2">
              <div class="px-4 py-2 bg-white rounded-xl shadow-md flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-sm text-gray-600">{{ events.length }} Events with Collection</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Animated Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Events Card -->
          <div class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                  <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Total</span>
              </div>
              <p class="text-sm text-gray-500 mb-1">Total Events</p>
              <p class="text-3xl font-bold text-gray-800">{{ events.length }}</p>
              <div class="mt-4 flex items-center gap-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span>{{ generatedCount }} reports generated</span>
              </div>
            </div>
          </div>

          <!-- Reports Generated Card -->
          <div class="group bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
            <div class="p-6 text-white">
              <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl backdrop-blur-sm flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                  </svg>
                </div>
                <span class="text-xs font-medium text-white bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                  Generated
                </span>
              </div>
              <p class="text-sm text-white/80 mb-1">Reports Generated</p>
              <p class="text-3xl font-bold">{{ generatedCount }}</p>
              <div class="mt-4 flex items-center gap-2 text-sm text-white/80">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>{{ Math.round((generatedCount / events.length) * 100) || 0 }}% completion</span>
              </div>
            </div>
          </div>

          <!-- Total Collected Card -->
          <div class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                  <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <span class="text-xs font-medium text-purple-600 bg-purple-50 px-3 py-1 rounded-full">Total</span>
              </div>
              <p class="text-sm text-gray-500 mb-1">Total Collected</p>
              <p class="text-3xl font-bold text-gray-800">₱{{ formatNumber(totalCollected) }}</p>
              <div class="mt-4 flex items-center gap-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Across {{ events.length }} events</span>
              </div>
            </div>
          </div>

          <!-- Collection Rate Card -->
          <div class="group bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
            <div class="p-6 text-white">
              <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl backdrop-blur-sm flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                </div>
                <span class="text-xs font-medium text-white bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                  Overall
                </span>
              </div>
              <p class="text-sm text-white/80 mb-1">Collection Rate</p>
              <p class="text-3xl font-bold">{{ overallCollectionRate }}%</p>
              <div class="mt-4 flex items-center gap-2 text-sm text-white/80">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span>{{ totalCollectedStudents }} / {{ totalStudents }} students</span>
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

            <!-- Filter by Date Range -->
            <div class="flex gap-2">
              <input
                type="date"
                v-model="filters.date_from"
                placeholder="Date From"
                class="px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-300 outline-none text-gray-700"
              />
              <input
                type="date"
                v-model="filters.date_to"
                placeholder="Date To"
                class="px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-300 outline-none text-gray-700"
              />
            </div>

            <!-- Collection Status Filter -->
            <select v-model="filters.collection_status" class="px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-300 outline-none text-gray-700">
              <option value="">All Status</option>
              <option value="completed">Fully Collected (100%)</option>
              <option value="partial">Partial Collection (1-99%)</option>
              <option value="pending">Pending Collection (0%)</option>
            </select>

            <!-- Sort Options -->
            <select v-model="sortBy" class="px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-300 outline-none text-gray-700">
              <option value="date">Sort by Date</option>
              <option value="name">Sort by Name</option>
              <option value="rate">Sort by Collection Rate</option>
              <option value="collected">Sort by Amount Collected</option>
            </select>

            <div class="flex gap-2">
              <button @click="applyFilters" class="px-4 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Apply
              </button>
              <button @click="resetFilters" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset
              </button>
            </div>
          </div>
        </div>

        <!-- Reports Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="event in sortedEvents" :key="event.id" 
               class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-[1.02] overflow-hidden">
            
            <!-- Event Header with Gradient based on collection rate -->
            <div class="h-32 bg-gradient-to-r p-4 relative overflow-hidden"
                 :class="getHeaderGradient(event.collection_rate)">
              <!-- Decorative Circles -->
              <div class="absolute -right-8 -top-8 w-24 h-24 bg-white/10 rounded-full"></div>
              <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-white/10 rounded-full"></div>
              
              <div class="relative z-10 flex justify-between items-start">
                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white rounded-full text-xs flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                  </svg>
                  {{ formatDate(event.event_date) }}
                </span>
                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white rounded-full text-xs flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  ₱{{ formatNumber(event.event_fee) }} each
                </span>
              </div>
              
              <!-- Collection Rate Badge in Header -->
              <div class="absolute bottom-4 right-4">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                  <span class="text-white font-bold text-lg">{{ Math.round(event.collection_rate) }}%</span>
                </div>
              </div>
            </div>
            
            <!-- Event Details -->
            <div class="p-6">
              <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                  <h3 class="text-lg font-semibold text-gray-800 group-hover:text-emerald-600 transition-colors line-clamp-1">
                    {{ event.event_name }}
                  </h3>
                  <p class="text-sm text-gray-500 mt-1">{{ event.organization_name }}</p>
                </div>
              </div>
              
              <!-- Collection Stats -->
              <div class="space-y-3">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Collection Progress</span>
                  <span class="font-medium text-emerald-600">{{ formatNumber(event.total_collected) }} / {{ formatNumber(event.expected_total) }}</span>
                </div>
                
                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                  <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full h-2.5 transition-all duration-1000 relative"
                       :style="{ width: event.collection_rate + '%' }">
                    <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                  </div>
                </div>
                
                <!-- Student Stats -->
                <div class="grid grid-cols-2 gap-2 pt-2">
                  <div class="bg-gray-50 rounded-lg p-2 text-center">
                    <p class="text-xs text-gray-500">Students</p>
                    <p class="text-sm font-bold text-gray-700">{{ event.total_students }}</p>
                    <p class="text-xs text-gray-400">total assigned</p>
                  </div>
                  <div class="bg-gray-50 rounded-lg p-2 text-center">
                    <p class="text-xs text-gray-500">Paid / Unpaid</p>
                    <p class="text-sm font-bold text-emerald-600">{{ event.paid_students }} / {{ event.not_paid_students }}</p>
                    <p class="text-xs text-gray-400">fully settled</p>
                  </div>
                </div>
              </div>
              
              <!-- Report Status and Actions -->
              <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full" :class="event.report_generated_at ? 'bg-green-500' : 'bg-yellow-500'"></div>
                    <span class="text-xs text-gray-500">
                      {{ event.report_generated_at ? 'Report generated on ' + formatDateShort(event.report_generated_at) : 'Report not yet generated' }}
                    </span>
                  </div>
                </div>
                
                <div class="flex gap-2">
                  <!-- Generate Button (only if no report exists) -->
                  <button
                    v-if="!event.report_generated_at"
                    @click="generateReport(event)"
                    :disabled="generating[event.id]"
                    class="flex-1 px-3 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm flex items-center justify-center gap-1"
                  >
                    <svg v-if="generating[event.id]" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Generate Report
                  </button>
                  
                  <!-- Regenerate Button (for existing reports) -->
                  <button
                    v-if="event.report_generated_at"
                    @click="regenerateReport(event)"
                    :disabled="regenerating[event.id]"
                    class="flex-1 px-3 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition text-sm flex items-center justify-center gap-1"
                  >
                    <svg v-if="regenerating[event.id]" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Regenerate
                  </button>
                  
                  <!-- View Button -->
                  <a
                    v-if="event.report_path"
                    :href="`/treasurer/collection-reports/${event.id}/view`"
                    target="_blank"
                    class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm flex items-center justify-center gap-1"
                  >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View
                  </a>
                  
                  <!-- Download Button -->
                  <button
                    v-if="event.report_path"
                    :href="`/treasurer/collection-reports/${event.id}/download`"
                    class="flex-1 px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm flex items-center justify-center gap-1"
                  >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="sortedEvents.length === 0" class="col-span-3">
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
              <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">No Reports Available</h3>
              <p class="text-gray-500 mb-4">No events found matching your filters.</p>
              <button @click="resetFilters" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                Clear Filters
              </button>
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
              <h4 class="font-semibold text-gray-800 mb-1">Collection Report Tips</h4>
              <ul class="text-sm text-gray-600 space-y-1">
                <li>• <strong>Generate</strong> - Create a new report for events without existing reports</li>
                <li>• <strong>Regenerate</strong> - Update existing reports with the latest payment data (includes new payments)</li>
                <li>• <strong>View</strong> - Preview the report in your browser</li>
                <li>• <strong>Download</strong> - Save the PDF report for official records</li>
                <li>• Reports include all student payment details with ID, Name, Amount, Status, and Receipt Number</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

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
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';

const props = defineProps({
  events: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

// State
const searchQuery = ref('');
const sortBy = ref('date');
const generating = ref({});
const regenerating = ref({});
const toast = ref({ show: false, message: '', type: 'success', bgClass: '' });
const filters = ref({
  date_from: props.filters?.date_from || '',
  date_to: props.filters?.date_to || '',
  collection_status: props.filters?.collection_status || ''
});

// Computed
const generatedCount = computed(() => {
  return props.events.filter(e => e.report_generated_at).length;
});

const totalCollected = computed(() => {
  return props.events.reduce((sum, e) => sum + (e.total_collected || 0), 0);
});

const totalStudents = computed(() => {
  return props.events.reduce((sum, e) => sum + (e.total_students || 0), 0);
});

const totalCollectedStudents = computed(() => {
  return props.events.reduce((sum, e) => sum + (e.paid_students || 0), 0);
});

const overallCollectionRate = computed(() => {
  const totalExpected = props.events.reduce((sum, e) => sum + (e.expected_total || 0), 0);
  if (totalExpected === 0) return 0;
  return Math.round((totalCollected.value / totalExpected) * 100);
});

const filteredEvents = computed(() => {
  let filtered = [...props.events];
  
  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(event => 
      event.event_name.toLowerCase().includes(query) ||
      (event.organization_name || '').toLowerCase().includes(query)
    );
  }
  
  // Date range filter
  if (filters.value.date_from) {
    filtered = filtered.filter(event => event.event_date >= filters.value.date_from);
  }
  if (filters.value.date_to) {
    filtered = filtered.filter(event => event.event_date <= filters.value.date_to);
  }
  
  // Collection status filter
  if (filters.value.collection_status) {
    filtered = filtered.filter(event => {
      if (filters.value.collection_status === 'completed') {
        return event.collection_rate === 100;
      } else if (filters.value.collection_status === 'partial') {
        return event.collection_rate > 0 && event.collection_rate < 100;
      } else if (filters.value.collection_status === 'pending') {
        return event.collection_rate === 0;
      }
      return true;
    });
  }
  
  return filtered;
});

const sortedEvents = computed(() => {
  const events = [...filteredEvents.value];
  
  switch(sortBy.value) {
    case 'name':
      return events.sort((a, b) => a.event_name.localeCompare(b.event_name));
    case 'rate':
      return events.sort((a, b) => (b.collection_rate || 0) - (a.collection_rate || 0));
    case 'collected':
      return events.sort((a, b) => (b.total_collected || 0) - (a.total_collected || 0));
    case 'date':
    default:
      return events.sort((a, b) => new Date(b.event_date) - new Date(a.event_date));
  }
});

// Methods
function formatNumber(num) {
  if (num === null || num === undefined) return '0';
  return Number(num).toLocaleString('en-US');
}

function formatDate(date) {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
}

function formatDateShort(date) {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  });
}

function getHeaderGradient(rate) {
  if (rate === 100) return 'from-emerald-500 to-teal-600';
  if (rate >= 50) return 'from-blue-500 to-cyan-600';
  if (rate > 0) return 'from-yellow-500 to-orange-600';
  return 'from-gray-500 to-gray-600';
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
  
  setTimeout(() => toast.value.show = false, 5000);
}

function applyFilters() {
  router.get(route('treasurer.reports.index'), filters.value, {
    preserveState: true,
    preserveScroll: true
  });
}

function resetFilters() {
  filters.value = {
    date_from: '',
    date_to: '',
    collection_status: ''
  };
  searchQuery.value = '';
  applyFilters();
}

function applySearch() {
  // Search is reactive, no need for additional logic
}

function clearSearch() {
  searchQuery.value = '';
}

async function generateReport(event) {
  if (generating.value[event.id]) return;
  
  generating.value[event.id] = true;
  showToast('Generating collection report...', 'info');
  
  try {
    const response = await axios.post(`/treasurer/collection-reports/${event.id}/generate`);
    
    if (response.data.success) {
      showToast('✅ Collection report generated successfully!', 'success');
      setTimeout(() => router.reload(), 1500);
    } else {
      showToast(response.data.error || 'Failed to generate report', 'error');
      generating.value[event.id] = false;
    }
  } catch (error) {
    console.error('Generate error:', error);
    const errorMessage = error.response?.data?.error || error.message || 'Failed to generate report';
    showToast(errorMessage, 'error');
    generating.value[event.id] = false;
  }
}

async function regenerateReport(event) {
  if (regenerating.value[event.id]) return;
  
  if (!confirm('Are you sure you want to regenerate this report? This will update the report with the latest payment data including any new payments recorded after the last generation.')) return;
  
  regenerating.value[event.id] = true;
  showToast('Regenerating report with latest payment data...', 'info');
  
  try {
    const response = await axios.post(`/treasurer/collection-reports/${event.id}/regenerate`);
    
    if (response.data.success) {
      showToast('✅ Report regenerated successfully with latest payment data!', 'success');
      setTimeout(() => router.reload(), 1500);
    } else {
      showToast(response.data.error || 'Failed to regenerate report', 'error');
      regenerating.value[event.id] = false;
    }
  } catch (error) {
    console.error('Regenerate error:', error);
    const errorMessage = error.response?.data?.error || error.message || 'Failed to regenerate report';
    showToast(errorMessage, 'error');
    regenerating.value[event.id] = false;
  }
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