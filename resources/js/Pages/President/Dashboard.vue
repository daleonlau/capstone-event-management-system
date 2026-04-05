<template>
  <OrganizationUserLayout>
    <div class="space-y-8">
      <!-- Animated Gradient Background -->
      <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-teal-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-cyan-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
      </div>

      <!-- Welcome Header with Date -->
      <div class="relative overflow-hidden bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-800 rounded-2xl shadow-xl p-8 text-white">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
        
        <div class="relative z-10">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
              <div class="flex items-center gap-3 mb-3">
                <div class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium backdrop-blur-sm">
                  ✨ President Dashboard
                </div>
                <div class="px-3 py-1 bg-yellow-500/20 rounded-full text-sm font-medium backdrop-blur-sm">
                  {{ currentDate }}
                </div>
              </div>
              <h1 class="text-4xl font-bold mb-2">
                Welcome back, <span class="text-emerald-200">{{ userName }}</span>!
              </h1>
              <p class="text-emerald-100 text-base max-w-2xl">Manage your organization's events, track student participation, and gain AI-powered insights.</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-5 text-center border border-white/20">
              <p class="text-3xl font-bold">{{ formatDate() }}</p>
              <p class="text-sm text-emerald-100 mt-1">{{ formatDay() }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Total</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ stats.total_events || 0 }}</h3>
            <p class="text-sm text-gray-500 mt-1">Total Events Created</p>
            <div class="mt-4 flex items-center gap-2">
              <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full" :style="{ width: `${Math.min((stats.total_events / 50) * 100, 100)}%` }"></div>
              </div>
              <span class="text-xs text-gray-500">{{ Math.min(((stats.total_events / 50) * 100).toFixed(0), 100) }}%</span>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-yellow-600 bg-yellow-50 px-3 py-1 rounded-full">Pending</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ stats.pending_events || 0 }}</h3>
            <p class="text-sm text-gray-500 mt-1">Pending Events</p>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <span class="text-sm font-medium text-green-600 bg-green-50 px-3 py-1 rounded-full">Approved</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ stats.approved_events || 0 }}</h3>
            <p class="text-sm text-gray-500 mt-1">Approved Events</p>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-purple-600 bg-purple-50 px-3 py-1 rounded-full">Students</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ stats.total_students || 0 }}</h3>
            <p class="text-sm text-gray-500 mt-1">Enrolled Students</p>
          </div>
        </div>
      </div>

      <!-- AI Insights Preview Cards - Same as Admin Dashboard -->
      <div v-if="aiInsightsList && aiInsightsList.length > 0" class="mt-8">
        <div class="flex items-center justify-between mb-5">
          <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
              <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
              AI-Powered Evaluation Insights
            </h2>
            <p class="text-sm text-gray-500 mt-1">Click on any evaluation to view detailed AI analysis</p>
          </div>
          <Link href="/president/evaluations" class="text-sm text-purple-600 hover:text-purple-700">
            View all evaluations →
          </Link>
        </div>

        <div class="grid grid-cols-1 gap-4">
          <div v-for="insight in aiInsightsList" :key="insight.id" 
               @click="openInsightModal(insight)"
               class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-200 cursor-pointer group">
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
                      <div class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs text-gray-500">{{ insight.response_rate || 0 }}% response rate</span>
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

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md p-5">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-800">Events Overview</h3>
            <span class="text-xs text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Last 6 months</span>
          </div>
          <div style="height: 280px;">
            <canvas ref="eventsChart"></canvas>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-5">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-800">Students by Department</h3>
            <span class="text-xs text-gray-500">{{ stats.total_students || 0 }} total</span>
          </div>
          <div style="height: 280px;">
            <canvas ref="departmentChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Recent Events Table -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
          <h3 class="text-base font-semibold text-gray-800">Recent Events</h3>
          <Link href="/president/events" class="text-sm text-emerald-600 hover:text-emerald-700">
            View all →
          </Link>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="event in recentEvents" :key="event.id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mr-3">
                      <span class="text-white font-bold">{{ event.event_name?.charAt(0) || 'E' }}</span>
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">{{ event.event_name }}</p>
                      <p class="text-xs text-gray-500">ID: {{ event.id }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ event.event_type?.name || 'N/A' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ formatEventDate(event.event_date_start) }}</td>
                <td class="px-6 py-4">
                  <span :class="statusBadgeClass(event.status)" class="px-3 py-1 text-xs rounded-full">
                    {{ event.status }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span :class="approvalBadgeClass(event.approval_status)" class="px-3 py-1 text-xs rounded-full">
                    {{ formatApprovalStatus(event.approval_status) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right space-x-3">
                  <Link :href="`/president/events/${event.id}`" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</Link>
                  <Link :href="`/president/events/${event.id}/edit`" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">Edit</Link>
                </td>
              </tr>
              <tr v-if="recentEvents.length === 0">
                <td colspan="6" class="px-6 py-8 text-center">
                  <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-gray-500">No events yet. Create your first event!</p>
                  <Link href="/president/events/create" class="inline-block mt-4 px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700">
                    Create Event
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Activity Timeline -->
      <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Recent Activity</h3>
        <div class="space-y-4">
          <div v-for="(activity, index) in recentActivities" :key="index" class="flex items-start gap-4">
            <div class="relative">
              <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center">
                <svg v-if="activity.icon === 'calendar'" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                </svg>
                <svg v-else-if="activity.icon === 'user'" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <svg v-else class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
              </div>
              <div v-if="index !== recentActivities.length - 1" class="absolute top-10 left-5 w-0.5 h-12 bg-gray-200"></div>
            </div>
            <div class="flex-1">
              <p class="text-sm text-gray-800">{{ activity.description }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ activity.time }}</p>
            </div>
          </div>
          
          <div v-if="recentActivities.length === 0" class="text-center py-8">
            <p class="text-gray-500">No recent activity</p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-xl shadow-md p-5">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
          <Link href="/president/events/create" class="text-center p-3 rounded-lg hover:bg-gray-50 transition group">
            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
              <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            <span class="text-xs text-gray-600">Create Event</span>
          </Link>

          <Link href="/president/students/create" class="text-center p-3 rounded-lg hover:bg-gray-50 transition group">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
              <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
            </div>
            <span class="text-xs text-gray-600">Add Student</span>
          </Link>

          <Link href="/president/students/bulk-upload" class="text-center p-3 rounded-lg hover:bg-gray-50 transition group">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
              <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
              </svg>
            </div>
            <span class="text-xs text-gray-600">Bulk Upload</span>
          </Link>

          <Link href="/president/evaluations" class="text-center p-3 rounded-lg hover:bg-gray-50 transition group">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
              <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <span class="text-xs text-gray-600">View Evaluations</span>
          </Link>
        </div>
      </div>
    </div>

    <!-- AI Insights Modal - Built-in, no separate component -->
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

            <!-- Content -->
            <div class="px-6 py-6 max-h-[65vh] overflow-y-auto">
              <!-- Overview Tab -->
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
                  <span class="text-xs text-gray-500">Analyzed: {{ formatAnalyzedDate(selectedInsight?.analyzed_at) }}</span>
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
const userName = computed(() => user?.name?.split(' ')[0] || 'President');

const props = defineProps({
stats: { type: Object, default: () => ({}) },
recentEvents: { type: Array, default: () => [] },
studentsByDepartment: { type: Array, default: () => [] },
eventTrends: { type: Array, default: () => [] },
evaluationTrends: { type: Array, default: () => [] },
recentActivities: { type: Array, default: () => [] },
aiInsightsList: { type: Array, default: () => [] },
user: { type: Object, default: () => ({}) }
});

// Modal state
const showInsightModal = ref(false);
const selectedInsight = ref(null);
const modalTab = ref('overview');

// Chart refs
const eventsChart = ref(null);
const departmentChart = ref(null);
let eventsChartInstance = null;
let departmentChartInstance = null;

// Modal functions
function openInsightModal(insight) {
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

function formatAnalyzedDate(date) {
if (!date) return 'N/A';
return new Date(date).toLocaleDateString('en-US', {
  year: 'numeric',
  month: 'short',
  day: 'numeric',
  hour: '2-digit',
  minute: '2-digit'
});
}

function formatDate() {
return new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
}

function formatDay() {
return new Date().toLocaleDateString('en-US', { weekday: 'long' });
}

function formatEventDate(date) {
if (!date) return 'N/A';
return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function formatApprovalStatus(status) {
if (!status) return 'N/A';
return status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
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

function approvalBadgeClass(status) {
const base = 'px-3 py-1 text-xs rounded-full font-medium';
switch(status) {
  case 'pending_document': return `${base} bg-yellow-100 text-yellow-700`;
  case 'pending_approval': return `${base} bg-blue-100 text-blue-700`;
  case 'approved': return `${base} bg-green-100 text-green-700`;
  case 'rejected': return `${base} bg-red-100 text-red-700`;
  default: return `${base} bg-gray-100 text-gray-700`;
}
}

function initCharts() {
// Events Chart
if (eventsChart.value) {
  if (eventsChartInstance) eventsChartInstance.destroy();
  
  const hasData = props.eventTrends && props.eventTrends.length > 0;
  const labels = hasData ? props.eventTrends.map(item => {
    const [year, month] = item.month.split('-');
    const date = new Date(year, month - 1);
    return date.toLocaleDateString('en-US', { month: 'short' });
  }) : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
  
  const data = hasData ? props.eventTrends.map(item => item.total) : [0, 0, 0, 0, 0, 0];
  
  eventsChartInstance = new Chart(eventsChart.value, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Events Created',
        data: data,
        borderColor: 'rgb(16, 185, 129)',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        tension: 0.4,
        fill: true
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

// Department Chart
if (departmentChart.value) {
  if (departmentChartInstance) departmentChartInstance.destroy();
  
  const hasData = props.studentsByDepartment && props.studentsByDepartment.length > 0;
  const labels = hasData ? props.studentsByDepartment.map(item => item.department) : ['No Data'];
  const data = hasData ? props.studentsByDepartment.map(item => item.total) : [1];
  const backgroundColors = hasData 
    ? ['rgba(16, 185, 129, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(245, 158, 11, 0.8)', 'rgba(139, 92, 246, 0.8)', 'rgba(236, 72, 153, 0.8)']
    : ['rgba(156, 163, 175, 0.8)'];
  
  departmentChartInstance = new Chart(departmentChart.value, {
    type: 'doughnut',
    data: {
      labels: labels,
      datasets: [{ data: data, backgroundColor: backgroundColors, borderWidth: 0 }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
        tooltip: {
          callbacks: {
            label: function(context) {
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
              return `${context.label}: ${context.raw} (${percentage}%)`;
            }
          }
        }
      },
      cutout: '60%'
    }
  });
}
}

const currentDate = computed(() => formatDate());

onMounted(() => {
initCharts();
});

onUnmounted(() => {
if (eventsChartInstance) eventsChartInstance.destroy();
if (departmentChartInstance) departmentChartInstance.destroy();
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