<template>
  <AdminLayout>
    <div class="space-y-8">
      <!-- Animated Gradient Background Overlay -->
      <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-yellow-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
      </div>

      <!-- Welcome Hero Section with 3D Effect -->
      <div class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700 rounded-3xl shadow-2xl p-10 text-white transform hover:scale-[1.01] transition-all duration-500">
        <div class="absolute inset-0 bg-black/20 backdrop-blur-sm"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -mr-48 -mt-48 animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -ml-48 -mb-48 animate-pulse animation-delay-2000"></div>
        
        <div class="relative z-10">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-3">
              <div class="flex items-center gap-3">
                <div class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-sm font-medium animate-pulse">
                  ✨ Welcome Back
                </div>
                <div class="px-3 py-1 bg-yellow-500/30 backdrop-blur-md rounded-full text-sm font-medium">
                  {{ new Date().toLocaleDateString('en-US', { weekday: 'long' }) }}
                </div>
              </div>
              <h1 class="text-5xl font-bold tracking-tight bg-gradient-to-r from-white to-emerald-200 bg-clip-text text-transparent">
                {{ adminName || 'Admin' }}
              </h1>
              <p class="text-emerald-100 text-lg max-w-2xl">Your comprehensive dashboard for managing evaluations, tracking performance, and gaining actionable insights.</p>
            </div>
            <div class="bg-white/20 backdrop-blur-md rounded-2xl p-6 text-center border border-white/30 shadow-xl">
              <p class="text-4xl font-bold">{{ currentDate }}</p>
              <p class="text-sm text-emerald-100 mt-1">{{ currentDay }}</p>
              <div class="mt-3 flex justify-center gap-1">
                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
              </div>
            </div>
          </div>
          
          <!-- Quick Stats Badges -->
          <div class="flex flex-wrap gap-4 mt-8">
            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2">
              <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
              <span class="text-sm">System Active</span>
            </div>
            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="text-sm">Real-time Updates</span>
            </div>
            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="text-sm">AI-Powered Analytics</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards - Animated Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="(stat, index) in statCards" :key="index" 
             class="group relative overflow-hidden bg-white/80 backdrop-blur-md rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 cursor-pointer"
             :style="{ animationDelay: `${index * 0.1}s` }">
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="p-3 rounded-xl shadow-lg transition-all duration-300 group-hover:scale-110 group-hover:rotate-6" 
                   :class="stat.gradient">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
            <h3 class="text-gray-600 font-semibold text-lg">{{ stat.title }}</h3>
            <p class="text-sm text-gray-400 mt-1">{{ stat.subtitle }}</p>
          </div>
          <div class="h-1 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </div>
      </div>

      <!-- Advanced Analytics Row -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Satisfaction Gauge Chart -->
        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl p-6 border border-white/20 hover:shadow-2xl transition-all duration-300">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <div class="p-2 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-lg shadow-lg">
                  <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                </div>
                Satisfaction Score
              </h3>
              <p class="text-sm text-gray-500 mt-1">Overall participant satisfaction rating</p>
            </div>
            <div class="text-right">
              <div class="text-3xl font-bold text-emerald-600">{{ avgSatisfaction || 0 }}<span class="text-lg text-gray-400">/5.0</span></div>
              <div class="text-xs text-gray-500 mt-1">Excellent</div>
            </div>
          </div>
          <div class="relative pt-4">
            <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full bg-gradient-to-r from-red-500 via-yellow-500 to-emerald-500 rounded-full transition-all duration-1000" 
                   :style="{ width: ((avgSatisfaction || 0) / 5 * 100) + '%' }"></div>
            </div>
            <div class="flex justify-between mt-2 text-xs text-gray-500">
              <span>Poor</span>
              <span>Fair</span>
              <span>Good</span>
              <span>Excellent</span>
            </div>
          </div>
          <div class="grid grid-cols-3 gap-4 mt-6">
            <div class="text-center p-3 bg-gray-50 rounded-xl">
              <p class="text-2xl font-bold text-gray-800">{{ positivePercentage }}%</p>
              <p class="text-xs text-gray-500">Positive</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-xl">
              <p class="text-2xl font-bold text-gray-800">{{ neutralPercentage }}%</p>
              <p class="text-xs text-gray-500">Neutral</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-xl">
              <p class="text-2xl font-bold text-gray-800">{{ negativePercentage }}%</p>
              <p class="text-xs text-gray-500">Negative</p>
            </div>
          </div>
        </div>

        <!-- Response Rate Circle -->
        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl p-6 border border-white/20 hover:shadow-2xl transition-all duration-300">
          <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center gap-2">
            <div class="p-2 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg shadow-lg">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            Response Rate
          </h3>
          <div class="flex justify-center items-center py-4">
            <div class="relative w-48 h-48">
              <svg class="w-full h-full transform -rotate-90">
                <circle cx="96" cy="96" r="88" stroke="currentColor" stroke-width="12" fill="none" class="text-gray-200"/>
                <circle cx="96" cy="96" r="88" stroke="currentColor" stroke-width="12" fill="none" 
                        stroke-dasharray="553" :stroke-dashoffset="553 - (553 * (responseRate || 0) / 100)"
                        class="text-emerald-500 transition-all duration-1000"
                        stroke-linecap="round"/>
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-4xl font-bold text-gray-800">{{ responseRate || 0 }}%</span>
                <span class="text-xs text-gray-500">Response Rate</span>
              </div>
            </div>
          </div>
          <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">{{ stats?.total_responses || 0 }} responses out of {{ stats?.total_events || 0 }} events</p>
          </div>
        </div>
      </div>

      <!-- Charts Section with Glass Effect -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl p-6 border border-white/20 hover:shadow-2xl transition-all duration-300 group">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
              <div class="p-2 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-lg shadow-lg group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
              </div>
              Evaluations Trend
            </h3>
            <div class="flex gap-2">
              <span class="text-xs px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full">+12% vs last month</span>
            </div>
          </div>
          <div style="height: 320px;">
            <canvas ref="evaluationsChart"></canvas>
          </div>
        </div>

        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl p-6 border border-white/20 hover:shadow-2xl transition-all duration-300 group">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
              <div class="p-2 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg shadow-lg group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </div>
              Responses Trend
            </h3>
            <div class="flex gap-2">
              <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-full">+8% vs last month</span>
            </div>
          </div>
          <div style="height: 320px;">
            <canvas ref="responsesChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Advanced Analytics Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Status Distribution Card -->
        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl p-6 border border-white/20 col-span-1">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <div class="p-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg shadow-lg">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            Evaluation Status
          </h3>
          <div style="height: 220px;">
            <canvas ref="statusChart"></canvas>
          </div>
          <div class="flex justify-around mt-4">
            <div class="text-center">
              <div class="w-10 h-10 mx-auto bg-emerald-100 rounded-full flex items-center justify-center mb-2">
                <span class="text-emerald-600 font-bold">{{ statusDistribution?.Active || 0 }}</span>
              </div>
              <p class="text-xs text-gray-600">Active</p>
            </div>
            <div class="text-center">
              <div class="w-10 h-10 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-2">
                <span class="text-blue-600 font-bold">{{ statusDistribution?.Closed || 0 }}</span>
              </div>
              <p class="text-xs text-gray-600">Closed</p>
            </div>
            <div class="text-center">
              <div class="w-10 h-10 mx-auto bg-yellow-100 rounded-full flex items-center justify-center mb-2">
                <span class="text-yellow-600 font-bold">{{ statusDistribution?.Draft || 0 }}</span>
              </div>
              <p class="text-xs text-gray-600">Draft</p>
            </div>
          </div>
        </div>

        <!-- Top Organizations Card -->
        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl p-6 border border-white/20 col-span-2">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <div class="p-2 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg shadow-lg">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            Top Performing Organizations
          </h3>
          <div class="space-y-4">
            <div v-for="org in (topOrganizations || [])" :key="org.name" class="group">
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-lg flex items-center justify-center">
                    <span class="text-emerald-600 font-bold">{{ org.name.charAt(0) }}</span>
                  </div>
                  <span class="text-sm font-medium text-gray-700">{{ org.name }}</span>
                </div>
                <span class="text-sm font-semibold text-emerald-600">{{ org.event_count }} events</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full h-2 transition-all duration-500 group-hover:opacity-80" 
                     :style="{ width: ((org.event_count || 0) / ((topOrganizations[0]?.event_count || 1)) * 100) + '%' }"></div>
              </div>
            </div>
          </div>
          <div v-if="!topOrganizations || topOrganizations.length === 0" class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            No organizations data available
          </div>
        </div>
      </div>

      <!-- Recent Activity with Hover Effects -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Evaluations -->
        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl overflow-hidden border border-white/20 hover:shadow-2xl transition-all duration-300">
          <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <div class="p-1.5 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-lg">
                  <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                Recent Evaluations
              </h3>
              <Link href="/admin/evaluations" class="text-xs text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                View all
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </Link>
            </div>
          </div>
          <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
            <div v-for="evaluation in (recentEvaluations || [])" :key="evaluation.id" 
                 class="p-4 hover:bg-gray-50 transition-all duration-300 cursor-pointer group">
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <p class="font-semibold text-gray-800 group-hover:text-emerald-600 transition">{{ evaluation.title }}</p>
                  <p class="text-sm text-gray-500 mt-1">{{ evaluation.event_name }} • {{ evaluation.organization }}</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                  <span :class="[
                    'px-3 py-1 text-xs font-medium rounded-full shadow-sm',
                    evaluation.status === 'active' ? 'bg-green-100 text-green-700' :
                    evaluation.status === 'closed' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700'
                  ]">
                    {{ evaluation.status }}
                  </span>
                  <span class="text-xs text-gray-400">{{ evaluation.responses }} responses</span>
                </div>
              </div>
              <div class="mt-2 text-xs text-gray-400">
                {{ evaluation.created_at }}
              </div>
            </div>
            <div v-if="!recentEvaluations || recentEvaluations.length === 0" class="p-8 text-center text-gray-500">
              <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              No recent evaluations
            </div>
          </div>
        </div>

        <!-- Pending Requests -->
        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl overflow-hidden border border-white/20 hover:shadow-2xl transition-all duration-300">
          <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <div class="p-1.5 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg">
                  <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                Pending Requests
              </h3>
              <Link href="/admin/evaluations/create" class="text-xs text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                Create new
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
              </Link>
            </div>
          </div>
          <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
            <div v-for="request in (pendingRequests || [])" :key="request.id" 
                 class="p-4 hover:bg-gray-50 transition-all duration-300 cursor-pointer group">
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <p class="font-semibold text-gray-800 group-hover:text-orange-600 transition">{{ request.title }}</p>
                  <p class="text-sm text-gray-500 mt-1">{{ request.event_name }} • {{ request.organization }}</p>
                </div>
                <span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700 shadow-sm animate-pulse">
                  Pending
                </span>
              </div>
              <div class="mt-2 flex justify-between text-xs text-gray-400">
                <span>Requested by: {{ request.requested_by }}</span>
                <span>{{ request.created_at }}</span>
              </div>
            </div>
            <div v-if="!pendingRequests || pendingRequests.length === 0" class="p-8 text-center text-gray-500">
              <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              No pending requests
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions - Modern Gradient Grid -->
      <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl p-6 border border-white/20">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <div class="p-1.5 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg">
            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          Quick Actions
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <Link href="/admin/evaluations/create" class="group relative overflow-hidden bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-4 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-400 to-teal-400 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
            <div class="relative z-10 text-center">
              <div class="p-3 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full w-14 h-14 mx-auto mb-3 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-700">Create Evaluation</span>
            </div>
          </Link>

          <Link href="/admin/organizations" class="group relative overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-indigo-400 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
            <div class="relative z-10 text-center">
              <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full w-14 h-14 mx-auto mb-3 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-700">Manage Organizations</span>
            </div>
          </Link>

          <Link href="/admin/reports" class="group relative overflow-hidden bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="absolute inset-0 bg-gradient-to-r from-purple-400 to-pink-400 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
            <div class="relative z-10 text-center">
              <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full w-14 h-14 mx-auto mb-3 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-700">View Reports</span>
            </div>
          </Link>

          <Link href="/admin/profile" class="group relative overflow-hidden bg-gradient-to-br from-gray-50 to-slate-50 rounded-xl p-4 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-400 to-slate-400 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
            <div class="relative z-10 text-center">
              <div class="p-3 bg-gradient-to-br from-gray-500 to-slate-500 rounded-full w-14 h-14 mx-auto mb-3 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-700">Update Profile</span>
            </div>
          </Link>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Chart from 'chart.js/auto';

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({})
  },
  recentEvaluations: {
    type: Array,
    default: () => []
  },
  pendingRequests: {
    type: Array,
    default: () => []
  },
  monthlyEvaluations: {
    type: Object,
    default: () => ({})
  },
  monthlyResponses: {
    type: Object,
    default: () => ({})
  },
  statusDistribution: {
    type: Object,
    default: () => ({})
  },
  topOrganizations: {
    type: Array,
    default: () => []
  },
  recentResponses: {
    type: Array,
    default: () => []
  },
  avgSatisfaction: {
    type: Number,
    default: 0
  },
  responseRate: {
    type: Number,
    default: 0
  },
  adminName: {
    type: String,
    default: 'Admin'
  }
});

// Stat Cards Data
const statCards = computed(() => [
  {
    title: 'Total Evaluations',
    value: props.stats?.total_evaluations || 0,
    subtitle: `${props.stats?.active_evaluations || 0} active, ${props.stats?.closed_evaluations || 0} closed`,
    icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    gradient: 'bg-gradient-to-br from-blue-500 to-blue-600',
    trend: 'up',
    trendValue: '+12%'
  },
  {
    title: 'Organizations',
    value: props.stats?.total_organizations || 0,
    subtitle: 'Registered organizations',
    icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
    gradient: 'bg-gradient-to-br from-purple-500 to-purple-600',
    trend: 'up',
    trendValue: '+5%'
  },
  {
    title: 'Total Responses',
    value: props.stats?.total_responses || 0,
    subtitle: 'Collected from students',
    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    gradient: 'bg-gradient-to-br from-green-500 to-green-600',
    trend: 'up',
    trendValue: '+8%'
  },
  {
    title: 'Pending Requests',
    value: props.stats?.pending_requests || 0,
    subtitle: 'Awaiting action',
    icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    gradient: 'bg-gradient-to-br from-orange-500 to-orange-600',
    trend: 'down',
    trendValue: '-2%'
  }
]);

const currentDate = computed(() => {
  return new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
});

const currentDay = computed(() => {
  return new Date().toLocaleDateString('en-US', { weekday: 'long' });
});

// Sentiment percentages (mock data - replace with actual data from AI insights)
const positivePercentage = computed(() => 68);
const neutralPercentage = computed(() => 22);
const negativePercentage = computed(() => 10);

const evaluationsChart = ref(null);
const responsesChart = ref(null);
const statusChart = ref(null);
let evaluationsChartInstance = null;
let responsesChartInstance = null;
let statusChartInstance = null;

onMounted(() => {
  // Initialize Evaluations Chart
  if (evaluationsChart.value && props.monthlyEvaluations && Object.keys(props.monthlyEvaluations).length > 0) {
    const labels = Object.keys(props.monthlyEvaluations);
    const data = Object.values(props.monthlyEvaluations);
    
    evaluationsChartInstance = new Chart(evaluationsChart.value, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Evaluations Created',
          data: data,
          borderColor: 'rgb(16, 185, 129)',
          backgroundColor: 'rgba(16, 185, 129, 0.1)',
          tension: 0.4,
          fill: true,
          pointBackgroundColor: 'rgb(16, 185, 129)',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6,
          pointHoverBackgroundColor: 'rgb(16, 185, 129)',
          pointHoverBorderColor: '#fff',
          pointHoverBorderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              usePointStyle: true,
              boxWidth: 8
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#fff',
            bodyColor: '#e5e7eb',
            borderColor: 'rgb(16, 185, 129)',
            borderWidth: 1,
            callbacks: {
              label: (ctx) => `${ctx.dataset.label}: ${ctx.raw} evaluations`
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            title: {
              display: true,
              text: 'Number of Evaluations',
              color: '#6b7280'
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              maxRotation: 45,
              minRotation: 45
            }
          }
        }
      }
    });
  }

  // Initialize Responses Chart
  if (responsesChart.value && props.monthlyResponses && Object.keys(props.monthlyResponses).length > 0) {
    const labels = Object.keys(props.monthlyResponses);
    const data = Object.values(props.monthlyResponses);
    
    responsesChartInstance = new Chart(responsesChart.value, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Responses Received',
          data: data,
          backgroundColor: 'rgba(59, 130, 246, 0.8)',
          borderRadius: 8,
          barPercentage: 0.7,
          categoryPercentage: 0.8,
          hoverBackgroundColor: 'rgba(59, 130, 246, 1)',
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              usePointStyle: true,
              boxWidth: 8
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#fff',
            bodyColor: '#e5e7eb',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1,
            callbacks: {
              label: (ctx) => `${ctx.dataset.label}: ${ctx.raw} responses`
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            title: {
              display: true,
              text: 'Number of Responses',
              color: '#6b7280'
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              maxRotation: 45,
              minRotation: 45
            }
          }
        }
      }
    });
  }

  // Initialize Status Distribution Chart
  if (statusChart.value && props.statusDistribution && Object.keys(props.statusDistribution).length > 0) {
    const labels = Object.keys(props.statusDistribution);
    const data = Object.values(props.statusDistribution);
    const colors = ['#10b981', '#3b82f6', '#f59e0b'];
    
    statusChartInstance = new Chart(statusChart.value, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: colors,
          borderWidth: 0,
          hoverOffset: 10,
          cutout: '65%'
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
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#fff',
            bodyColor: '#e5e7eb',
            borderColor: '#10b981',
            borderWidth: 1,
            callbacks: {
              label: (ctx) => `${ctx.label}: ${ctx.raw} evaluations (${((ctx.raw / data.reduce((a,b) => a + b, 0)) * 100).toFixed(1)}%)`
            }
          }
        }
      }
    });
  }
});
</script>

<style scoped>
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
    transform: translate(0px, 0px) scale(1);
  }
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