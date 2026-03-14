<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Overview</h1>
        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl shadow-sm">
          <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
          <span class="text-sm text-gray-600">Live</span>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Organizations -->
        <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <span class="text-sm font-medium text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Total</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-800">{{ totalOrganizations }}</h3>
          <p class="text-sm text-gray-500 mt-1">Registered Organizations</p>
          <div class="mt-4 h-1 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-emerald-500 rounded-full" :style="{ width: '75%' }"></div>
          </div>
        </div>

        <!-- Total Members -->
        <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <span class="text-sm font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Active</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-800">{{ totalOrganizationUsers }}</h3>
          <p class="text-sm text-gray-500 mt-1">Organization Members</p>
        </div>

        <!-- Total Events -->
        <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
              </svg>
            </div>
            <span class="text-sm font-medium text-purple-600 bg-purple-50 px-3 py-1 rounded-full">All</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-800">{{ totalEvents }}</h3>
          <p class="text-sm text-gray-500 mt-1">Total Events Created</p>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <span class="text-sm font-medium text-yellow-600 bg-yellow-50 px-3 py-1 rounded-full">Pending</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-800">{{ pendingEvents }}</h3>
          <p class="text-sm text-gray-500 mt-1">Awaiting Approval</p>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Organizations -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Organizations</h2>
          <div class="space-y-4">
            <div v-for="i in 5" :key="i" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-lg flex items-center justify-center">
                  <span class="text-white font-bold">O</span>
                </div>
                <div>
                  <p class="font-medium text-gray-800">Organization {{ i }}</p>
                  <p class="text-sm text-gray-500">Registered 2 hours ago</p>
                </div>
              </div>
              <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full">Active</span>
            </div>
          </div>
        </div>

        <!-- Recent Events -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Events</h2>
          <div class="space-y-4">
            <div v-for="i in 5" :key="i" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-800">Event {{ i }}</p>
                  <p class="text-sm text-gray-500">Created by Organization {{ i }}</p>
                </div>
              </div>
              <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full">Pending</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
  totalOrganizations: Number,
  totalOrganizationUsers: Number,
  totalEvents: Number,
  pendingEvents: Number,
});
</script>