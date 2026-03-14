<template>
    <div class="min-h-screen bg-gradient-to-br from-red-50 to-orange-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full text-center">
        <!-- Warning Icon -->
        <div class="mb-8">
          <div class="w-24 h-24 bg-gradient-to-r from-red-500 to-orange-500 rounded-full mx-auto flex items-center justify-center shadow-xl">
            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
  
        <!-- Expired Message -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Evaluation Expired</h1>
        <p class="text-lg text-gray-600 mb-4">
          {{ message || 'This evaluation form is no longer available.' }}
        </p>
        <p class="text-sm text-gray-500">
          The time period for submitting responses has ended.
        </p>
  
        <!-- Decorative Elements -->
        <div class="relative mt-12">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-gradient-to-br from-red-50 to-orange-50 text-gray-500">
              CSUCC EMS
            </span>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    message: {
      type: String,
      default: 'This evaluation form has expired.'
    }
  });
  </script>