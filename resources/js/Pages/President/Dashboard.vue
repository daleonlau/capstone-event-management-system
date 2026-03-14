<template>
    <OrganizationUserLayout>
      <div class="space-y-8">
        <!-- Welcome Header with Date -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div>
            <h1 class="text-4xl font-bold text-gray-800">
              Welcome back, <span class="text-emerald-600">{{ user?.name?.split(' ')[0] || 'President' }}</span>!
            </h1>
            <p class="text-gray-500 mt-2 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
              </svg>
              {{ currentDate }}
            </p>
          </div>
          
          <!-- Quick Actions Dropdown -->
          <div class="relative" ref="quickActionsRef">
            <button 
              @click="toggleQuickActions"
              class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition shadow-sm"
            >
              <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              <span>Quick Actions</span>
              <svg class="w-4 h-4 ml-2" :class="{ 'rotate-180': showQuickActions }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            
            <!-- Dropdown Menu -->
            <Transition name="dropdown">
              <div v-if="showQuickActions" class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                <Link href="/president/events/create" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                  <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-800">Create Event</p>
                    <p class="text-xs text-gray-500">Start a new event proposal</p>
                  </div>
                </Link>
                
                <Link href="/president/students/create" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                  <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-800">Add Student</p>
                    <p class="text-xs text-gray-500">Register a new student</p>
                  </div>
                </Link>
                
                <Link href="/president/students/bulk-upload" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                  <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-800">Bulk Upload</p>
                    <p class="text-xs text-gray-500">Import multiple students</p>
                  </div>
                </Link>
              </div>
            </Transition>
          </div>
        </div>
  
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Total Events Card -->
          <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Total</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ stats.total_events }}</h3>
            <p class="text-sm text-gray-500 mt-1">Total Events Created</p>
            <div class="mt-4 flex items-center gap-2">
              <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full" :style="{ width: `${Math.min((stats.total_events / 50) * 100, 100)}%` }"></div>
              </div>
              <span class="text-xs text-gray-500">{{ Math.min(((stats.total_events / 50) * 100).toFixed(0), 100) }}%</span>
            </div>
          </div>
  
          <!-- Pending Events Card -->
          <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-yellow-600 bg-yellow-50 px-3 py-1 rounded-full">Pending</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ stats.pending_events }}</h3>
            <p class="text-sm text-gray-500 mt-1">Awaiting Approval</p>
            <div class="mt-4 flex items-center gap-2">
              <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-yellow-500 rounded-full" :style="{ width: `${(stats.pending_events / (stats.total_events || 1)) * 100}%` }"></div>
              </div>
              <span class="text-xs text-gray-500">{{ ((stats.pending_events / (stats.total_events || 1)) * 100).toFixed(0) }}%</span>
            </div>
          </div>
  
          <!-- Approved Events Card -->
          <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <span class="text-sm font-medium text-green-600 bg-green-50 px-3 py-1 rounded-full">Approved</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ stats.approved_events }}</h3>
            <p class="text-sm text-gray-500 mt-1">Ready for Implementation</p>
            <div class="mt-4 flex items-center gap-2">
              <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 rounded-full" :style="{ width: `${(stats.approved_events / (stats.total_events || 1)) * 100}%` }"></div>
              </div>
              <span class="text-xs text-gray-500">{{ ((stats.approved_events / (stats.total_events || 1)) * 100).toFixed(0) }}%</span>
            </div>
          </div>
  
          <!-- Total Students Card -->
          <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-purple-600 bg-purple-50 px-3 py-1 rounded-full">Students</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ stats.total_students }}</h3>
            <p class="text-sm text-gray-500 mt-1">Enrolled Students</p>
            <div class="mt-4 flex items-center gap-2">
              <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-purple-500 rounded-full" :style="{ width: `${(stats.total_students / 200) * 100}%` }"></div>
              </div>
              <span class="text-xs text-gray-500">{{ ((stats.total_students / 200) * 100).toFixed(0) }}%</span>
            </div>
          </div>
        </div>
  
        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Events Overview Chart -->
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-lg font-semibold text-gray-800">Events Overview</h2>
              <select v-model="chartPeriod" class="px-3 py-1 border border-gray-300 rounded-lg text-sm">
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
              </select>
            </div>
            <div style="height: 250px;">
              <canvas ref="eventsChartRef"></canvas>
            </div>
          </div>
  
          <!-- Department Distribution - NOW USING REAL DATA -->
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-lg font-semibold text-gray-800">Students by Department</h2>
              <span class="text-sm text-gray-500">{{ stats.total_students }} total</span>
            </div>
            <div style="height: 250px;">
              <canvas ref="departmentChartRef"></canvas>
            </div>
          </div>
        </div>
  
        <!-- Recent Events Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Recent Events</h2>
            <Link href="/president/events" class="text-sm text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
              View All
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
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
                        <span class="text-white font-bold">{{ event.event_name?.charAt(0) }}</span>
                      </div>
                      <div>
                        <p class="font-medium text-gray-900">{{ event.event_name }}</p>
                        <p class="text-xs text-gray-500">ID: {{ event.id }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ event.event_type?.name || 'N/A' }}</td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(event.event_date_start) }}</td>
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
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-6">Recent Activity</h2>
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
      </div>
    </OrganizationUserLayout>
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3';
  import { usePage } from '@inertiajs/vue3';
  import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
  import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
  import Chart from 'chart.js/auto';
  
  const page = usePage();
  const user = page.props.auth?.user;
  
  const props = defineProps({
    stats: {
      type: Object,
      default: () => ({
        total_events: 0,
        pending_events: 0,
        approved_events: 0,
        finished_events: 0,
        total_students: 0,
        pending_document: 0,
        pending_approval: 0,
        rejected_events: 0,
        event_completion_rate: 0,
        approval_rate: 0
      })
    },
    recentEvents: {
      type: Array,
      default: () => []
    },
    studentsByDepartment: {
      type: Array,
      default: () => []
    },
    eventTrends: {
      type: Array,
      default: () => []
    },
    recentActivities: {
      type: Array,
      default: () => []
    }
  });
  
  // State
  const showQuickActions = ref(false);
  const chartPeriod = ref('month');
  const quickActionsRef = ref(null);
  const eventsChartRef = ref(null);
  const departmentChartRef = ref(null);
  let eventsChart = null;
  let departmentChart = null;
  
  // Current date
  const currentDate = computed(() => {
    return new Date().toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  });
  
  // Toggle quick actions
  function toggleQuickActions() {
    showQuickActions.value = !showQuickActions.value;
  }
  
  // Close dropdown when clicking outside
  function handleClickOutside(event) {
    if (quickActionsRef.value && !quickActionsRef.value.contains(event.target)) {
      showQuickActions.value = false;
    }
  }
  
  // Format date
  function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  }
  
  // Format approval status
  function formatApprovalStatus(status) {
    if (!status) return 'N/A';
    return status.split('_').map(word => 
      word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');
  }
  
  // Status badge classes
  function statusBadgeClass(status) {
    const base = 'px-3 py-1 text-xs rounded-full font-medium';
    switch(status) {
      case 'Pending': return `${base} bg-yellow-100 text-yellow-700`;
      case 'Approved': return `${base} bg-green-100 text-green-700`;
      case 'Finished': return `${base} bg-gray-100 text-gray-700`;
      default: return `${base} bg-gray-100 text-gray-700`;
    }
  }
  
  // Approval badge classes
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
  
  // Initialize charts with REAL DATA
  function initCharts() {
    // Events Overview Chart
    if (eventsChartRef.value) {
      // Destroy existing chart if it exists
      if (eventsChart) eventsChart.destroy();
      
      // Process event trends data
      const labels = props.eventTrends.map(item => {
        const [year, month] = item.month.split('-');
        const date = new Date(year, month - 1);
        return date.toLocaleDateString('en-US', { month: 'short' });
      });
      
      const data = props.eventTrends.map(item => item.total);
      
      eventsChart = new Chart(eventsChartRef.value, {
        type: 'line',
        data: {
          labels: labels.length ? labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [
            {
              label: 'Events Created',
              data: data.length ? data : [0, 0, 0, 0, 0, 0],
              borderColor: 'rgb(16, 185, 129)',
              backgroundColor: 'rgba(16, 185, 129, 0.1)',
              tension: 0.4,
              fill: true
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                display: true,
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
  
    // Department Distribution Chart - NOW USING REAL DATA
    if (departmentChartRef.value) {
      // Destroy existing chart if it exists
      if (departmentChart) departmentChart.destroy();
      
      // Prepare data from studentsByDepartment prop
      const hasData = props.studentsByDepartment && props.studentsByDepartment.length > 0;
      
      const labels = hasData 
        ? props.studentsByDepartment.map(item => item.department || 'Unspecified')
        : ['No Data'];
      
      const data = hasData 
        ? props.studentsByDepartment.map(item => item.total)
        : [1]; // Show 1 for "No Data" to make chart visible
      
      const backgroundColors = hasData
        ? [
            'rgba(16, 185, 129, 0.8)',  // emerald
            'rgba(59, 130, 246, 0.8)',  // blue
            'rgba(245, 158, 11, 0.8)',  // yellow
            'rgba(139, 92, 246, 0.8)',  // purple
            'rgba(236, 72, 153, 0.8)',  // pink
            'rgba(249, 115, 22, 0.8)',  // orange
          ]
        : ['rgba(156, 163, 175, 0.8)']; // gray for no data
      
      departmentChart = new Chart(departmentChartRef.value, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [
            {
              data: data,
              backgroundColor: backgroundColors,
              borderWidth: 0
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                boxWidth: 12,
                padding: 15,
                font: {
                  size: 11
                }
              }
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const label = context.label || '';
                  const value = context.raw || 0;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                  return `${label}: ${value} students (${percentage}%)`;
                }
              }
            }
          },
          cutout: '60%'
        }
      });
    }
  }
  
  // Watch for changes in studentsByDepartment to update chart
  watch(() => props.studentsByDepartment, () => {
    initCharts();
  }, { deep: true });
  
  // Initialize charts on mount
  onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    initCharts();
  });
  
  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    if (eventsChart) eventsChart.destroy();
    if (departmentChart) departmentChart.destroy();
  });
  </script>
  
  <style scoped>
  .dropdown-enter-active, .dropdown-leave-active {
    transition: all 0.2s ease;
  }
  .dropdown-enter-from, .dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
  }
  </style>