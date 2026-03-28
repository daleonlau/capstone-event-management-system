<template>
    <AdminLayout>
      <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              Organizations
            </h1>
            <p class="text-gray-500 mt-1">Manage all registered organizations and their members</p>
          </div>
          <Link 
            href="/admin/organizations/create" 
            class="px-5 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 flex items-center gap-2 group"
          >
            <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Register New Organization
          </Link>
        </div>
  
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
              <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <span class="text-3xl font-bold text-gray-800">{{ organizations.length }}</span>
            </div>
            <h3 class="text-gray-600 font-semibold text-lg">Total Organizations</h3>
            <p class="text-sm text-gray-400 mt-2">Registered in the system</p>
          </div>
  
          <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
              <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
              <span class="text-3xl font-bold text-gray-800">{{ totalMembers }}</span>
            </div>
            <h3 class="text-gray-600 font-semibold text-lg">Total Members</h3>
            <p class="text-sm text-gray-400 mt-2">Across all organizations</p>
          </div>
  
          <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
              <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
              </div>
              <span class="text-3xl font-bold text-gray-800">{{ totalDepartments }}</span>
            </div>
            <h3 class="text-gray-600 font-semibold text-lg">Departments Assigned</h3>
            <p class="text-sm text-gray-400 mt-2">Total department assignments</p>
          </div>
  
          <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
              <div class="p-3 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
              <span class="text-3xl font-bold text-gray-800">{{ totalCourses }}</span>
            </div>
            <h3 class="text-gray-600 font-semibold text-lg">Courses Assigned</h3>
            <p class="text-sm text-gray-400 mt-2">Total course assignments</p>
          </div>
        </div>
  
        <!-- Search and Filter Bar -->
        <div class="bg-white rounded-2xl shadow-lg p-4">
          <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              <input 
                v-model="searchQuery" 
                type="text" 
                placeholder="Search organizations by name or email..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
              >
            </div>
            <div class="flex gap-3">
              <select v-model="sortBy" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                <option value="name">Sort by Name</option>
                <option value="created_at">Sort by Date Created</option>
                <option value="members">Sort by Members</option>
              </select>
              <select v-model="sortOrder" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                <option value="asc">Ascending</option>
                <option value="desc">Descending</option>
              </select>
            </div>
          </div>
        </div>
  
        <!-- Organizations Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
          <div v-for="org in paginatedOrganizations" :key="org.id" 
               class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
            <!-- Card Header with Gradient Background -->
            <div class="h-24 bg-gradient-to-r from-emerald-500 to-teal-600 relative">
              <div class="absolute -bottom-8 left-4">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                  <span class="text-2xl font-bold text-emerald-600">{{ org.name.charAt(0) }}</span>
                </div>
              </div>
              <div class="absolute top-4 right-4">
                <div class="flex gap-2">
                  <Link :href="`/admin/organizations/${org.id}`" 
                        class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition text-white"
                        title="View Details">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </Link>
                  <button @click="confirmDelete(org)" 
                          class="p-2 bg-white/20 rounded-lg hover:bg-red-500/80 transition text-white"
                          title="Delete Organization">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
  
            <!-- Card Content -->
            <div class="p-4 pt-10">
              <h3 class="font-bold text-lg text-gray-800 mb-1">{{ org.name }}</h3>
              <p class="text-sm text-gray-500 mb-3 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                {{ org.email }}
              </p>
  
              <!-- Stats Row -->
              <div class="grid grid-cols-3 gap-2 mb-4 py-3 border-t border-b border-gray-100">
                <div class="text-center">
                  <p class="text-xl font-bold text-emerald-600">{{ org.members_count }}</p>
                  <p class="text-xs text-gray-500">Members</p>
                </div>
                <div class="text-center">
                  <p class="text-xl font-bold text-blue-600">{{ org.departments_count }}</p>
                  <p class="text-xs text-gray-500">Departments</p>
                </div>
                <div class="text-center">
                  <p class="text-xl font-bold text-orange-600">{{ org.courses_count }}</p>
                  <p class="text-xs text-gray-500">Courses</p>
                </div>
              </div>
  
              <!-- Footer -->
              <div class="flex justify-between items-center">
                <div class="flex items-center gap-1 text-xs text-gray-400">
                  <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  {{ formatDate(org.created_at) }}
                </div>
                <Link :href="`/admin/organizations/${org.id}`" 
                      class="text-sm text-emerald-600 hover:text-emerald-700 font-medium flex items-center gap-1 group">
                  View Details
                  <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </Link>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Empty State -->
        <div v-if="filteredOrganizations.length === 0" class="bg-white rounded-2xl shadow-lg p-12 text-center">
          <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No Organizations Found</h3>
          <p class="text-gray-500 mb-4">Get started by registering your first organization.</p>
          <Link href="/admin/organizations/create" 
                class="inline-flex items-center gap-2 px-5 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Register Organization
          </Link>
        </div>
  
        <!-- Pagination -->
        <div v-if="totalPages > 1" class="flex justify-center items-center gap-2">
          <button 
            @click="currentPage--" 
            :disabled="currentPage === 1"
            class="px-4 py-2 border rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
          >
            Previous
          </button>
          <div class="flex gap-1">
            <button 
              v-for="page in visiblePages" 
              :key="page"
              @click="currentPage = page"
              :class="[
                'px-4 py-2 rounded-lg transition',
                currentPage === page 
                  ? 'bg-emerald-600 text-white' 
                  : 'border hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
          </div>
          <button 
            @click="currentPage++" 
            :disabled="currentPage === totalPages"
            class="px-4 py-2 border rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
          >
            Next
          </button>
        </div>
  
        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
          <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
              <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showDeleteModal = false"></div>
              <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
                  <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                          <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white">Delete Organization</h3>
                      </div>
                      <button @click="showDeleteModal = false" class="text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>
                  </div>
                  <div class="p-6">
                    <div class="mb-4 rounded-lg bg-red-50 p-4">
                      <div class="flex">
                        <div class="flex-shrink-0">
                          <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                          </svg>
                        </div>
                        <div class="ml-3">
                          <p class="text-sm text-red-700">
                            Are you sure you want to delete <strong class="font-semibold">{{ organizationToDelete?.name }}</strong>?
                          </p>
                          <p class="text-sm text-red-700 mt-1">
                            This action cannot be undone. This will permanently delete the organization and all its members, events, and evaluations.
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="flex justify-end gap-3 bg-gray-50 px-6 py-4">
                    <button
                      @click="showDeleteModal = false"
                      class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition"
                    >
                      Cancel
                    </button>
                    <button
                      @click="deleteOrganization"
                      :disabled="deleting"
                      class="rounded-lg bg-gradient-to-r from-red-600 to-red-700 px-4 py-2 text-sm font-medium text-white hover:from-red-700 hover:to-red-800 transition disabled:opacity-50 flex items-center gap-2"
                    >
                      <svg v-if="deleting" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      <span>{{ deleting ? 'Deleting...' : 'Delete Organization' }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </Transition>
        </Teleport>
  
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
      </div>
    </AdminLayout>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue';
  import { Link, router } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';
  import axios from 'axios';
  
  const props = defineProps({
    organizations: {
      type: Array,
      default: () => []
    }
  });
  
  // Search and filter
  const searchQuery = ref('');
  const sortBy = ref('name');
  const sortOrder = ref('asc');
  const currentPage = ref(1);
  const itemsPerPage = ref(9);
  const showDeleteModal = ref(false);
  const organizationToDelete = ref(null);
  const deleting = ref(false);
  const toast = ref({ show: false, message: '', type: 'success', bgClass: '' });
  
  // Computed totals
  const totalMembers = computed(() => {
    return props.organizations.reduce((sum, org) => sum + (org.members_count || 0), 0);
  });
  
  const totalDepartments = computed(() => {
    return props.organizations.reduce((sum, org) => sum + (org.departments_count || 0), 0);
  });
  
  const totalCourses = computed(() => {
    return props.organizations.reduce((sum, org) => sum + (org.courses_count || 0), 0);
  });
  
  // Filtered and sorted organizations
  const filteredOrganizations = computed(() => {
    let filtered = [...props.organizations];
    
    // Search filter
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase();
      filtered = filtered.filter(org => 
        org.name.toLowerCase().includes(query) || 
        org.email.toLowerCase().includes(query)
      );
    }
    
    // Sort
    filtered.sort((a, b) => {
      let aVal, bVal;
      if (sortBy.value === 'name') {
        aVal = a.name;
        bVal = b.name;
      } else if (sortBy.value === 'created_at') {
        aVal = new Date(a.created_at);
        bVal = new Date(b.created_at);
      } else if (sortBy.value === 'members') {
        aVal = a.members_count;
        bVal = b.members_count;
      }
      
      if (sortOrder.value === 'asc') {
        return aVal > bVal ? 1 : -1;
      } else {
        return aVal < bVal ? 1 : -1;
      }
    });
    
    return filtered;
  });
  
  // Pagination
  const totalPages = computed(() => Math.ceil(filteredOrganizations.value.length / itemsPerPage.value));
  const paginatedOrganizations = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredOrganizations.value.slice(start, end);
  });
  
  const visiblePages = computed(() => {
    const pages = [];
    const maxVisible = 5;
    let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
    let end = Math.min(totalPages.value, start + maxVisible - 1);
    
    if (end - start + 1 < maxVisible) {
      start = Math.max(1, end - maxVisible + 1);
    }
    
    for (let i = start; i <= end; i++) {
      pages.push(i);
    }
    return pages;
  });
  
  function formatDate(date) {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
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
    
    setTimeout(() => toast.value.show = false, 3000);
  }
  
  function confirmDelete(org) {
    organizationToDelete.value = org;
    showDeleteModal.value = true;
  }
  
  async function deleteOrganization() {
    if (!organizationToDelete.value) return;
    
    deleting.value = true;
    try {
      const response = await axios.delete(`/admin/organizations/${organizationToDelete.value.id}`);
      if (response.data.success) {
        showToast('Organization deleted successfully!', 'success');
        setTimeout(() => router.reload(), 1500);
      }
    } catch (error) {
      showToast(error.response?.data?.error || 'Failed to delete organization', 'error');
    } finally {
      deleting.value = false;
      showDeleteModal.value = false;
      organizationToDelete.value = null;
    }
  }
  </script>
  
  <style scoped>
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  .animate-spin {
    animation: spin 1s linear infinite;
  }
  </style>