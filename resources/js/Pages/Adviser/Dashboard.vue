<template>
  <OrganizationUserLayout>
    <div class="space-y-6">
      <!-- Animated Gradient Background -->
      <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-cyan-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
      </div>

      <!-- Header -->
      <div class="relative overflow-hidden bg-gradient-to-r from-blue-700 via-indigo-700 to-cyan-800 rounded-2xl shadow-xl p-8 text-white">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="relative z-10">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
              <div class="flex items-center gap-3 mb-3">
                <div class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium backdrop-blur-sm">
                  📋 Adviser Dashboard
                </div>
                <div class="px-3 py-1 bg-yellow-500/20 rounded-full text-sm font-medium backdrop-blur-sm">
                  {{ currentDate }}
                </div>
              </div>
              <h1 class="text-4xl font-bold mb-2">
                Welcome back, <span class="text-blue-200">{{ userName }}</span>!
              </h1>
              <p class="text-blue-100 text-base">Review event proposals, track approvals, and monitor evaluation results.</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-5 text-center border border-white/20">
              <p class="text-2xl font-bold">{{ stats.pending_approval || 0 }}</p>
              <p class="text-sm text-blue-100 mt-1">Pending Approval</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <span class="text-sm font-medium text-yellow-600 bg-yellow-50 px-3 py-1 rounded-full">Action Needed</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-800">{{ stats.pending_approval || 0 }}</h3>
          <p class="text-sm text-gray-500 mt-1">Pending Approval</p>
          <div class="mt-4 flex items-center gap-2">
            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
              <div class="h-full bg-yellow-500 rounded-full" :style="{ width: `${Math.min((stats.pending_approval / 20) * 100, 100)}%` }"></div>
            </div>
            <span class="text-xs text-gray-500">{{ stats.pending_approval || 0 }} pending</span>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <span class="text-sm font-medium text-orange-600 bg-orange-50 px-3 py-1 rounded-full">Documents</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-800">{{ stats.pending_document || 0 }}</h3>
          <p class="text-sm text-gray-500 mt-1">Pending Documents</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <span class="text-sm font-medium text-green-600 bg-green-50 px-3 py-1 rounded-full">Approved</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-800">{{ stats.approved || 0 }}</h3>
          <p class="text-sm text-gray-500 mt-1">Approved Events</p>
          <div class="mt-4 flex items-center gap-2">
            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
              <div class="h-full bg-green-500 rounded-full" :style="{ width: `${stats.approval_rate || 0}%` }"></div>
            </div>
            <span class="text-xs text-gray-500">{{ stats.approval_rate || 0 }}% rate</span>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <span class="text-sm font-medium text-purple-600 bg-purple-50 px-3 py-1 rounded-full">Finished</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-800">{{ stats.finished_events || 0 }}</h3>
          <p class="text-sm text-gray-500 mt-1">Finished Events</p>
        </div>
      </div>

      <!-- Pending Approvals Section -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
          <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
            <div class="w-6 h-6 bg-yellow-100 rounded-lg flex items-center justify-center">
              <svg class="w-3 h-3 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            Events Pending Your Approval
          </h2>
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
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center">
                  <span class="text-white font-bold text-lg">{{ event.event_name?.charAt(0) || 'E' }}</span>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-800">{{ event.event_name }}</h3>
                  <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                    <span class="flex items-center gap-1">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                      </svg>
                      {{ event.event_type?.name || 'N/A' }}
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
                <a v-if="event.signed_document_path" :href="`/storage/${event.signed_document_path}`" target="_blank" 
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

      <!-- ==================== AI INSIGHTS SECTION ==================== -->
      <!-- This is the button/section you were missing! -->
      <div v-if="aiInsightsList && aiInsightsList.length > 0" class="mt-6">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
              <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
              AI-Powered Evaluation Insights
            </h2>
            <p class="text-sm text-gray-500">Click on any evaluation card to view detailed AI analysis</p>
          </div>
          <Link href="/adviser/evaluations" class="text-sm text-purple-600 hover:text-purple-700">
            View all evaluations →
          </Link>
        </div>

        <div class="grid grid-cols-1 gap-4">
          <div v-for="insight in aiInsightsList" :key="insight.id" 
               @click="openInsightModal(insight)"
               class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-200 cursor-pointer group border border-gray-100">
            <div class="p-5">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-4 flex-1">
                  <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                    <span class="text-white font-bold text-lg">{{ insight.event_name?.charAt(0) || 'E' }}</span>
                  </div>
                  <div class="flex-1">
                    <h3 class="font-semibold text-gray-800 group-hover:text-purple-600 transition">
                      {{ insight.event_name }}
                    </h3>
                    <p class="text-sm text-gray-500">{{ insight.evaluation_title }}</p>
                    <div class="flex flex-wrap items-center gap-4 mt-2">
                      <div class="flex items-center gap-1">
                        <div class="w-2 h-2 rounded-full" :class="getInsightDotColor(insight.predicted_satisfaction || 3.5)"></div>
                        <span class="text-xs text-gray-500">Satisfaction: {{ insight.predicted_satisfaction || 0 }}/5.0</span>
                      </div>
                      <div class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="text-xs text-gray-500">{{ insight.total_responses || 0 }} responses</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="flex items-center gap-4">
                  <div class="text-right">
                    <div class="flex items-center gap-1 mb-1">
                      <span class="text-xs text-gray-500">Success Rate:</span>
                      <span :class="getRateTextClass(insight.success_probability)" class="text-sm font-semibold">
                        {{ ((insight.success_probability || 0) * 100).toFixed(0) }}%
                      </span>
                    </div>
                    <div class="w-24 bg-gray-200 rounded-full h-1.5">
                      <div class="h-full rounded-full" :class="getRateColorClass(insight.success_probability)" :style="{ width: ((insight.success_probability || 0) * 100) + '%' }"></div>
                    </div>
                  </div>
                  <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-500 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </div>
              </div>

              <!-- Sentiment Bar Preview -->
              <div class="mt-4 pt-3 border-t border-gray-100">
                <div class="flex justify-between text-xs mb-1">
                  <span class="text-green-600">Positive {{ insight.positive_percentage || 0 }}%</span>
                  <span class="text-yellow-600">Neutral {{ insight.neutral_percentage || 0 }}%</span>
                  <span class="text-red-600">Negative {{ insight.negative_percentage || 0 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                  <div class="h-full flex">
                    <div class="bg-green-500" :style="{ width: (insight.positive_percentage || 0) + '%' }"></div>
                    <div class="bg-yellow-500" :style="{ width: (insight.neutral_percentage || 0) + '%' }"></div>
                    <div class="bg-red-500" :style="{ width: (insight.negative_percentage || 0) + '%' }"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Show message when no insights available -->
      <div v-else-if="stats.finished_events > 0 && aiInsightsList.length === 0" class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
          <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
          </svg>
        </div>
        <h3 class="text-base font-medium text-gray-900 mb-1">No AI Insights Available Yet</h3>
        <p class="text-sm text-gray-500 max-w-md mx-auto">Complete evaluations for your finished events and generate AI insights to see analysis here.</p>
      </div>

      <!-- Approval Trends Chart -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-gray-800">Approval Trends (Last 6 Months)</h2>
          <span class="text-sm text-gray-500">Avg. approval time: {{ stats.avg_approval_time_hours || 0 }} hours</span>
        </div>
        <div style="height: 280px;">
          <canvas ref="approvalTrendsChart"></canvas>
        </div>
      </div>

      <!-- Recent Approvals -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
          <h2 class="text-lg font-semibold text-gray-800">Recent Approval Activity</h2>
        </div>
        <div class="divide-y divide-gray-200">
          <div v-for="approval in recentApprovals" :key="approval.event_name" class="p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-800">{{ approval.event_name }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ approval.approved_at }}</p>
              </div>
              <span :class="approval.status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" 
                    class="px-3 py-1 text-xs rounded-full font-medium">
                {{ approval.status === 'approved' ? 'Approved' : 'Rejected' }}
              </span>
            </div>
            <p v-if="approval.rejection_reason" class="text-xs text-red-600 mt-2">{{ approval.rejection_reason }}</p>
          </div>
          <div v-if="recentApprovals.length === 0" class="p-8 text-center text-gray-500">
            No recent approval activity
          </div>
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

    <!-- ==================== AI INSIGHTS MODAL ==================== -->
    <Teleport to="body">
      <div v-if="showInsightModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeInsightModal">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative w-full max-w-4xl transform overflow-hidden rounded-xl bg-white shadow-2xl transition-all duration-300 scale-100">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-purple-600 to-indigo-600">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                  </div>
                  <div>
                    <h2 class="text-lg font-bold text-white">{{ selectedInsight?.event_name }}</h2>
                    <p class="text-sm text-purple-100">{{ selectedInsight?.evaluation_title }}</p>
                  </div>
                </div>
                <button @click="closeInsightModal" class="text-white/60 hover:text-white transition">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 px-6 pt-4 bg-gray-50">
              <nav class="flex gap-6">
                <button @click="modalTab = 'overview'" 
                        :class="modalTab === 'overview' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-3 py-2 text-sm border-b-2 font-medium transition">
                  Overview
                </button>
                <button @click="modalTab = 'sentiment'" 
                        :class="modalTab === 'sentiment' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-3 py-2 text-sm border-b-2 font-medium transition">
                  Sentiment Analysis
                </button>
                <button @click="modalTab = 'recommendations'" 
                        :class="modalTab === 'recommendations' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-3 py-2 text-sm border-b-2 font-medium transition">
                  Recommendations
                </button>
              </nav>
            </div>

            <!-- Content - Overview Tab -->
            <div class="px-6 py-6 max-h-[65vh] overflow-y-auto">
              <div v-if="modalTab === 'overview'" class="space-y-5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                  <div class="bg-emerald-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Satisfaction</p>
                    <p class="text-xl font-bold text-emerald-600">{{ selectedInsight?.predicted_satisfaction || 0 }}/5.0</p>
                  </div>
                  <div class="bg-blue-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Success Rate</p>
                    <p class="text-xl font-bold text-blue-600">{{ ((selectedInsight?.success_probability || 0) * 100).toFixed(0) }}%</p>
                  </div>
                  <div class="bg-purple-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Response Rate</p>
                    <p class="text-xl font-bold text-purple-600">{{ selectedInsight?.response_rate || 0 }}%</p>
                  </div>
                  <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Responses</p>
                    <p class="text-xl font-bold text-gray-700">{{ selectedInsight?.total_responses || 0 }}</p>
                  </div>
                </div>

                <div v-if="selectedInsight?.category_breakdown && Object.keys(selectedInsight.category_breakdown).length > 0" class="bg-gray-50 rounded-lg p-4">
                  <h3 class="font-semibold text-gray-800 text-sm mb-3">Category Performance</h3>
                  <div class="space-y-2">
                    <div v-for="(score, category) in selectedInsight.category_breakdown" :key="category">
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                    <h3 class="font-semibold text-green-700 text-sm mb-2">💪 Strengths</h3>
                    <ul class="space-y-1">
                      <li v-for="strength in selectedInsight?.strengths?.slice(0, 5)" :key="strength" class="text-xs text-green-600 flex items-start gap-1">
                        <span class="text-green-500">✓</span> {{ strength }}
                      </li>
                      <li v-if="!selectedInsight?.strengths?.length" class="text-xs text-gray-500 italic">No strengths identified</li>
                    </ul>
                  </div>
                  <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                    <h3 class="font-semibold text-red-700 text-sm mb-2">⚠️ Areas to Improve</h3>
                    <ul class="space-y-1">
                      <li v-for="weakness in selectedInsight?.weaknesses?.slice(0, 5)" :key="weakness" class="text-xs text-red-600 flex items-start gap-1">
                        <span class="text-red-500">•</span> {{ weakness }}
                      </li>
                      <li v-if="!selectedInsight?.weaknesses?.length" class="text-xs text-gray-500 italic">No weaknesses identified</li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Sentiment Tab -->
              <div v-if="modalTab === 'sentiment'" class="space-y-5">
                <div class="grid grid-cols-3 gap-3 mb-4">
                  <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ selectedInsight?.positive_percentage || 0 }}%</p>
                    <p class="text-xs text-gray-500">Positive Comments</p>
                  </div>
                  <div class="bg-yellow-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-600">{{ selectedInsight?.neutral_percentage || 0 }}%</p>
                    <p class="text-xs text-gray-500">Neutral Comments</p>
                  </div>
                  <div class="bg-red-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-red-600">{{ selectedInsight?.negative_percentage || 0 }}%</p>
                    <p class="text-xs text-gray-500">Negative Comments</p>
                  </div>
                </div>

                <div class="mb-6">
                  <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="h-full flex">
                      <div class="bg-green-500" :style="{ width: (selectedInsight?.positive_percentage || 0) + '%' }"></div>
                      <div class="bg-yellow-500" :style="{ width: (selectedInsight?.neutral_percentage || 0) + '%' }"></div>
                      <div class="bg-red-500" :style="{ width: (selectedInsight?.negative_percentage || 0) + '%' }"></div>
                    </div>
                  </div>
                </div>

                <div v-if="selectedInsight?.common_themes && selectedInsight.common_themes.length > 0" class="bg-gray-50 rounded-lg p-4">
                  <h3 class="font-semibold text-gray-800 text-sm mb-3">Common Themes</h3>
                  <div class="flex flex-wrap gap-2">
                    <span v-for="theme in selectedInsight.common_themes" :key="theme" class="px-3 py-1.5 bg-white rounded-full text-xs text-gray-600 border border-gray-200">
                      {{ theme }}
                    </span>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                    <h3 class="font-semibold text-green-700 text-sm mb-2">📝 Sample Positive Comments</h3>
                    <ul class="space-y-2">
                      <li v-for="comment in selectedInsight?.sample_positive_comments?.slice(0, 3)" :key="comment" class="text-xs text-green-700 italic">
                        "{{ comment }}"
                      </li>
                      <li v-if="!selectedInsight?.sample_positive_comments?.length" class="text-xs text-gray-500 italic">No positive comments</li>
                    </ul>
                  </div>
                  <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                    <h3 class="font-semibold text-red-700 text-sm mb-2">📝 Sample Negative Comments</h3>
                    <ul class="space-y-2">
                      <li v-for="comment in selectedInsight?.sample_negative_comments?.slice(0, 3)" :key="comment" class="text-xs text-red-700 italic">
                        "{{ comment }}"
                      </li>
                      <li v-if="!selectedInsight?.sample_negative_comments?.length" class="text-xs text-gray-500 italic">No negative comments</li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Recommendations Tab -->
              <div v-if="modalTab === 'recommendations'" class="space-y-4">
                <div v-for="(rec, idx) in selectedInsight?.recommendations" :key="idx" 
                     class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                  <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 text-xs rounded-full" :class="{
                      'bg-red-100 text-red-700': rec.priority === 'high',
                      'bg-yellow-100 text-yellow-700': rec.priority === 'medium',
                      'bg-green-100 text-green-700': rec.priority === 'low'
                    }">
                      {{ rec.priority?.toUpperCase() || 'MEDIUM' }} PRIORITY
                    </span>
                    <span class="text-xs text-gray-500">{{ rec.category }}</span>
                  </div>
                  <p class="text-sm font-medium text-gray-800 mb-2">{{ rec.title || rec.problem_statement }}</p>
                  <div v-if="rec.action_items" class="mt-2">
                    <p class="text-xs font-semibold text-gray-700 mb-1">Action Items:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                      <li v-for="action in rec.action_items.slice(0, 3)" :key="action" class="text-xs text-gray-600">{{ action }}</li>
                    </ul>
                  </div>
                  <p v-if="rec.expected_outcome" class="text-xs text-green-700 mt-2">🎯 Expected: {{ rec.expected_outcome }}</p>
                </div>
                <div v-if="!selectedInsight?.recommendations?.length" class="text-center py-8 text-gray-500">
                  No recommendations available
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                  <span class="text-xs text-gray-500">Analyzed: {{ formatDate(selectedInsight?.analyzed_at) }}</span>
                </div>
                <button @click="closeInsightModal" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition text-sm">
                  Close
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </OrganizationUserLayout>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
import Chart from 'chart.js/auto';

const page = usePage();
const user = page.props.auth?.user;
const userName = computed(() => user?.name?.split(' ')[0] || 'Adviser');

const props = defineProps({
stats: { type: Object, default: () => ({}) },
pendingEvents: { type: Array, default: () => [] },
aiInsightsList: { type: Array, default: () => [] },
approvalTrends: { type: Array, default: () => [] },
recentApprovals: { type: Array, default: () => [] },
user: { type: Object, default: () => ({}) }
});

// Modal state
const showInsightModal = ref(false);
const selectedInsight = ref(null);
const modalTab = ref('overview');

// Chart refs
const approvalTrendsChart = ref(null);
let approvalTrendsChartInstance = null;

// Modal functions
function openInsightModal(insight) {
console.log('Opening modal for:', insight);
selectedInsight.value = insight;
modalTab.value = 'overview';
showInsightModal.value = true;
}

function closeInsightModal() {
showInsightModal.value = false;
selectedInsight.value = null;
modalTab.value = 'overview';
}

// Helper functions
function getInsightDotColor(score) {
if (score >= 4) return 'bg-green-500';
if (score >= 3) return 'bg-yellow-500';
return 'bg-red-500';
}

function getRateColorClass(probability) {
if (probability >= 0.7) return 'bg-green-500';
if (probability >= 0.4) return 'bg-yellow-500';
return 'bg-red-500';
}

function getRateTextClass(probability) {
if (probability >= 0.7) return 'text-green-600';
if (probability >= 0.4) return 'text-yellow-600';
return 'text-red-600';
}

function formatDate(date) {
if (!date) return 'N/A';
return new Date(date).toLocaleDateString('en-US', {
  year: 'numeric',
  month: 'short',
  day: 'numeric',
  hour: '2-digit',
  minute: '2-digit'
});
}

function initCharts() {
if (approvalTrendsChart.value && props.approvalTrends && props.approvalTrends.length > 0) {
  if (approvalTrendsChartInstance) approvalTrendsChartInstance.destroy();
  
  const labels = props.approvalTrends.map(item => {
    const [year, month] = item.month.split('-');
    const date = new Date(year, month - 1);
    return date.toLocaleDateString('en-US', { month: 'short' });
  });
  
  const approvedData = props.approvalTrends.map(item => item.approved);
  const rejectedData = props.approvalTrends.map(item => item.rejected);
  
  approvalTrendsChartInstance = new Chart(approvalTrendsChart.value, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Approved',
          data: approvedData,
          backgroundColor: 'rgba(16, 185, 129, 0.7)',
          borderRadius: 6
        },
        {
          label: 'Rejected',
          data: rejectedData,
          backgroundColor: 'rgba(239, 68, 68, 0.7)',
          borderRadius: 6
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'top' }
      },
      scales: {
        y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
        x: { grid: { display: false } }
      }
    }
  });
}
}

const currentDate = computed(() => {
return new Date().toLocaleDateString('en-US', {
  weekday: 'long',
  year: 'numeric',
  month: 'long',
  day: 'numeric'
});
});

onMounted(() => {
initCharts();
});

onUnmounted(() => {
if (approvalTrendsChartInstance) approvalTrendsChartInstance.destroy();
});
</script>

<style scoped>
@keyframes blob {
0% { transform: translate(0px, 0px) scale(1); }
33% { transform: translate(30px, -50px) scale(1.1); }
66% { transform: translate(-20px, 20px) scale(0.9); }
100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob { animation: blob 7s infinite; }
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }
</style>