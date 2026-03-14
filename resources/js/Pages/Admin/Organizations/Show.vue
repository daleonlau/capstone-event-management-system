<template>
  <AdminLayout>
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Organization Details</h1>
          <p class="text-gray-500 mt-1">View complete information about this organization</p>
        </div>
        <Link 
          href="/admin/organizations" 
          class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all duration-300 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Back to List
        </Link>
      </div>

      <!-- Organization Info Card -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-emerald-500 to-emerald-700"></div>
        <div class="px-8 pb-8">
          <div class="flex items-end -mt-12 mb-6">
            <div class="w-24 h-24 bg-white rounded-2xl shadow-lg p-1">
              <div class="w-full h-full bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center">
                <span class="text-white text-3xl font-bold">{{ organization.name?.charAt(0) || 'O' }}</span>
              </div>
            </div>
            <div class="ml-6">
              <h2 class="text-2xl font-bold text-gray-800">{{ organization.name }}</h2>
              <p class="text-gray-500">{{ organization.email }}</p>
            </div>
            <div class="ml-auto">
              <span class="px-4 py-2 bg-emerald-100 text-emerald-700 rounded-xl font-medium text-sm">
                Registered {{ formatDate(organization.created_at) }}
              </span>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">Total Members</p>
              <p class="text-2xl font-bold text-gray-800">{{ organization.organization_users?.length || 0 }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">Assigned Departments</p>
              <p class="text-2xl font-bold text-gray-800">{{ organization.organization_settings?.assigned_departments?.length || 0 }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-sm text-gray-500">Assigned Courses</p>
              <p class="text-2xl font-bold text-gray-800">{{ organization.organization_settings?.assigned_courses?.length || 0 }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Assigned Departments & Courses -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </span>
          Assigned Departments & Courses
        </h2>
        
        <div v-if="organization.organization_settings" class="space-y-4">
          <div v-for="deptId in organization.organization_settings.assigned_departments" :key="deptId" class="border rounded-xl p-4">
            <h3 class="font-semibold text-emerald-600 mb-2">{{ getDepartmentName(deptId) }}</h3>
            <div class="grid grid-cols-2 gap-2">
              <template v-if="getCoursesForDepartment(deptId).length > 0">
                <span v-for="courseId in getCoursesForDepartment(deptId)" :key="courseId" class="text-sm text-gray-600 flex items-center gap-1">
                  <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span>
                  {{ getCourseName(courseId) }}
                </span>
              </template>
              <p v-else class="text-sm text-gray-400 col-span-2">No courses assigned to this department</p>
            </div>
          </div>
          <div v-if="!organization.organization_settings.assigned_departments?.length" class="text-gray-500 text-center py-4">
            No departments assigned to this organization.
          </div>
        </div>
        <div v-else class="text-gray-500 text-center py-4">
          No departments/courses assigned to this organization.
        </div>
      </div>

      <!-- Organization Members -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <span class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </span>
          Organization Members
        </h2>
        
        <div v-if="organization.organization_users?.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div v-for="user in organization.organization_users" :key="user.id" class="border rounded-xl p-4 hover:shadow-md transition">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold">{{ user.name.charAt(0) }}</span>
              </div>
              <div class="flex-1">
                <p class="font-medium text-gray-800">{{ user.name }}</p>
                <p class="text-sm text-gray-500">{{ user.email }}</p>
                <span class="inline-block mt-1 text-xs px-2 py-1 rounded-full" 
                  :class="{
                    'bg-purple-100 text-purple-700': user.role === 'president',
                    'bg-yellow-100 text-yellow-700': user.role === 'adviser',
                    'bg-orange-100 text-orange-700': user.role === 'treasurer'
                  }"
                >
                  {{ user.role.charAt(0).toUpperCase() + user.role.slice(1) }}
                </span>
              </div>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-500 text-center py-4">No members found for this organization.</p>
      </div>

      <!-- Activity Summary -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </span>
          Activity Summary
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-sm text-gray-500">Total Events Created</p>
            <p class="text-2xl font-bold text-gray-800">0</p>
          </div>
          <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-sm text-gray-500">Total Students</p>
            <p class="text-2xl font-bold text-gray-800">0</p>
          </div>
          <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-sm text-gray-500">Total Collections</p>
            <p class="text-2xl font-bold text-gray-800">₱0.00</p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  organization: {
    type: Object,
    default: () => ({})
  },
  departments: {
    type: Array,
    default: () => []
  },
  courses: {
    type: Array,
    default: () => []
  }
});

function formatDate(date) {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

function getDepartmentName(deptId) {
  const dept = props.departments.find(d => d.id === deptId);
  return dept ? dept.name : 'Unknown Department';
}

function getCourseName(courseId) {
  const course = props.courses.find(c => c.id === courseId);
  return course ? course.name : 'Unknown Course';
}

function getCoursesForDepartment(deptId) {
  const dept = props.departments.find(d => d.id === deptId);
  if (!dept) return [];
  
  const assignedCourseIds = props.organization?.organization_settings?.assigned_courses || [];
  
  // Return course IDs that belong to this department AND are assigned
  return dept.courses
    .filter(course => assignedCourseIds.includes(course.id))
    .map(course => course.id);
}
</script>