<template>
  <div 
    class="min-h-screen flex flex-col relative"
    :style="{ backgroundImage: `url('/images/campus-bg')`, backgroundSize: 'cover', backgroundPosition: 'center', backgroundRepeat: 'no-repeat' }"
  >
    <!-- Dark Overlay for better readability -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    
    <!-- Modern Header -->
    <header class="relative z-10 bg-white/10 backdrop-blur-md border-b border-white/20">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-400 rounded-xl flex items-center justify-center shadow-lg">
              <span class="text-white font-bold text-xl">C</span>
            </div>
            <span class="text-white font-bold text-xl tracking-tight">CSUCC EMS</span>
          </div>
          <div class="flex items-center space-x-4">
            <a href="#" class="text-white/80 hover:text-white transition-colors text-sm font-medium">About</a>
            <a href="#" class="text-white/80 hover:text-white transition-colors text-sm font-medium">Contact</a>
            <a href="#" class="text-white/80 hover:text-white transition-colors text-sm font-medium">Help</a>
          </div>
        </div>
      </nav>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
      <div class="max-w-md w-full">
        <!-- Clean Login Card -->
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-8 transform hover:scale-[1.02] transition-all duration-500 border border-white/30">
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
            <div v-if="form.errors.email || form.errors.password" class="mb-6 p-3 bg-red-50 border-l-4 border-red-500 rounded-lg">
              <p class="text-red-700 text-sm">{{ form.errors.email || form.errors.password }}</p>
            </div>
          </Transition>

          <!-- Success/Info Message -->
          <Transition name="slide">
            <div v-if="infoMessage" class="mb-6 p-3 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
              <p class="text-blue-700 text-sm">{{ infoMessage }}</p>
            </div>
          </Transition>

          <!-- Login Form -->
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Email Field -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
              <div class="relative group">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                  </svg>
                </span>
                <input
                  v-model="form.email"
                  type="email"
                  autocomplete="username"
                  class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-200 bg-white/50"
                  placeholder="Enter your email"
                  required
                />
              </div>
            </div>

            <!-- Password Field -->
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="block text-sm font-semibold text-gray-700">Password</label>
                <button 
                  type="button" 
                  @click="showPassword = !showPassword" 
                  class="text-xs font-medium text-emerald-600 hover:text-emerald-800 transition-colors"
                >
                  {{ showPassword ? 'Hide' : 'Show' }}
                </button>
              </div>
              <div class="relative group">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </span>
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  autocomplete="current-password"
                  class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-200 bg-white/50"
                  placeholder="Enter your password"
                  required
                />
              </div>
            </div>

            <!-- Login Button -->
            <button
              type="submit"
              class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-3 px-4 rounded-xl hover:from-emerald-700 hover:to-teal-700 hover:shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 font-semibold"
              :disabled="form.processing"
            >
              <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ form.processing ? 'Signing in...' : 'Sign In' }}</span>
            </button>
          </form>

          <!-- Admin Contact Notice -->
          <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center justify-center gap-2 text-sm text-gray-500">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              <span>Password can only be changed after login or by contacting the administrator</span>
            </div>
          </div>
          
          <!-- Simple Sign Up Link -->
          <p class="text-center text-sm text-gray-500 mt-6">
            Don't have an account? 
            <a href="#" class="text-emerald-600 hover:text-emerald-800 font-semibold transition-colors">
              Contact Administrator
            </a>
          </p>
        </div>
        
        <!-- Additional Info Card -->
        <div class="mt-6 text-center">
          <p class="text-white/70 text-xs backdrop-blur-sm inline-block px-4 py-2 rounded-full bg-black/20">
            🔒 Secure access for authorized personnel only
          </p>
        </div>
      </div>
    </main>

    <!-- Clean Footer -->
    <footer class="relative z-10 bg-white/5 backdrop-blur-md border-t border-white/20">
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
const infoMessage = ref('');

const form = useForm({
  email: '',
  password: '',
});

const submit = () => {
  // Clear any previous info message
  infoMessage.value = '';
  
  // Submit the form
  form.post('/login', {
    onError: (errors) => {
      // Handle specific error cases
      if (errors.password === 'The password is incorrect.' || errors.email === 'These credentials do not match our records.') {
        // You could set an info message here if needed
        console.log('Invalid credentials');
      }
    }
  });
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

/* Additional smooth transitions */
input, button {
  transition: all 0.2s ease;
}

/* Hover effect for the card */
.bg-white\/95 {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.bg-white\/95:hover {
  box-shadow: 0 25px 40px -12px rgba(0, 0, 0, 0.25);
}
</style>