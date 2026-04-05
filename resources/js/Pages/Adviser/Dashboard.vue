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

      <!-- AI Insights Section (Completed Events) -->
      <div v-if="aiInsightsList && aiInsightsList.length > 0" class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
              <div class="w-6 h-6 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-3 h-3 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
              </div>
              Completed Event Insights
            </h2>
            <Link href="/adviser/evaluations" class="text-sm text-purple-600 hover:text-purple-700">
              View all evaluations →
            </Link>
          </div>
        </div>

        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="insight in aiInsightsList" :key="insight.id" 
                 class="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">
              <div class="flex items-center justify-between mb-3">
                <div>
                  <h3 class="font-semibold text-gray-800">{{ insight.event_name }}</h3>
                  <p class="text-xs text-gray-500">{{ insight.total_responses }} responses</p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-bold" :class="getScoreColor(insight.predicted_satisfaction)">
                    {{ insight.predicted_satisfaction || 0 }}/5.0
                  </p>
                  <p class="text-xs text-gray-500">Satisfaction</p>
                </div>
              </div>

              <!-- Sentiment Mini Chart -->
              <div class="mb-2">
                <div class="flex justify-between text-xs mb-1">
                  <span class="text-green-600">👍 {{ insight.positive_percentage || 0 }}%</span>
                  <span class="text-yellow-600">● {{ insight.neutral_percentage || 0 }}%</span>
                  <span class="text-red-600">👎 {{ insight.negative_percentage || 0 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                  <div class="h-full flex">
                    <div class="bg-green-500" :style="{ width: (insight.positive_percentage || 0) + '%' }"></div>
                    <div class="bg-yellow-500" :style="{ width: (insight.neutral_percentage || 0) + '%' }"></div>
                    <div class="bg-red-500" :style="{ width: (insight.negative_percentage || 0) + '%' }"></div>
                  </div>
                </div>
              </div>

              <!-- Success Probability -->
              <div class="mt-3 pt-3 border-t border-gray-200">
                <div class="flex justify-between text-xs">
                  <span class="text-gray-500">Success Probability</span>
                  <span class="font-medium" :class="getProbabilityColor(insight.success_probability)">
                    {{ ((insight.success_probability || 0) * 100).toFixed(0) }}%
                  </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1 mt-1 overflow-hidden">
                  <div class="h-full rounded-full" :class="getProbabilityBarColor(insight.success_probability)" 
                       :style="{ width: ((insight.success_probability || 0) * 100) + '%' }"></div>
                </div>
              </div>

              <p class="text-xs text-gray-400 mt-3">
                Analyzed: {{ insight.analyzed_at ? new Date(insight.analyzed_at).toLocaleDateString() : 'N/A' }}
              </p>
            </div>
          </div>
        </div>
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
eventTypeDistribution: { type: Array, default: () => [] },
monthlyEventTrends: { type: Array, default: () => [] },
user: { type: Object, default: () => ({}) }
});

const approvalTrendsChart = ref(null);
let approvalTrendsChartInstance = null;

function getScoreColor(score) {
if (score >= 4) return 'text-green-600';
if (score >= 3) return 'text-yellow-600';
return 'text-red-600';
}

function getProbabilityColor(probability) {
if (probability >= 0.7) return 'text-green-600';
if (probability >= 0.4) return 'text-yellow-600';
return 'text-red-600';
}

function getProbabilityBarColor(probability) {
if (probability >= 0.7) return 'bg-green-500';
if (probability >= 0.4) return 'bg-yellow-500';
return 'bg-red-500';
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