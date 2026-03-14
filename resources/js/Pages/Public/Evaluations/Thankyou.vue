<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
      <!-- Success Animation -->
      <div class="mb-8">
        <div class="w-24 h-24 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full mx-auto flex items-center justify-center shadow-xl animate-bounce">
          <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
      </div>

      <!-- Thank You Message -->
      <h1 class="text-3xl font-bold text-gray-900 mb-4">Thank You!</h1>
      <p class="text-lg text-gray-600 mb-8">
        Your evaluation has been successfully submitted. We appreciate your feedback!
      </p>

      <!-- Decorative Elements -->
      <div class="relative mt-12">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
          <span class="px-4 bg-gradient-to-br from-emerald-50 to-teal-50 text-gray-500">
            CSUCC EMS
          </span>
        </div>
      </div>

      <!-- Close Tab Instruction -->
      <p class="text-sm text-gray-400 mt-8">
        You may now close this tab.
      </p>
    </div>
  </div>
</template>