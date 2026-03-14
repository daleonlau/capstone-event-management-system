<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-emerald-900 via-emerald-800 to-teal-900">
    <!-- Modern Header -->
    <header class="bg-white/10 backdrop-blur-md border-b border-white/20">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-400 rounded-xl flex items-center justify-center shadow-lg">
              <span class="text-white font-bold text-xl">C</span>
            </div>
            <span class="text-white font-bold text-xl">CSUCC EMS</span>
          </div>
          <div class="flex items-center space-x-4">
            <a href="#" class="text-white/80 hover:text-white transition-colors text-sm">About</a>
            <a href="#" class="text-white/80 hover:text-white transition-colors text-sm">Contact</a>
            <a href="#" class="text-white/80 hover:text-white transition-colors text-sm">Help</a>
          </div>
        </div>
      </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
      <div class="max-w-md w-full">
        <!-- Clean Login Card -->
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-8 transform hover:scale-[1.01] transition-all duration-500 relative z-10 border border-white/30">
          <!-- Logo and Title Section -->
          <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-2xl mx-auto mb-4 flex items-center justify-center shadow-xl">
              <span class="text-white font-bold text-3xl">C</span>
            </div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-800 to-teal-800 bg-clip-text text-transparent">
              Welcome Back
            </h1>
            <p class="text-gray-500 mt-2 text-sm">
              Sign in to access your dashboard
            </p>
          </div>

          <!-- Error Message -->
          <Transition name="slide">
            <div v-if="form.errors.email" class="mb-6 p-3 bg-red-50 border-l-4 border-red-500 rounded-lg">
              <p class="text-red-700 text-sm">{{ form.errors.email }}</p>
            </div>
          </Transition>

          <!-- Login Form -->
          <form @submit.prevent="submit" class="space-y-5">
            <!-- Email Field -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                  </svg>
                </span>
                <input
                  v-model="form.email"
                  type="email"
                  autocomplete="username"
                  class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors"
                  placeholder="Enter your email"
                  required
                />
              </div>
            </div>

            <!-- Password Field -->
            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <button type="button" @click="showPassword = !showPassword" class="text-xs text-emerald-600 hover:text-emerald-800 transition-colors">
                  {{ showPassword ? 'Hide' : 'Show' }}
                </button>
              </div>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </span>
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  autocomplete="current-password"
                  class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors"
                  placeholder="Enter your password"
                  required
                />
              </div>
            </div>

            <!-- Forgot Password Link -->
            <div class="text-right">
              <a href="#" class="text-sm text-emerald-600 hover:text-emerald-800 transition-colors">
                Forgot password?
              </a>
            </div>

            <!-- Login Button -->
            <button
              type="submit"
              class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-2.5 px-4 rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
              :disabled="form.processing"
            >
              <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ form.processing ? 'Signing in...' : 'Sign In' }}</span>
            </button>
          </form>

          <!-- Simple Sign Up Link -->
          <p class="text-center text-sm text-gray-500 mt-6">
            Don't have an account? 
            <a href="#" class="text-emerald-600 hover:text-emerald-800 font-medium transition-colors">
              Contact Administrator
            </a>
          </p>
        </div>
      </div>
    </main>

    <!-- Clean Footer -->
    <footer class="bg-white/5 backdrop-blur-md border-t border-white/20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="flex items-center space-x-2">
            <div class="w-6 h-6 bg-gradient-to-br from-emerald-400 to-teal-400 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-xs">C</span>
            </div>
            <span class="text-white/60 text-xs">Caraga State University - Cabadbaran Campus</span>
          </div>
          <div class="flex items-center space-x-4">
            <a href="#" class="text-white/40 hover:text-white/60 text-xs transition-colors">Privacy</a>
            <span class="text-white/20">•</span>
            <a href="#" class="text-white/40 hover:text-white/60 text-xs transition-colors">Terms</a>
            <span class="text-white/20">•</span>
            <span class="text-white/40 text-xs">© {{ new Date().getFullYear() }}</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const showPassword = ref(false);

const form = useForm({
  email: '',
  password: '',
});

const submit = () => {
  form.post('/login');
};
</script>

<style scoped>
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Slide Transitions */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
}
.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>