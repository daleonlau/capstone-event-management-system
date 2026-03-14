<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full text-center">
        <!-- Info Icon -->
        <div class="mb-8">
          <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full mx-auto flex items-center justify-center shadow-xl">
            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
  
        <!-- Already Submitted Message -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Already Submitted</h1>
        <p class="text-lg text-gray-600 mb-2">
          You have already submitted an evaluation for
        </p>
        <p class="text-xl font-semibold text-gray-800 mb-4">
          {{ evaluation.event_name }}
        </p>
        <p class="text-sm text-gray-500">
          Thank you for your feedback!
        </p>
  
        <!-- Decorative Elements -->
        <div class="relative mt-12">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-gradient-to-br from-blue-50 to-indigo-50 text-gray-500">
              CSUCC EMS
            </span>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    evaluation: {
      type: Object,
      required: true
    }
  });
  </script>