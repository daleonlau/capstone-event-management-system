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

      <!-- AI Insights Section -->
      <div v-if="aiInsightsList && aiInsightsList.length > 0">
        <div class="flex items-center justify-between mb-5">
          <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
              <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
              AI-Powered Evaluation Insights
            </h2>
            <p class="text-sm text-gray-500 mt-1">See how participants rated your events</p>
          </div>
          <Link href="/president/evaluations" class="text-sm text-purple-600 hover:text-purple-700">
            View all evaluations →
          </Link>
        </div>

        <div class="grid grid-cols-1 gap-4">
          <div v-for="insight in aiInsightsList" :key="insight.id" 
               class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="p-5">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold">{{ insight.event_name?.charAt(0) || 'E' }}</span>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-800">{{ insight.event_name }}</h3>
                    <p class="text-xs text-gray-500">{{ insight.evaluation_title }}</p>
                  </div>
                </div>
                <Link :href="`/president/evaluations/${insight.evaluation_id}`" 
                      class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                  View Details →
                </Link>
              </div>

              <!-- KPI Row -->
              <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-emerald-50 rounded-lg p-2 text-center">
                  <p class="text-xs text-gray-500">Satisfaction</p>
                  <p class="text-lg font-bold text-emerald-600">{{ insight.predicted_satisfaction || 0 }}/5.0</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-2 text-center">
                  <p class="text-xs text-gray-500">Success Rate</p>
                  <p class="text-lg font-bold text-blue-600">{{ ((insight.success_probability || 0) * 100).toFixed(0) }}%</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-2 text-center">
                  <p class="text-xs text-gray-500">Response Rate</p>
                  <p class="text-lg font-bold text-purple-600">{{ insight.response_rate || 0 }}%</p>
                </div>
              </div>

              <!-- Sentiment Bar -->
              <div class="mb-3">
                <div class="flex justify-between text-xs mb-1">
                  <span class="text-green-600">Positive {{ insight.positive_percentage || 0 }}%</span>
                  <span class="text-yellow-600">Neutral {{ insight.neutral_percentage || 0 }}%</span>
                  <span class="text-red-600">Negative {{ insight.negative_percentage || 0 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                  <div class="h-full flex">
                    <div class="bg-green-500" :style="{ width: (insight.positive_percentage || 0) + '%' }"></div>
                    <div class="bg-yellow-500" :style="{ width: (insight.neutral_percentage || 0) + '%' }"></div>
                    <div class="bg-red-500" :style="{ width: (insight.negative_percentage || 0) + '%' }"></div>
                  </div>
                </div>
              </div>

              <!-- Strengths & Weaknesses -->
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div class="bg-green-50 rounded-lg p-2">
                  <p class="text-xs font-semibold text-green-600 mb-1">💪 Strengths</p>
                  <ul class="text-xs text-green-600 space-y-0.5">
                    <li v-for="s in insight.strengths?.slice(0, 2)" :key="s">✓ {{ s }}</li>
                    <li v-if="!insight.strengths?.length" class="text-gray-500">None identified</li>
                  </ul>
                </div>
                <div class="bg-red-50 rounded-lg p-2">
                  <p class="text-xs font-semibold text-red-600 mb-1">⚠️ Areas to Improve</p>
                  <ul class="text-xs text-red-600 space-y-0.5">
                    <li v-for="w in insight.weaknesses?.slice(0, 2)" :key="w">• {{ w }}</li>
                    <li v-if="!insight.weaknesses?.length" class="text-gray-500">None identified</li>
                  </ul>
                </div>
              </div>

              <!-- Top Recommendation -->
              <div v-if="insight.recommendations && insight.recommendations.length > 0" class="bg-amber-50 rounded-lg p-3">
                <p class="text-xs font-semibold text-amber-700 mb-1">📋 Top Recommendation</p>
                <p class="text-sm text-amber-800">{{ insight.recommendations[0]?.title || insight.recommendations[0]?.category || 'Improve low-scoring areas' }}</p>
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

const eventsChart = ref(null);
const departmentChart = ref(null);
let eventsChartInstance = null;
let departmentChartInstance = null;

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