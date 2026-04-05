<template>
  <OrganizationUserLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Treasurer Dashboard</h1>
          <p class="text-gray-500 mt-1">Welcome back, {{ user?.name }}! Manage collections for approved events.</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-xl shadow-sm flex items-center gap-2">
          <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
          <span class="text-sm text-gray-600">{{ currentDate }}</span>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Events -->
        <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
              </svg>
            </div>
            <span class="text-sm font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Active</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-800">{{ stats.total_events }}</h3>
          <p class="text-sm text-gray-500 mt-1">Active Events</p>
        </div>

        <!-- Total Collected -->
        <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zM12 2v2m0 16v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
              </svg>
            </div>
            <span class="text-sm font-medium text-green-600 bg-green-50 px-3 py-1 rounded-full">Total</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-800">₱{{ formatNumber(stats.total_collected) }}</h3>
          <p class="text-sm text-gray-500 mt-1">Total Collections</p>
          <p class="text-xs text-gray-400 mt-2">Target: ₱{{ formatNumber(stats.total_expected) }}</p>
        </div>

        <!-- Overall Rate -->
        <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <span class="text-sm font-medium text-purple-600 bg-purple-50 px-3 py-1 rounded-full">Rate</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-800">{{ stats.overall_rate }}%</h3>
          <p class="text-sm text-gray-500 mt-1">Overall Collection Rate</p>
          <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
            <div class="bg-purple-600 rounded-full h-2 transition-all duration-500" :style="{ width: stats.overall_rate + '%' }"></div>
          </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <span class="text-sm font-medium text-yellow-600 bg-yellow-50 px-3 py-1 rounded-full">Pending</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-800">{{ stats.pending_payments }}</h3>
          <p class="text-sm text-gray-500 mt-1">Pending Payments</p>
          <p class="text-xs text-gray-400 mt-2">Need your attention</p>
        </div>
      </div>

      <!-- Monthly Collection Trend Chart -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
          <h2 class="text-lg font-semibold text-gray-800">Monthly Collection Trend</h2>
          <span class="text-xs text-gray-500">Last 6 months</span>
        </div>
        <div class="h-64">
          <canvas ref="trendChart"></canvas>
        </div>
      </div>

      <!-- Active Events for Collection -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center flex-wrap gap-2">
          <h2 class="text-lg font-semibold text-gray-800">Active Collections</h2>
          <Link href="/treasurer/collections" class="text-sm text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
            View All
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </Link>
        </div>

        <div class="p-6">
          <div v-if="events.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="event in events" :key="event.id" 
                 class="border rounded-xl p-4 hover:shadow-lg transition cursor-pointer"
                 @click="goToEvent(event.id)">
              <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold text-gray-800">{{ event.event_name }}</h3>
                <span :class="getStatusBadgeClass(event.status)" class="text-xs px-2 py-1 rounded-full">
                  {{ event.status.label }}
                </span>
              </div>
              
              <p class="text-sm text-gray-500 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                </svg>
                {{ formatDate(event.event_date_start) }}
                <span v-if="event.days_remaining !== null && event.days_remaining > 0" class="text-xs text-orange-500">
                  ({{ event.days_remaining }} days left)
                </span>
              </p>
              
              <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-600">Fee: ₱{{ formatNumber(event.event_fee) }}</span>
                <span class="text-sm font-medium" :class="getPaidPercentageColor(event.paid_percentage)">
                  {{ event.paid_count }}/{{ event.target_count }} paid
                </span>
              </div>
              
              <!-- Progress bar -->
              <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                <div class="bg-emerald-600 rounded-full h-2 transition-all duration-500"
                     :style="{ width: event.progress + '%' }"></div>
              </div>
              
              <div class="flex justify-between text-xs text-gray-500">
                <span>Collected: ₱{{ formatNumber(event.collected_amount) }}</span>
                <span>{{ event.progress }}%</span>
              </div>
              
              <div class="mt-3 pt-2 border-t border-gray-100 flex justify-between text-xs">
                <span class="text-gray-500">Remaining: ₱{{ formatNumber(event.remaining_amount) }}</span>
                <span class="text-emerald-600 font-medium">{{ event.paid_percentage }}% paid</span>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <p class="text-gray-500">No active collections at the moment</p>
            <p class="text-sm text-gray-400 mt-1">Approved events will appear here</p>
          </div>
        </div>
      </div>

      <!-- Top Performing Events & Recent Payments -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Performing Events -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
              <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
              Top Performing Events
            </h2>
          </div>
          <div class="divide-y divide-gray-100">
            <div v-for="(event, idx) in topEvents" :key="event.id" class="p-4 hover:bg-gray-50 transition">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full" :class="getRankColor(idx + 1)">
                    <span class="text-white font-bold text-sm flex items-center justify-center h-full">{{ idx + 1 }}</span>
                  </div>
                  <div>
                    <p class="font-medium text-gray-800">{{ event.event_name }}</p>
                    <p class="text-xs text-gray-500">{{ event.paid_count }}/{{ event.target_count }} students</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="font-bold text-emerald-600">₱{{ formatNumber(event.collected_amount) }}</p>
                  <p class="text-xs text-gray-500">{{ event.progress }}% collected</p>
                </div>
              </div>
            </div>
            <div v-if="topEvents.length === 0" class="p-8 text-center text-gray-500">
              No events with collections yet
            </div>
          </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
              <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zM12 2v2m0 16v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
              </svg>
              Recent Payments
            </h2>
          </div>
          <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
            <div v-for="payment in recentPayments" :key="payment.receipt_number" class="p-4 hover:bg-gray-50 transition">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="font-medium text-gray-800">{{ payment.student_name }}</p>
                  <p class="text-xs text-gray-500">{{ payment.event_name }}</p>
                  <p class="text-xs text-gray-400 mt-1">{{ payment.paid_at }}</p>
                </div>
                <div class="text-right">
                  <p class="font-bold text-green-600">₱{{ formatNumber(payment.amount) }}</p>
                  <p class="text-xs text-gray-400">Receipt: {{ payment.receipt_number }}</p>
                </div>
              </div>
            </div>
            <div v-if="recentPayments.length === 0" class="p-8 text-center text-gray-500">
              No recent payments
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <Link href="/treasurer/collections" 
              class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition flex items-center gap-4 group">
          <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-800">Manage Collections</h3>
            <p class="text-sm text-gray-500">View and process payments for events</p>
          </div>
        </Link>

        <Link href="/treasurer/reports" 
              class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition flex items-center gap-4 group">
          <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <svg class="w-7 h-7 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-800">Collection Reports</h3>
            <p class="text-sm text-gray-500">View summary and analytics</p>
          </div>
        </Link>
      </div>
    </div>
  </OrganizationUserLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
import Chart from 'chart.js/auto';

const page = usePage();
const user = page.props.auth?.user;

const props = defineProps({
stats: {
  type: Object,
  default: () => ({
    total_events: 0,
    total_collected: 0,
    total_expected: 0,
    overall_rate: 0,
    pending_payments: 0,
    total_students: 0
  })
},
events: {
  type: Array,
  default: () => []
},
recentPayments: {
  type: Array,
  default: () => []
},
monthlyTrend: {
  type: Array,
  default: () => []
},
topEvents: {
  type: Array,
  default: () => []
},
user: {
  type: Object,
  default: () => ({})
}
});

const trendChart = ref(null);
let trendChartInstance = null;

const currentDate = computed(() => {
return new Date().toLocaleDateString('en-US', {
  weekday: 'long',
  year: 'numeric',
  month: 'long',
  day: 'numeric'
});
});

function formatNumber(num) {
return num?.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '0.00';
}

function formatDate(date) {
if (!date) return 'N/A';
return new Date(date).toLocaleDateString('en-US', {
  year: 'numeric',
  month: 'short',
  day: 'numeric'
});
}

function getStatusBadgeClass(status) {
const base = 'text-xs px-2 py-1 rounded-full';
switch(status.color) {
  case 'green': return `${base} bg-green-100 text-green-700`;
  case 'emerald': return `${base} bg-emerald-100 text-emerald-700`;
  case 'yellow': return `${base} bg-yellow-100 text-yellow-700`;
  case 'blue': return `${base} bg-blue-100 text-blue-700`;
  default: return `${base} bg-gray-100 text-gray-700`;
}
}

function getPaidPercentageColor(percentage) {
if (percentage >= 75) return 'text-green-600';
if (percentage >= 50) return 'text-yellow-600';
if (percentage >= 25) return 'text-orange-600';
return 'text-red-600';
}

function getRankColor(rank) {
if (rank === 1) return 'bg-yellow-500';
if (rank === 2) return 'bg-gray-400';
if (rank === 3) return 'bg-orange-500';
return 'bg-gray-300';
}

function goToEvent(eventId) {
router.visit(`/treasurer/collections/${eventId}`);
}

function initTrendChart() {
if (trendChart.value && props.monthlyTrend.length > 0) {
  if (trendChartInstance) trendChartInstance.destroy();
  
  const labels = props.monthlyTrend.map(item => item.month);
  const data = props.monthlyTrend.map(item => item.total);
  
  trendChartInstance = new Chart(trendChart.value, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Collection Amount (₱)',
        data: data,
        borderColor: 'rgb(16, 185, 129)',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        tension: 0.4,
        fill: true,
        pointBackgroundColor: 'rgb(16, 185, 129)',
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
              return `₱${context.raw.toLocaleString()}`;
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
            color: '#f0f0f0'
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
}

onMounted(() => {
initTrendChart();
});
</script>