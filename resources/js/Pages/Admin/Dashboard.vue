<template>
  <AdminLayout>
    <div class="space-y-8">
      <!-- Animated Gradient Background -->
      <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-teal-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-cyan-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
      </div>

      <!-- Welcome Hero Section -->
      <div class="relative overflow-hidden bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-800 rounded-2xl shadow-xl p-8 text-white">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
        
        <div class="relative z-10">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
              <div class="flex items-center gap-3 mb-3">
                <div class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium backdrop-blur-sm">
                  ✨ Welcome Back
                </div>
                <div class="px-3 py-1 bg-yellow-500/20 rounded-full text-sm font-medium backdrop-blur-sm">
                  {{ new Date().toLocaleDateString('en-US', { weekday: 'long' }) }}
                </div>
              </div>
              <h1 class="text-4xl font-bold mb-2">
                {{ adminName || 'Admin' }}
              </h1>
              <p class="text-emerald-100 text-base max-w-2xl">Manage evaluations, track performance, and gain AI-powered insights.</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-5 text-center border border-white/20">
              <p class="text-3xl font-bold">{{ currentDate }}</p>
              <p class="text-sm text-emerald-100 mt-1">{{ currentDay }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="(stat, index) in statCards" :key="index" 
             class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center" :class="stat.bgColor">
                <svg class="w-6 h-6" :class="stat.iconColor" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon" />
                </svg>
              </div>
              <div class="text-right">
                <span class="text-3xl font-bold text-gray-800">{{ stat.value }}</span>
                <div class="flex items-center gap-1 mt-1">
                  <svg v-if="stat.trend === 'up'" class="w-3 h-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                  </svg>
                  <span class="text-xs text-green-500">{{ stat.trendValue }}</span>
                </div>
              </div>
            </div>
            <h3 class="text-gray-600 font-semibold">{{ stat.title }}</h3>
            <p class="text-xs text-gray-400 mt-1">{{ stat.subtitle }}</p>
          </div>
        </div>
      </div>

      <!-- AI Insights Section -->
      <div>
        <div class="flex items-center justify-between mb-5">
          <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
              <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
              AI-Powered Evaluation Insights
            </h2>
            <p class="text-sm text-gray-500 mt-1">Select an evaluation to view detailed AI analysis</p>
          </div>
          <div class="px-3 py-1.5 bg-emerald-50 rounded-lg">
            <span class="text-xs font-medium text-emerald-600">Powered by Machine Learning</span>
          </div>
        </div>

        <!-- List of Evaluations with AI Insights -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
          <div class="divide-y divide-gray-100">
            <div v-for="insight in aiInsightsList" :key="insight.id" 
                 @click="openInsightModal(insight)"
                 class="p-5 hover:bg-gray-50 transition-all duration-200 cursor-pointer group">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-4 flex-1">
                  <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                    <span class="text-white font-bold text-lg">{{ insight.event_name?.charAt(0) || 'E' }}</span>
                  </div>
                  <div class="flex-1">
                    <h3 class="font-semibold text-gray-800 group-hover:text-emerald-600 transition">
                      {{ insight.event_name }}
                    </h3>
                    <p class="text-sm text-gray-500">{{ insight.evaluation_title }}</p>
                    <div class="flex flex-wrap items-center gap-4 mt-2">
                      <div class="flex items-center gap-1">
                        <div class="w-2 h-2 rounded-full" :class="getInsightDotColor(insight.overall?.predicted_satisfaction || 3.5)"></div>
                        <span class="text-xs text-gray-500">Satisfaction: {{ insight.overall?.predicted_satisfaction || 0 }}/5.0</span>
                      </div>
                      <div class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="text-xs text-gray-500">{{ insight.total_responses || 0 }} responses</span>
                      </div>
                      <div v-if="insight.date_insights?.length > 0" class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-xs text-gray-500">{{ insight.date_insights.length }} dates analyzed</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="flex items-center gap-4">
                  <div class="text-right">
                    <div class="flex items-center gap-1 mb-1">
                      <span class="text-xs text-gray-500">Response Rate:</span>
                      <span :class="getRateTextClass(insight.response_rate)" class="text-sm font-semibold">
                        {{ insight.response_rate }}%
                      </span>
                    </div>
                    <div class="w-24 bg-gray-200 rounded-full h-1.5">
                      <div class="h-full rounded-full" :class="getRateColorClass(insight.response_rate)" :style="{ width: insight.response_rate + '%' }"></div>
                    </div>
                  </div>
                  <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-500 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </div>
              </div>
            </div>

            <div v-if="aiInsightsList.length === 0" class="p-12 text-center">
              <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
              </div>
              <h3 class="text-base font-medium text-gray-900 mb-1">No AI Insights Available</h3>
              <p class="text-sm text-gray-500 max-w-md mx-auto">Generate AI insights for closed evaluations to see ML-powered analysis here.</p>
              <Link href="/admin/evaluations" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                View Evaluations
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md p-5">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-800">Evaluations Trend</h3>
            <span class="text-xs text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+12% vs last month</span>
          </div>
          <div style="height: 280px;">
            <canvas ref="evaluationsChart"></canvas>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-5">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-800">Responses Trend</h3>
            <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full">+8% vs last month</span>
          </div>
          <div style="height: 280px;">
            <canvas ref="responsesChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Evaluations -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between">
              <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <div class="w-6 h-6 bg-emerald-100 rounded-lg flex items-center justify-center">
                  <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                Recent Evaluations
              </h3>
              <Link href="/admin/evaluations" class="text-xs text-emerald-600 hover:text-emerald-700">
                View all →
              </Link>
            </div>
          </div>
          <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
            <div v-for="evaluation in (recentEvaluations || [])" :key="evaluation.id" 
                 class="p-4 hover:bg-gray-50 transition">
              <div class="flex justify-between items-start">
                <div>
                  <p class="font-medium text-gray-800 text-sm">{{ evaluation.title }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ evaluation.event_name }} • {{ evaluation.organization }}</p>
                </div>
                <div class="text-right">
                  <span :class="[
                    'px-2 py-0.5 text-xs font-medium rounded-full',
                    evaluation.status === 'active' ? 'bg-green-100 text-green-700' :
                    evaluation.status === 'closed' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700'
                  ]">
                    {{ evaluation.status }}
                  </span>
                  <p class="text-xs text-gray-400 mt-1">{{ evaluation.responses }}/{{ evaluation.expected }} ({{ evaluation.response_rate }}%)</p>
                </div>
              </div>
            </div>
            <div v-if="!recentEvaluations || recentEvaluations.length === 0" class="p-8 text-center text-gray-500 text-sm">
              No recent evaluations
            </div>
          </div>
        </div>

        <!-- Pending Requests -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between">
              <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <div class="w-6 h-6 bg-orange-100 rounded-lg flex items-center justify-center">
                  <svg class="w-3 h-3 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                Pending Requests
              </h3>
              <Link href="/admin/evaluations/create" class="text-xs text-emerald-600 hover:text-emerald-700">
                Create new →
              </Link>
            </div>
          </div>
          <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
            <div v-for="request in (pendingRequests || [])" :key="request.id" 
                 class="p-4 hover:bg-gray-50 transition">
              <div class="flex justify-between items-start">
                <div>
                  <p class="font-medium text-gray-800 text-sm">{{ request.title }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ request.event_name }} • {{ request.organization }}</p>
                </div>
                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">
                  Pending
                </span>
              </div>
              <div class="flex justify-between text-xs text-gray-400 mt-2">
                <span>Requested by: {{ request.requested_by }}</span>
                <span>{{ request.created_at }}</span>
              </div>
            </div>
            <div v-if="!pendingRequests || pendingRequests.length === 0" class="p-8 text-center text-gray-500 text-sm">
              No pending requests
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-xl shadow-md p-5">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
          <Link href="/admin/evaluations/create" class="text-center p-3 rounded-lg hover:bg-gray-50 transition group">
            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
              <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            <span class="text-xs text-gray-600">Create Evaluation</span>
          </Link>

          <Link href="/admin/organizations" class="text-center p-3 rounded-lg hover:bg-gray-50 transition group">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
              <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <span class="text-xs text-gray-600">Manage Organizations</span>
          </Link>

          <Link href="/admin/reports" class="text-center p-3 rounded-lg hover:bg-gray-50 transition group">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
              <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <span class="text-xs text-gray-600">View Reports</span>
          </Link>

          <Link href="/admin/profile" class="text-center p-3 rounded-lg hover:bg-gray-50 transition group">
            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
              <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <span class="text-xs text-gray-600">Update Profile</span>
          </Link>
        </div>
      </div>
    </div>

    <!-- AI Insights Modal - Clean Professional Design -->
    <Teleport to="body">
      <div v-if="selectedInsight" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative w-full max-w-4xl transform overflow-hidden rounded-xl bg-white shadow-2xl transition-all duration-300 scale-100">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-emerald-600 to-teal-600">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                  </div>
                  <div>
                    <h2 class="text-lg font-bold text-white">{{ selectedInsight.event_name }}</h2>
                    <p class="text-sm text-emerald-100">{{ selectedInsight.evaluation_title }}</p>
                  </div>
                </div>
                <button @click="closeModal" class="text-white/60 hover:text-white transition">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 px-6 pt-4 bg-gray-50">
              <nav class="flex gap-6">
                <button @click="modalTab = 'overall'" 
                        :class="modalTab === 'overall' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-3 py-2 text-sm border-b-2 font-medium transition">
                  Overall Analysis
                </button>
                <button v-if="selectedInsight.date_insights && selectedInsight.date_insights.length > 0" 
                        @click="modalTab = 'dates'" 
                        :class="modalTab === 'dates' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-3 py-2 text-sm border-b-2 font-medium transition">
                  Per Date Analysis
                  <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full bg-gray-200 text-gray-600">{{ selectedInsight.date_insights.length }}</span>
                </button>
              </nav>
            </div>

            <!-- Content -->
            <div class="px-6 py-6 max-h-[65vh] overflow-y-auto">
              <!-- Overall Analysis Tab -->
              <div v-if="modalTab === 'overall' && selectedInsight.overall" class="space-y-5">
                <!-- KPI Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                  <div class="bg-emerald-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Satisfaction</p>
                    <p class="text-xl font-bold text-emerald-600">{{ selectedInsight.overall.predicted_satisfaction }}/5.0</p>
                  </div>
                  <div class="bg-blue-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Success Rate</p>
                    <p class="text-xl font-bold text-blue-600">{{ ((selectedInsight.overall.success_probability || 0) * 100).toFixed(0) }}%</p>
                  </div>
                  <div class="bg-purple-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Response Rate</p>
                    <p class="text-xl font-bold text-purple-600">{{ ((selectedInsight.overall.response_rate || 0) * 100).toFixed(1) }}%</p>
                  </div>
                  <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Respondents</p>
                    <p class="text-xl font-bold text-gray-700">{{ selectedInsight.overall.total_respondents || 0 }}</p>
                  </div>
                </div>

                <!-- Category Breakdown -->
                <div v-if="selectedInsight.overall.category_breakdown && Object.keys(selectedInsight.overall.category_breakdown).length > 0" class="bg-gray-50 rounded-lg p-4">
                  <h3 class="font-semibold text-gray-800 text-sm mb-3">Category Performance</h3>
                  <div class="space-y-2">
                    <div v-for="(score, category) in selectedInsight.overall.category_breakdown" :key="category">
                      <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600">{{ category }}</span>
                        <span class="text-emerald-600 font-medium">{{ score }}/5.0</span>
                      </div>
                      <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="h-full rounded-full bg-emerald-500" :style="{ width: (score / 5 * 100) + '%' }"></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Strengths & Weaknesses -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                    <h3 class="font-semibold text-green-700 text-sm mb-2">Strengths</h3>
                    <ul class="space-y-1">
                      <li v-for="strength in selectedInsight.overall.strengths" :key="strength" class="text-xs text-green-600 flex items-start gap-1">
                        <span class="text-green-500">✓</span>
                        {{ strength }}
                      </li>
                      <li v-if="!selectedInsight.overall.strengths?.length" class="text-xs text-gray-500 italic">No strengths identified</li>
                    </ul>
                  </div>
                  <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                    <h3 class="font-semibold text-red-700 text-sm mb-2">Areas to Improve</h3>
                    <ul class="space-y-1">
                      <li v-for="weakness in selectedInsight.overall.weaknesses" :key="weakness" class="text-xs text-red-600 flex items-start gap-1">
                        <span class="text-red-500">•</span>
                        {{ weakness }}
                      </li>
                      <li v-if="!selectedInsight.overall.weaknesses?.length" class="text-xs text-gray-500 italic">No weaknesses identified</li>
                    </ul>
                  </div>
                </div>

                <!-- Sentiment Analysis -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h3 class="font-semibold text-gray-800 text-sm mb-3">Sentiment Analysis</h3>
                  <div class="grid grid-cols-3 gap-3 mb-3">
                    <div class="text-center">
                      <p class="text-lg font-bold text-green-600">{{ selectedInsight.overall.positive_percentage || 0 }}%</p>
                      <p class="text-xs text-gray-500">Positive</p>
                    </div>
                    <div class="text-center">
                      <p class="text-lg font-bold text-yellow-600">{{ selectedInsight.overall.neutral_percentage || 0 }}%</p>
                      <p class="text-xs text-gray-500">Neutral</p>
                    </div>
                    <div class="text-center">
                      <p class="text-lg font-bold text-red-600">{{ selectedInsight.overall.negative_percentage || 0 }}%</p>
                      <p class="text-xs text-gray-500">Negative</p>
                    </div>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-1.5">
                    <div class="h-full flex rounded-full overflow-hidden">
                      <div class="bg-green-500" :style="{ width: (selectedInsight.overall.positive_percentage || 0) + '%' }"></div>
                      <div class="bg-yellow-500" :style="{ width: (selectedInsight.overall.neutral_percentage || 0) + '%' }"></div>
                      <div class="bg-red-500" :style="{ width: (selectedInsight.overall.negative_percentage || 0) + '%' }"></div>
                    </div>
                  </div>
                </div>

                <!-- Recommendations -->
                <div v-if="selectedInsight.overall.recommendations?.length" class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                  <h3 class="font-semibold text-blue-700 text-sm mb-3">AI Recommendations</h3>
                  <div class="space-y-2">
                    <div v-for="(rec, idx) in selectedInsight.overall.recommendations" :key="idx" 
                         class="bg-white rounded-lg p-3 border border-blue-200">
                      <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 text-xs rounded-full" :class="{
                          'bg-red-100 text-red-700': rec.priority === 'high',
                          'bg-yellow-100 text-yellow-700': rec.priority === 'medium',
                          'bg-green-100 text-green-700': rec.priority === 'low'
                        }">
                          {{ rec.priority?.toUpperCase() || 'MEDIUM' }}
                        </span>
                        <span class="text-xs text-gray-500">{{ rec.category }}</span>
                      </div>
                      <p class="text-sm font-medium text-gray-800">{{ rec.title }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Per Date Analysis Tab -->
              <div v-if="modalTab === 'dates' && selectedInsight.date_insights && selectedInsight.date_insights.length > 0">
                <div class="space-y-4">
                  <div v-for="(dateInsight, idx) in selectedInsight.date_insights" :key="idx" class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                      <h3 class="font-semibold text-gray-800">{{ dateInsight.formatted_date }}</h3>
                      <span class="text-xs text-gray-500">{{ dateInsight.total_respondents || 0 }} responses</span>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-2 mb-3">
                      <div class="bg-white rounded p-2 text-center">
                        <p class="text-xs text-gray-500">Satisfaction</p>
                        <p class="text-sm font-bold text-emerald-600">{{ dateInsight.predicted_satisfaction }}/5.0</p>
                      </div>
                      <div class="bg-white rounded p-2 text-center">
                        <p class="text-xs text-gray-500">Success Rate</p>
                        <p class="text-sm font-bold text-blue-600">{{ ((dateInsight.success_probability || 0) * 100).toFixed(0) }}%</p>
                      </div>
                      <div class="bg-white rounded p-2 text-center">
                        <p class="text-xs text-gray-500">Response Rate</p>
                        <p class="text-sm font-bold text-purple-600">{{ ((dateInsight.response_rate || 0) * 100).toFixed(1) }}%</p>
                      </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 mb-3">
                      <div class="bg-green-50 rounded p-2">
                        <p class="text-xs font-semibold text-green-600 mb-1">Strengths</p>
                        <ul class="text-xs text-green-600 space-y-0.5">
                          <li v-for="s in dateInsight.strengths.slice(0, 2)" :key="s">✓ {{ s }}</li>
                        </ul>
                      </div>
                      <div class="bg-red-50 rounded p-2">
                        <p class="text-xs font-semibold text-red-600 mb-1">Areas to Improve</p>
                        <ul class="text-xs text-red-600 space-y-0.5">
                          <li v-for="w in dateInsight.weaknesses.slice(0, 2)" :key="w">• {{ w }}</li>
                        </ul>
                      </div>
                    </div>
                    
                    <div class="bg-white rounded p-2">
                      <div class="flex justify-between items-center mb-1">
                        <p class="text-xs font-medium text-gray-600">Sentiment</p>
                        <div class="flex gap-2 text-xs">
                          <span class="text-green-600">+{{ dateInsight.sentiment_analysis?.positive_percentage || 0 }}%</span>
                          <span class="text-yellow-600">○{{ dateInsight.sentiment_analysis?.neutral_percentage || 0 }}%</span>
                          <span class="text-red-600">-{{ dateInsight.sentiment_analysis?.negative_percentage || 0 }}%</span>
                        </div>
                      </div>
                      <div class="w-full bg-gray-200 rounded-full h-1">
                        <div class="h-full flex rounded-full overflow-hidden">
                          <div class="bg-green-500" :style="{ width: (dateInsight.sentiment_analysis?.positive_percentage || 0) + '%' }"></div>
                          <div class="bg-yellow-500" :style="{ width: (dateInsight.sentiment_analysis?.neutral_percentage || 0) + '%' }"></div>
                          <div class="bg-red-500" :style="{ width: (dateInsight.sentiment_analysis?.negative_percentage || 0) + '%' }"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                  <span class="text-xs text-gray-500">Analyzed: {{ formatDate(selectedInsight.analyzed_at) }}</span>
                </div>
                <button @click="closeModal" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition text-sm">
                  Close
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Chart from 'chart.js/auto';

const props = defineProps({
  stats: { type: Object, default: () => ({}) },
  recentEvaluations: { type: Array, default: () => [] },
  pendingRequests: { type: Array, default: () => [] },
  monthlyEvaluations: { type: Object, default: () => ({}) },
  monthlyResponses: { type: Object, default: () => ({}) },
  statusDistribution: { type: Object, default: () => ({}) },
  topOrganizations: { type: Array, default: () => [] },
  aiInsightsList: { type: Array, default: () => [] },
  adminName: { type: String, default: 'Admin' }
});

const selectedInsight = ref(null);
const modalTab = ref('overall');

function openInsightModal(insight) {
  selectedInsight.value = insight;
  modalTab.value = 'overall';
}

function closeModal() {
  selectedInsight.value = null;
}

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

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

function getInsightDotColor(score) {
  if (score >= 4) return 'bg-green-500';
  if (score >= 3) return 'bg-yellow-500';
  return 'bg-red-500';
}

const statCards = computed(() => [
  {
    title: 'Total Evaluations',
    value: props.stats?.total_evaluations || 0,
    subtitle: `${props.stats?.active_evaluations || 0} active, ${props.stats?.closed_evaluations || 0} closed`,
    icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    bgColor: 'bg-blue-100',
    iconColor: 'text-blue-600',
    trend: 'up',
    trendValue: '+12%'
  },
  {
    title: 'Organizations',
    value: props.stats?.total_organizations || 0,
    subtitle: 'Registered organizations',
    icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
    bgColor: 'bg-purple-100',
    iconColor: 'text-purple-600',
    trend: 'up',
    trendValue: '+5%'
  },
  {
    title: 'Total Responses',
    value: props.stats?.total_responses || 0,
    subtitle: 'Collected from students',
    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    bgColor: 'bg-green-100',
    iconColor: 'text-green-600',
    trend: 'up',
    trendValue: '+8%'
  }
]);

const currentDate = computed(() => {
  return new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
});

const currentDay = computed(() => {
  return new Date().toLocaleDateString('en-US', { weekday: 'long' });
});

const evaluationsChart = ref(null);
const responsesChart = ref(null);
let evaluationsChartInstance = null;
let responsesChartInstance = null;

onMounted(() => {
  if (evaluationsChart.value && props.monthlyEvaluations && Object.keys(props.monthlyEvaluations).length > 0) {
    const labels = Object.keys(props.monthlyEvaluations);
    const data = Object.values(props.monthlyEvaluations);
    
    evaluationsChartInstance = new Chart(evaluationsChart.value, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Evaluations',
          data: data,
          borderColor: 'rgb(16, 185, 129)',
          backgroundColor: 'rgba(16, 185, 129, 0.05)',
          tension: 0.4,
          fill: true,
          pointBackgroundColor: 'rgb(16, 185, 129)',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 3,
          pointHoverRadius: 5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#f0f0f0' } } }
      }
    });
  }

  if (responsesChart.value && props.monthlyResponses && Object.keys(props.monthlyResponses).length > 0) {
    const labels = Object.keys(props.monthlyResponses);
    const data = Object.values(props.monthlyResponses);
    
    responsesChartInstance = new Chart(responsesChart.value, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Responses',
          data: data,
          backgroundColor: 'rgba(59, 130, 246, 0.6)',
          borderRadius: 6,
          barPercentage: 0.6,
          categoryPercentage: 0.8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#f0f0f0' } } }
      }
    });
  }
});
</script>

<style scoped>
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}
</style>