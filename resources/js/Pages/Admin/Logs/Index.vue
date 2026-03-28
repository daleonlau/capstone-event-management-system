<template>
    <AdminLayout>
      <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              Activity Logs
            </h1>
            <p class="text-gray-500 mt-1">Track user sessions and individual actions</p>
          </div>
          <div class="flex gap-3">
            <button 
              @click="exportLogs" 
              class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              Export Logs
            </button>
          </div>
        </div>
  
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-xs text-gray-500">Total Sessions</p>
            <p class="text-2xl font-bold text-gray-800">{{ stats.total_sessions || 0 }}</p>
          </div>
          <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-xs text-gray-500">Completed</p>
            <p class="text-2xl font-bold text-green-600">{{ stats.completed_sessions || 0 }}</p>
          </div>
          <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-xs text-gray-500">Active Now</p>
            <p class="text-2xl font-bold text-amber-600">{{ stats.active_sessions || 0 }}</p>
          </div>
          <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-xs text-gray-500">Today's Logs</p>
            <p class="text-2xl font-bold text-emerald-600">{{ stats.today_logs || 0 }}</p>
          </div>
        </div>
  
        <!-- Tabs -->
        <div class="border-b border-gray-200">
          <nav class="flex gap-4">
            <button 
              @click="activeTab = 'sessions'" 
              :class="activeTab === 'sessions' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'"
              class="px-4 py-2 border-b-2 font-medium transition"
            >
              User Sessions ({{ stats.total_sessions || 0 }})
            </button>
            <button 
              @click="activeTab = 'actions'" 
              :class="activeTab === 'actions' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'"
              class="px-4 py-2 border-b-2 font-medium transition"
            >
              User Actions ({{ stats.total_actions || 0 }})
            </button>
          </nav>
        </div>
  
        <!-- Filter Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filter Logs
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
              <input 
                v-model="filters.search" 
                type="text" 
                placeholder="Search by user, action..."
                class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                @input="applyFilters"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
              <input 
                v-model="filters.date_from" 
                type="date" 
                class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                @change="applyFilters"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
              <input 
                v-model="filters.date_to" 
                type="date" 
                class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                @change="applyFilters"
              >
            </div>
            <div v-if="activeTab === 'sessions'">
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select v-model="filters.status" class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500" @change="applyFilters">
                <option value="">All Sessions</option>
                <option value="completed">Completed (Has Logout)</option>
                <option value="active">Active (Still Logged In)</option>
              </select>
            </div>
            <div v-else>
              <label class="block text-sm font-medium text-gray-700 mb-2">Action Type</label>
              <input 
                v-model="filters.action_type" 
                type="text" 
                placeholder="e.g., create, update, delete"
                class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                @input="applyFilters"
              >
            </div>
          </div>
          
          <div class="flex gap-2 mt-4">
            <button @click="applyFilters" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
              Apply Filters
            </button>
            <button @click="clearFilters" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
              Clear
            </button>
          </div>
        </div>
  
        <!-- Sessions View -->
        <div v-if="activeTab === 'sessions'" class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="divide-y divide-gray-200 max-h-[600px] overflow-y-auto">
            <div v-for="session in filteredSessions" :key="session.session_id" 
                 class="p-4 hover:bg-gray-50 transition-all duration-300"
                 :class="{
                   'border-l-4 border-l-emerald-500': session.status === 'completed',
                   'border-l-4 border-l-amber-500': session.status === 'active',
                   'border-l-4 border-l-red-500': session.status === 'incomplete'
                 }">
              <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <!-- User Info -->
                <div class="flex items-center gap-3 min-w-[200px]">
                  <div class="w-12 h-12 rounded-full bg-gradient-to-r from-emerald-600 to-teal-600 flex items-center justify-center text-white font-bold text-lg">
                    {{ session.user_name?.charAt(0) || 'U' }}
                  </div>
                  <div>
                    <p class="font-semibold text-gray-800">{{ session.user_name || 'Unknown User' }}</p>
                    <p class="text-xs text-gray-500">{{ session.user_email || 'N/A' }}</p>
                    <span class="text-xs font-mono text-gray-400">{{ session.session_id }}</span>
                  </div>
                </div>
  
                <!-- Login and Logout Times -->
                <div class="flex-1">
                  <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <!-- Login Time -->
                    <div class="flex-1 bg-green-50 rounded-lg p-2">
                      <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <div>
                          <p class="text-xs text-gray-500">Login</p>
                          <p class="font-semibold text-gray-800">{{ session.login_time_formatted || 'N/A' }}</p>
                        </div>
                      </div>
                    </div>
  
                    <!-- Arrow -->
                    <div class="hidden sm:block text-gray-400">
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                      </svg>
                    </div>
  
                    <!-- Logout Time -->
                    <div class="flex-1" :class="session.logout_time ? 'bg-red-50' : 'bg-amber-50'">
                      <div class="flex items-center gap-2 p-2 rounded-lg">
                        <svg class="w-4 h-4" :class="session.logout_time ? 'text-red-600' : 'text-amber-600'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <div>
                          <p class="text-xs text-gray-500">Logout</p>
                          <p class="font-semibold" :class="session.logout_time ? 'text-gray-800' : 'text-amber-600'">
                            {{ session.logout_time_formatted || 'Still logged in' }}
                          </p>
                        </div>
                      </div>
                    </div>
  
                    <!-- Duration -->
                    <span v-if="session.duration" class="px-3 py-1 bg-emerald-600 text-white text-xs rounded-full whitespace-nowrap">
                      <i class="fas fa-hourglass-half mr-1"></i>
                      {{ session.duration }}
                    </span>
                  </div>
                </div>
  
                <!-- Status and Device Info -->
                <div class="flex flex-col items-end gap-2 min-w-[180px]">
                  <span :class="[
                    'px-3 py-1 text-xs rounded-full',
                    session.status === 'completed' ? 'bg-green-100 text-green-700' :
                    session.status === 'active' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700'
                  ]">
                    <span class="mr-1">●</span>
                    {{ session.status === 'completed' ? 'Completed' : session.status === 'active' ? 'Active' : 'Incomplete' }}
                  </span>
                  <div class="flex gap-1">
                    <span class="px-2 py-1 bg-gray-100 rounded text-xs text-gray-600">
                      <svg class="w-3 h-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                      </svg>
                      {{ session.ip_address || 'N/A' }}
                    </span>
                    <span class="px-2 py-1 bg-gray-100 rounded text-xs text-gray-600">
                      <svg class="w-3 h-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      {{ session.device || 'Unknown' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="filteredSessions.length === 0" class="p-12 text-center text-gray-500">
              <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <p>No user sessions found</p>
            </div>
          </div>
        </div>
  
        <!-- Actions View -->
        <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="divide-y divide-gray-200 max-h-[600px] overflow-y-auto">
            <div v-for="action in filteredActions" :key="action.id" 
                 class="p-4 hover:bg-gray-50 transition-all duration-300 border-l-4 border-l-amber-500">
              <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gradient-to-r from-amber-600 to-orange-600 flex items-center justify-center text-white font-bold">
                    {{ action.user_name?.charAt(0) || 'U' }}
                  </div>
                  <div>
                    <p class="font-semibold text-gray-800">{{ action.user_name || 'Unknown User' }}</p>
                    <p class="text-xs text-gray-500">{{ action.user_email }}</p>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                      <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs rounded-full">
                        {{ action.action }}
                      </span>
                      <span class="text-xs text-gray-500">
                        <svg class="w-3 h-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ action.created_at_readable }}
                      </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ action.description }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <div class="flex flex-wrap justify-end gap-1">
                    <span class="px-2 py-1 bg-gray-100 rounded text-xs text-gray-600">
                      <svg class="w-3 h-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                      </svg>
                      {{ action.ip_address || 'N/A' }}
                    </span>
                    <span class="px-2 py-1 bg-gray-100 rounded text-xs text-gray-600">
                      <svg class="w-3 h-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      {{ action.device || 'Unknown' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="filteredActions.length === 0" class="p-12 text-center text-gray-500">
              <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <p>No user actions found</p>
            </div>
          </div>
        </div>
      </div>
    </AdminLayout>
  </template>
  
  <script setup>
  import { ref, computed, watch } from 'vue';
  import { router } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';
  
  const props = defineProps({
    sessions: {
      type: Array,
      default: () => []
    },
    actionLogs: {
      type: Array,
      default: () => []
    },
    stats: {
      type: Object,
      default: () => ({})
    },
    actionTypes: {
      type: Array,
      default: () => []
    },
    users: {
      type: Array,
      default: () => []
    },
    filters: {
      type: Object,
      default: () => ({})
    }
  });
  
  const activeTab = ref('sessions');
  
  // Filter states
  const filters = ref({
    search: props.filters?.search || '',
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || '',
    status: props.filters?.status || '',
    action_type: props.filters?.action_type || ''
  });
  
  // Filtered sessions
  const filteredSessions = computed(() => {
    let filtered = [...props.sessions];
    
    if (filters.value.search) {
      const search = filters.value.search.toLowerCase();
      filtered = filtered.filter(s => 
        s.user_name?.toLowerCase().includes(search) ||
        s.user_email?.toLowerCase().includes(search) ||
        s.ip_address?.toLowerCase().includes(search)
      );
    }
    
    if (filters.value.date_from) {
      filtered = filtered.filter(s => {
        const date = s.login_time || s.logout_time;
        return date && date >= filters.value.date_from;
      });
    }
    
    if (filters.value.date_to) {
      filtered = filtered.filter(s => {
        const date = s.login_time || s.logout_time;
        return date && date <= filters.value.date_to;
      });
    }
    
    if (filters.value.status) {
      filtered = filtered.filter(s => s.status === filters.value.status);
    }
    
    return filtered;
  });
  
  // Filtered actions
  const filteredActions = computed(() => {
    let filtered = [...props.actionLogs];
    
    if (filters.value.search) {
      const search = filters.value.search.toLowerCase();
      filtered = filtered.filter(a => 
        a.user_name?.toLowerCase().includes(search) ||
        a.user_email?.toLowerCase().includes(search) ||
        a.action?.toLowerCase().includes(search) ||
        a.description?.toLowerCase().includes(search)
      );
    }
    
    if (filters.value.date_from) {
      filtered = filtered.filter(a => a.created_at >= filters.value.date_from);
    }
    
    if (filters.value.date_to) {
      filtered = filtered.filter(a => a.created_at <= filters.value.date_to);
    }
    
    if (filters.value.action_type) {
      const actionType = filters.value.action_type.toLowerCase();
      filtered = filtered.filter(a => a.action?.toLowerCase().includes(actionType));
    }
    
    return filtered;
  });
  
  function applyFilters() {
    router.get(route('admin.logs.index'), {
      tab: activeTab.value,
      search: filters.value.search,
      date_from: filters.value.date_from,
      date_to: filters.value.date_to,
      status: filters.value.status,
      action_type: filters.value.action_type
    }, {
      preserveState: true,
      replace: true
    });
  }
  
  function clearFilters() {
    filters.value = {
      search: '',
      date_from: '',
      date_to: '',
      status: '',
      action_type: ''
    };
    applyFilters();
  }
  
  function exportLogs() {
    const params = new URLSearchParams();
    params.append('type', activeTab.value);
    if (filters.value.date_from) params.append('date_from', filters.value.date_from);
    if (filters.value.date_to) params.append('date_to', filters.value.date_to);
    
    window.open(`/admin/logs/export?${params.toString()}`, '_blank');
  }
  </script>