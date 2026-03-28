<template>
  <AdminLayout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Section -->
        <div class="mb-10">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div>
                  <h1 class="text-4xl font-bold bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 bg-clip-text text-transparent">
                    Evaluation Management
                  </h1>
                  <p class="text-gray-500 mt-1">Create, manage, and analyze event evaluations with AI-powered insights</p>
                </div>
              </div>
            </div>
            
            <div class="flex items-center gap-3">
              <div class="flex -space-x-2">
                <div class="w-8 h-8 rounded-full bg-emerald-100 border-2 border-white flex items-center justify-center">
                  <span class="text-xs font-bold text-emerald-600">AI</span>
                </div>
                <div class="w-8 h-8 rounded-full bg-purple-100 border-2 border-white flex items-center justify-center">
                  <span class="text-xs font-bold text-purple-600">ML</span>
                </div>
                <div class="w-8 h-8 rounded-full bg-blue-100 border-2 border-white flex items-center justify-center">
                  <span class="text-xs font-bold text-blue-600">NLP</span>
                </div>
              </div>
              <div class="px-3 py-1.5 bg-emerald-50 rounded-full">
                <span class="text-xs font-medium text-emerald-600">Real-time Analytics</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Search Bar - Visible for ALL tabs -->
        <div class="mb-6">
          <div class="relative max-w-md">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input 
              v-model="searchQuery"
              type="text"
              :placeholder="activeTab === 'requests' ? 'Search by event name, organization, or requester...' : 'Search by event name, evaluation title, or organization...'"
              class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white/80 backdrop-blur-sm"
              @keyup.enter="performSearch"
            >
          </div>
        </div>

        <!-- Modern Tabs Navigation -->
        <div class="mb-8">
          <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-1.5 border border-gray-100">
            <div class="flex flex-wrap gap-1">
              <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="activeTab = tab.key"
                class="relative group px-6 py-3 rounded-xl font-medium text-sm transition-all duration-200"
                :class="activeTab === tab.key 
                  ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg' 
                  : 'text-gray-600 hover:bg-gray-100'"
              >
                <div class="flex items-center gap-2">
                  <component :is="tab.icon" class="w-4 h-4" />
                  <span>{{ tab.label }}</span>
                  <span 
                    v-if="tab.key === 'requests' && filteredPendingRequests.length > 0"
                    class="ml-1 px-2 py-0.5 text-xs rounded-full bg-white/20 text-white animate-pulse"
                  >
                    {{ filteredPendingRequests.length }}
                  </span>
                  <span 
                    v-else-if="tab.key === 'all'"
                    class="ml-1 px-2 py-0.5 text-xs rounded-full bg-white/20"
                  >
                    {{ filteredEvaluations.length }}
                  </span>
                  <span 
                    v-else-if="tab.key === 'draft'"
                    class="ml-1 px-2 py-0.5 text-xs rounded-full bg-white/20"
                  >
                    {{ draftCount }}
                  </span>
                  <span 
                    v-else-if="tab.key === 'active'"
                    class="ml-1 px-2 py-0.5 text-xs rounded-full bg-white/20"
                  >
                    {{ activeCount }}
                  </span>
                  <span 
                    v-else-if="tab.key === 'closed'"
                    class="ml-1 px-2 py-0.5 text-xs rounded-full bg-white/20"
                  >
                    {{ closedCount }}
                  </span>
                </div>
              </button>
            </div>
          </div>
        </div>

        <!-- Pending Requests Tab with Search -->
        <div v-if="activeTab === 'requests'" class="space-y-6">
          <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="relative overflow-hidden">
              <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-purple-500/5 to-pink-500/5 rounded-full blur-3xl"></div>
              <div class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-emerald-500/5 to-teal-500/5 rounded-full blur-3xl"></div>
              
              <div class="relative px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-purple-50/50 via-white to-pink-50/50">
                <div class="flex items-center justify-between">
                  <div>
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                      <span class="w-3 h-3 bg-purple-500 rounded-full animate-pulse"></span>
                      Pending Evaluation Requests
                    </h2>
                    <p class="text-gray-500 mt-1">Review and process evaluation requests from organizations</p>
                  </div>
                  <div class="px-4 py-2 bg-purple-100 rounded-2xl">
                    <span class="text-sm font-bold text-purple-600">{{ filteredPendingRequests.length }} pending requests</span>
                  </div>
                </div>
              </div>
              
              <div v-if="filteredPendingRequests.length > 0" class="divide-y divide-gray-100">
                <div v-for="request in filteredPendingRequests" :key="request.id" 
                     class="group p-6 hover:bg-gradient-to-r hover:from-purple-50/30 hover:to-transparent transition-all duration-500">
                  <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="flex-1">
                      <div class="flex flex-wrap items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 shadow-md flex items-center justify-center">
                          <span class="text-white font-bold text-lg">{{ request.organization_name?.charAt(0) || 'O' }}</span>
                        </div>
                        <div>
                          <h3 class="text-xl font-bold text-gray-800 group-hover:text-purple-700 transition-colors">
                            {{ request.title }}
                          </h3>
                          <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="px-2.5 py-1 bg-purple-100 text-purple-700 rounded-lg text-xs font-medium flex items-center gap-1">
                              <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                              </svg>
                              {{ request.organization_name }}
                            </span>
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs flex items-center gap-1">
                              <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                              </svg>
                              {{ request.event_name }}
                            </span>
                          </div>
                        </div>
                      </div>
                      
                      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-xl">
                          <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                          </svg>
                          <div>
                            <p class="text-xs text-gray-500">Date</p>
                            <p class="text-sm font-medium text-gray-700">{{ request.activity_date }}</p>
                          </div>
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-xl">
                          <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                          </svg>
                          <div>
                            <p class="text-xs text-gray-500">Venue</p>
                            <p class="text-sm font-medium text-gray-700">{{ request.venue }}</p>
                          </div>
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-xl">
                          <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                          </svg>
                          <div>
                            <p class="text-xs text-gray-500">Speaker</p>
                            <p class="text-sm font-medium text-gray-700">{{ request.speaker_name }}</p>
                          </div>
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-xl">
                          <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          <div>
                            <p class="text-xs text-gray-500">Food</p>
                            <p class="text-sm font-medium text-gray-700">{{ request.has_food ? 'With Food' : 'No Food' }}</p>
                          </div>
                        </div>
                      </div>
                      
                      <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="text-xs font-medium text-gray-500">Topics:</span>
                        <div class="flex flex-wrap gap-1.5">
                          <span v-for="topic in request.topics" :key="topic" 
                                class="px-2.5 py-1 bg-gradient-to-r from-gray-50 to-gray-100 text-gray-700 rounded-lg text-xs font-medium">
                            {{ topic }}
                          </span>
                        </div>
                      </div>
                      
                      <p class="text-xs text-gray-400 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Requested by: {{ request.requested_by }} | {{ request.created_at }}
                      </p>
                    </div>
                    
                    <div class="flex-shrink-0">
                      <Link :href="`/admin/evaluations/create?request_id=${request.id}`"
                        class="group/btn inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all duration-300 shadow-md hover:shadow-lg"
                      >
                        <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Create Form</span>
                        <svg class="w-4 h-4 transition-transform group-hover/btn:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else-if="searchQuery && pendingRequestsData.length > 0" class="p-16 text-center">
                <div class="relative inline-block">
                  <div class="w-28 h-28 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-14 h-14 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">No Matching Requests</h3>
                <p class="text-gray-500 max-w-md mx-auto">No pending requests match "{{ searchQuery }}". Try a different search term.</p>
                <button 
                  @click="clearSearch"
                  class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition"
                >
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  Clear Search
                </button>
              </div>
              
              <div v-else class="p-16 text-center">
                <div class="relative inline-block">
                  <div class="w-28 h-28 mx-auto bg-gradient-to-br from-purple-100 to-pink-100 rounded-full flex items-center justify-center mb-4 animate-pulse-slow">
                    <svg class="w-14 h-14 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full animate-ping"></div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">All Caught Up!</h3>
                <p class="text-gray-500 max-w-md mx-auto">No pending evaluation requests at the moment. New requests will appear here when organizations submit them.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Evaluations Tabs (All, Draft, Active, Closed) with Search -->
        <div v-else>
          <!-- Stats Cards for Quick Overview -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl shadow-md p-4 border-l-4 border-emerald-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-500">Total Responses</p>
                  <p class="text-2xl font-bold text-gray-800">{{ totalResponses }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-4 border-l-4 border-purple-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-500">Total Expected</p>
                  <p class="text-2xl font-bold text-gray-800">{{ totalExpected }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-4 border-l-4 border-blue-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-500">Active Evaluations</p>
                  <p class="text-2xl font-bold text-blue-600">{{ activeCount }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-4 border-l-4 border-indigo-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-500">Avg. Response Rate</p>
                  <p class="text-2xl font-bold text-indigo-600">{{ avgResponseRate }}%</p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Evaluations Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="evaluation in filteredEvaluations" 
              :key="evaluation.id" 
              class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden cursor-pointer transform hover:-translate-y-2"
              @click="goToEvaluation(evaluation.id)"
            >
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-emerald-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
              
              <div class="relative h-1.5" :class="{
                'bg-gray-400': evaluation.status === 'draft',
                'bg-gradient-to-r from-green-500 to-emerald-500 animate-pulse': evaluation.status === 'active',
                'bg-gradient-to-r from-blue-500 to-indigo-500': evaluation.status === 'closed'
              }"></div>
              
              <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-md flex items-center justify-center">
                        <span class="text-white font-bold text-lg">{{ evaluation.title?.charAt(0) || 'E' }}</span>
                      </div>
                      <div>
                        <h3 class="font-bold text-gray-800 line-clamp-1 group-hover:text-emerald-600 transition-colors">
                          {{ evaluation.title }}
                        </h3>
                        <p class="text-xs text-gray-500 line-clamp-1">{{ evaluation.event_name }}</p>
                      </div>
                    </div>
                  </div>
                  <span class="px-3 py-1 text-xs font-bold rounded-full shadow-sm backdrop-blur-sm" :class="{
                    'bg-gray-100 text-gray-700 border border-gray-200': evaluation.status === 'draft',
                    'bg-green-100 text-green-700 border border-green-200 animate-pulse': evaluation.status === 'active',
                    'bg-blue-100 text-blue-700 border border-blue-200': evaluation.status === 'closed'
                  }">
                    {{ evaluation.status }}
                  </span>
                </div>

                <div class="flex flex-wrap gap-2 mb-4">
                  <span class="text-xs px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full">
                    {{ getFormTypeName(evaluation.form_type) }}
                  </span>
                  <span class="text-xs px-2.5 py-1 bg-purple-50 text-purple-600 rounded-full">
                    {{ evaluation.organization_name }}
                  </span>
                </div>

                <!-- Response Stats with Progress Bar -->
                <div class="mb-4">
                  <div class="flex justify-between items-center mb-2">
                    <div class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                      </svg>
                      <span class="text-sm font-semibold text-gray-700">{{ evaluation.responses_count }} / {{ evaluation.expected_count }}</span>
                    </div>
                    <span :class="getRateTextClass(evaluation.response_rate)" class="text-sm font-bold">
                      {{ evaluation.response_rate }}%
                    </span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-full rounded-full transition-all" 
                         :class="getRateColorClass(evaluation.response_rate)"
                         :style="{ width: evaluation.response_rate + '%' }">
                    </div>
                  </div>
                  <div class="flex justify-between text-xs text-gray-400 mt-1">
                    <span>{{ evaluation.students_count }} students × {{ evaluation.number_of_dates || 1 }} days</span>
                    <span>{{ evaluation.guests_count }} guests</span>
                  </div>
                  <p class="text-xs text-gray-400 mt-1 text-center">
                    Expected = {{ evaluation.students_count }} × {{ evaluation.number_of_dates || 1 }} + {{ evaluation.guests_count }} = {{ evaluation.expected_count }}
                  </p>
                </div>

                <div class="grid grid-cols-2 gap-3 my-4">
                  <div class="text-center p-2.5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl">
                    <div class="flex items-center justify-center gap-1 mb-1">
                      <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                      </svg>
                      <p class="text-sm font-bold text-gray-700">{{ evaluation.created_at }}</p>
                    </div>
                    <p class="text-xs text-gray-500">Created</p>
                  </div>
                  <div class="text-center p-2.5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl">
                    <div class="flex items-center justify-center gap-1 mb-1">
                      <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <p class="text-sm font-bold text-gray-700">{{ evaluation.response_rate }}%</p>
                    </div>
                    <p class="text-xs text-gray-500">Response Rate</p>
                  </div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                  <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full" :class="{
                      'bg-gray-400': evaluation.status === 'draft',
                      'bg-green-500 animate-pulse': evaluation.status === 'active',
                      'bg-blue-500': evaluation.status === 'closed'
                    }"></div>
                    <span class="text-xs text-gray-500">{{ getStatusMessage(evaluation.status) }}</span>
                  </div>
                  <span class="text-emerald-600 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    {{ evaluation.status === 'draft' ? 'Configure Form' : 'View Insights' }}
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </span>
                </div>
              </div>
            </div>

            <div v-if="filteredEvaluations.length === 0" class="col-span-full">
              <div class="bg-white rounded-3xl shadow-xl p-16 text-center">
                <div class="relative inline-block">
                  <div class="w-32 h-32 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6 animate-bounce-slow">
                    <svg class="w-16 h-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <div class="absolute -top-2 -right-2 w-8 h-8 bg-emerald-500 rounded-full animate-ping"></div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">No Evaluations Found</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">{{ getEmptyMessage() }}</p>
                <button 
                  @click="activeTab = 'requests'"
                  class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                >
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  View Pending Requests
                </button>
              </div>
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
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import axios from 'axios';

const props = defineProps({
  evaluations: {
    type: Array,
    default: () => []
  },
  pendingRequestsCount: {
    type: Number,
    default: 0
  },
  search: {
    type: String,
    default: null
  }
});

const activeTab = ref('requests');
const pendingRequestsData = ref([]);
const searchQuery = ref(props.search || '');
const toast = ref({ show: false, message: '', type: 'success', bgClass: '' });

const tabs = [
  { key: 'requests', label: 'Pending Requests', icon: 'RequestsIcon' },
  { key: 'all', label: 'All Evaluations', icon: 'AllIcon' },
  { key: 'draft', label: 'Draft', icon: 'DraftIcon' },
  { key: 'active', label: 'Active', icon: 'ActiveIcon' },
  { key: 'closed', label: 'Closed', icon: 'ClosedIcon' }
];

const draftCount = computed(() => props.evaluations.filter(e => e.status === 'draft').length);
const activeCount = computed(() => props.evaluations.filter(e => e.status === 'active').length);
const closedCount = computed(() => props.evaluations.filter(e => e.status === 'closed').length);

const totalResponses = computed(() => {
  return props.evaluations.reduce((sum, e) => sum + (e.responses_count || 0), 0);
});

const totalExpected = computed(() => {
  return props.evaluations.reduce((sum, e) => sum + (e.expected_count || 0), 0);
});

const avgResponseRate = computed(() => {
  if (props.evaluations.length === 0) return 0;
  const total = props.evaluations.reduce((sum, e) => sum + (e.response_rate || 0), 0);
  return Math.round(total / props.evaluations.length);
});

// Filtered pending requests based on search
const filteredPendingRequests = computed(() => {
  if (!searchQuery.value) return pendingRequestsData.value;
  
  const query = searchQuery.value.toLowerCase();
  return pendingRequestsData.value.filter(request => 
    request.event_name?.toLowerCase().includes(query) ||
    request.organization_name?.toLowerCase().includes(query) ||
    request.requested_by?.toLowerCase().includes(query) ||
    request.title?.toLowerCase().includes(query)
  );
});

// Filtered evaluations based on active tab and search
const filteredEvaluations = computed(() => {
  let evaluations = props.evaluations;
  
  if (activeTab.value !== 'all') {
    evaluations = evaluations.filter(e => e.status === activeTab.value);
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    evaluations = evaluations.filter(e => 
      e.event_name?.toLowerCase().includes(query) ||
      e.title?.toLowerCase().includes(query) ||
      e.organization_name?.toLowerCase().includes(query)
    );
  }
  
  return evaluations;
});

function getRateColorClass(rate) {
  if (rate >= 75) return 'bg-green-500';
  if (rate >= 50) return 'bg-yellow-500';
  if (rate >= 25) return 'bg-orange-500';
  return 'bg-red-500';
}

function getRateTextClass(rate) {
  if (rate >= 75) return 'text-green-600';
  if (rate >= 50) return 'text-yellow-600';
  if (rate >= 25) return 'text-orange-600';
  return 'text-red-600';
}

function performSearch() {
  // For pending requests, we filter client-side
  // For evaluations, we also filter client-side but could also call server
  if (activeTab.value !== 'requests') {
    router.get('/admin/evaluations', { search: searchQuery.value }, {
      preserveState: true,
      replace: true
    });
  }
}

function clearSearch() {
  searchQuery.value = '';
  if (activeTab.value !== 'requests') {
    router.get('/admin/evaluations', {}, {
      preserveState: true,
      replace: true
    });
  }
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

onMounted(() => {
  if (activeTab.value === 'requests') {
    fetchPendingRequests();
  }
});

async function fetchPendingRequests() {
  try {
    const response = await axios.get('/admin/evaluations/pending-requests');
    pendingRequestsData.value = response.data;
  } catch (error) {
    console.error('Failed to fetch pending requests:', error);
    pendingRequestsData.value = [];
  }
}

function getFormTypeName(formType) {
  const types = {
    type1: '7 Quality Dimension',
    type2: '5 Quality Dimension',
    type3: '8 Quality Dimension',
    type4: '6 Quality Dimension (With Speaker)',
    type5: '6 Quality Dimension (With Food)'
  };
  return types[formType] || formType;
}

function getStatusMessage(status) {
  switch(status) {
    case 'draft': return 'Ready for activation';
    case 'active': return 'Collecting responses';
    case 'closed': return 'Analysis ready';
    default: return '';
  }
}

function getEmptyMessage() {
  if (searchQuery.value) {
    return `No evaluations match "${searchQuery.value}". Try a different search term.`;
  }
  switch(activeTab.value) {
    case 'draft': return 'No draft evaluations. Go to Pending Requests to create new evaluations.';
    case 'active': return 'No active evaluations. Activate a draft evaluation to start collecting responses.';
    case 'closed': return 'No closed evaluations. Close active evaluations to generate insights.';
    default: return 'Create your first evaluation from the pending requests tab.';
  }
}

function goToEvaluation(id) {
  router.visit(`/admin/evaluations/${id}`);
}

// Debounced search for evaluations only
let searchTimeout;
watch(searchQuery, (newVal) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    if (activeTab.value !== 'requests') {
      performSearch();
    }
  }, 500);
});

watch(activeTab, (newTab) => {
  if (newTab === 'requests') {
    fetchPendingRequests();
    // Clear search when switching to requests? Optional
    // searchQuery.value = '';
  }
});
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@keyframes bounce-slow {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-bounce-slow {
  animation: bounce-slow 3s ease-in-out infinite;
}

@keyframes pulse-slow {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(0.98);
  }
}

.animate-pulse-slow {
  animation: pulse-slow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>