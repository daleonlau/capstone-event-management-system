<template>
    <OrganizationUserLayout>
      <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Students Overview</h1>
            <p class="text-gray-500 mt-1">View all students in your organization (Read-only)</p>
          </div>
        </div>
  
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500">Total Students</p>
                <p class="text-2xl font-bold text-gray-800">{{ totalStudents }}</p>
              </div>
            </div>
          </div>
  
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500">Departments</p>
                <p class="text-2xl font-bold text-gray-800">{{ departmentCount }}</p>
              </div>
            </div>
          </div>
  
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500">Courses</p>
                <p class="text-2xl font-bold text-gray-800">{{ courseCount }}</p>
              </div>
            </div>
          </div>
  
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500">Year Levels</p>
                <p class="text-2xl font-bold text-gray-800">4</p>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-4">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Search Students</label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search by ID, name, or email..."
                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                @keyup.enter="applyFilters"
              />
            </div>
  
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Department</label>
              <select
                v-model="filters.department"
                @change="applyFilters"
                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
                <option value="">All Departments</option>
                <option v-for="dept in departments" :key="dept" :value="dept">
                  {{ dept }}
                </option>
              </select>
            </div>
  
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Year Level</label>
              <select
                v-model="filters.year_level"
                @change="applyFilters"
                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
                <option value="">All Years</option>
                <option v-for="year in yearLevels" :key="year" :value="year">
                  {{ year }}
                </option>
              </select>
            </div>
          </div>
        </div>
  
        <!-- Students Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Level</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="student in students.data" :key="student.student_id" class="hover:bg-gray-50 transition">
                  <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ student.student_id }}</td>
                  <td class="px-6 py-4">
                    <div class="flex items-center">
                      <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-lg flex items-center justify-center mr-3">
                        <span class="text-white text-xs font-bold">{{ student.firstname?.charAt(0) }}{{ student.lastname?.charAt(0) }}</span>
                      </div>
                      <span class="text-sm text-gray-900">{{ student.firstname }} {{ student.lastname }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-500">{{ student.email }}</td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ student.course }}</td>
                  <td class="px-6 py-4">
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs">{{ student.department }}</span>
                  </td>
                  <td class="px-6 py-4">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">{{ student.yearlevel }}</span>
                  </td>
                </tr>
                <tr v-if="students.data.length === 0">
                  <td colspan="6" class="px-6 py-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p class="text-gray-500">No students found</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
  
          <!-- Pagination -->
          <div v-if="students.links && students.links.length > 3" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-500">
              Showing {{ students.from }} to {{ students.to }} of {{ students.total }} entries
            </div>
            <div class="flex gap-2">
              <button
                v-for="(link, index) in students.links"
                :key="index"
                v-html="link.label"
                @click="goToPage(link.url)"
                class="px-3 py-1 border rounded-lg hover:bg-gray-50 transition disabled:opacity-50"
                :class="{ 'bg-emerald-600 text-white hover:bg-emerald-700': link.active }"
                :disabled="!link.url"
              ></button>
            </div>
          </div>
        </div>
      </div>
    </OrganizationUserLayout>
  </template>
  
  <script setup>
  import { ref, reactive, computed } from 'vue';
  import { router } from '@inertiajs/vue3';
  import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
  
  const props = defineProps({
    students: {
      type: Object,
      default: () => ({ data: [], links: [] })
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    departments: {
      type: Array,
      default: () => []
    },
    yearLevels: {
      type: Array,
      default: () => ['1st Year', '2nd Year', '3rd Year', '4th Year']
    }
  });
  
  // Stats
  const totalStudents = computed(() => props.students.total || 0);
  const departmentCount = computed(() => props.departments.length || 0);
  const courseCount = computed(() => {
    // This would need to be passed from controller
    return new Set(props.students.data?.map(s => s.course)).size || 0;
  });
  
  const filters = reactive({
    search: props.filters?.search || '',
    department: props.filters?.department || '',
    year_level: props.filters?.year_level || ''
  });
  
  function applyFilters() {
    router.get('/adviser/students', filters, { preserveState: true });
  }
  
  function goToPage(url) {
    if (url) router.visit(url, { preserveState: true });
  }
  </script>