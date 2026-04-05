<template>
  <div class="min-h-screen flex flex-col relative">
    <!-- Background Image -->
    <div 
      class="fixed inset-0 bg-cover bg-center bg-no-repeat z-0"
      :style="{ backgroundImage: `url('${bgImageUrl}')` }"
    ></div>
    
    <!-- Very Light Overlay for text readability only -->
    <div class="fixed inset-0 bg-black/20 z-0"></div>
    
    <!-- Green Themed Header - Responsive -->
    <header class="relative z-10 bg-emerald-700 shadow-lg">
      <nav class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-3 sm:py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-2 sm:space-x-3">
            <!-- CSU Logo Image -->
            <img 
              :src="logoPath" 
              alt="CSU Logo" 
              class="w-8 h-8 sm:w-10 sm:h-10 object-contain rounded-lg sm:rounded-xl shadow-lg bg-white p-1"
              @error="logoFailed = true"
            />
            <span class="text-white font-bold text-base sm:text-xl tracking-tight hidden xs:inline">Event Flow</span>
            <span class="text-white font-bold text-sm tracking-tight xs:hidden">EventFlow</span>
          </div>
          <div class="flex items-center space-x-3 sm:space-x-4">
            <a href="#" class="text-white/80 hover:text-white transition-colors text-xs sm:text-sm font-medium">About</a>
            <a href="#" class="text-white/80 hover:text-white transition-colors text-xs sm:text-sm font-medium hidden xs:inline">Contact</a>
            <a href="#" class="text-white/80 hover:text-white transition-colors text-xs sm:text-sm font-medium hidden sm:inline">Help</a>
          </div>
        </div>
      </nav>
    </header>

    <!-- Main Content - Responsive -->
    <main class="relative z-10 flex-1 flex items-center justify-center px-3 sm:px-4 md:px-6 lg:px-8 py-8 sm:py-12 md:py-16">
      <div class="w-full max-w-[90%] xs:max-w-sm sm:max-w-md md:max-w-lg">
        <!-- Clean Login Card - Fully Responsive -->
        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl p-4 sm:p-6 md:p-8 transform hover:scale-[1.02] transition-all duration-500">
          <!-- Logo and Title Section -->
          <div class="text-center mb-6 sm:mb-8">
            <!-- CSU Logo in Card -->
            <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mx-auto mb-3 sm:mb-4 flex items-center justify-center">
              <img 
                :src="logoPath" 
                alt="CSU Logo" 
                class="w-full h-full object-contain rounded-xl sm:rounded-2xl shadow-xl"
                @error="logoFailed = true"
              />
            </div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-emerald-700">
              Welcome Back
            </h1>
            <p class="text-gray-500 mt-1 sm:mt-2 text-xs sm:text-sm">
              Sign in to access your dashboard
            </p>
          </div>

          <!-- Error Message -->
          <Transition name="slide">
            <div v-if="form.errors.email || form.errors.password" class="mb-4 sm:mb-6 p-2 sm:p-3 bg-red-50 border-l-4 border-red-500 rounded-lg">
              <p class="text-red-700 text-xs sm:text-sm">{{ form.errors.email || form.errors.password }}</p>
            </div>
          </Transition>

          <!-- Success/Info Message -->
          <Transition name="slide">
            <div v-if="infoMessage" class="mb-4 sm:mb-6 p-2 sm:p-3 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
              <p class="text-blue-700 text-xs sm:text-sm">{{ infoMessage }}</p>
            </div>
          </Transition>

          <!-- Login Form -->
          <form @submit.prevent="submit" class="space-y-4 sm:space-y-6">
            <!-- Email Field -->
            <div>
              <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-1.5">Email Address</label>
              <div class="relative group">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                  <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                  </svg>
                </span>
                <input
                  v-model="form.email"
                  type="email"
                  autocomplete="username"
                  class="w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2 sm:py-3 text-sm sm:text-base border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-200"
                  placeholder="Enter your email"
                  required
                />
              </div>
            </div>

            <!-- Password Field -->
            <div>
              <div class="flex items-center justify-between mb-1 sm:mb-1.5">
                <label class="block text-xs sm:text-sm font-semibold text-gray-700">Password</label>
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
                  <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </span>
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  autocomplete="current-password"
                  class="w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2 sm:py-3 text-sm sm:text-base border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-200"
                  placeholder="Enter your password"
                  required
                />
              </div>
            </div>

            <!-- Login Button -->
            <button
              type="submit"
              class="w-full bg-emerald-600 text-white py-2 sm:py-3 px-4 rounded-xl hover:bg-emerald-700 hover:shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 font-semibold text-sm sm:text-base"
              :disabled="form.processing"
            >
              <svg v-if="form.processing" class="animate-spin h-4 w-4 sm:h-5 sm:w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ form.processing ? 'Signing in...' : 'Sign In' }}</span>
            </button>
          </form>

          <!-- Admin Contact Notice -->
          <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200">
            <div class="flex items-center justify-center gap-1 sm:gap-2 text-xs sm:text-sm text-gray-500">
              <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              <span class="text-center">Password can only be changed after login or by contacting the administrator</span>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- White Footer - Responsive -->
    <footer class="relative z-10 bg-white shadow-lg border-t border-gray-200 mt-auto">
      <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-3 sm:py-4">
        <div class="flex flex-col xs:flex-row items-center justify-between gap-2 sm:gap-4">
          <div class="flex items-center space-x-2">
            <div class="w-5 h-5 sm:w-6 sm:h-6 bg-emerald-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-[10px] sm:text-xs">C</span>
            </div>
            <span class="text-gray-500 text-[10px] sm:text-xs text-center xs:text-left">Caraga State University - Cabadbaran Campus</span>
          </div>
          <div class="flex items-center space-x-3 sm:space-x-4">
            <a href="#" class="text-gray-400 hover:text-gray-600 text-[10px] sm:text-xs transition-colors">Privacy</a>
            <span class="text-gray-300 text-[10px] sm:text-xs">•</span>
            <a href="#" class="text-gray-400 hover:text-gray-600 text-[10px] sm:text-xs transition-colors">Terms</a>
            <span class="text-gray-300 text-[10px] sm:text-xs">•</span>
            <span class="text-gray-400 text-[10px] sm:text-xs">© {{ new Date().getFullYear() }}</span>
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
const logoFailed = ref(false);

// Background image path
const bgImageUrl = '/images/campus-bg.png';

// Logo path
const logoPath = '/images/csu-logo.png';

const form = useForm({
  email: '',
  password: '',
});

const submit = () => {
  infoMessage.value = '';
  form.post('/login', {
    onError: (errors) => {
      if (errors.password === 'The password is incorrect.' || errors.email === 'These credentials do not match our records.') {
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

.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
}
.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

input, button {
  transition: all 0.2s ease;
}

.bg-white {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.bg-white:hover {
  transform: scale(1.02);
  box-shadow: 0 25px 40px -12px rgba(0, 0, 0, 0.25);
}

/* Custom breakpoint for extra small devices */
@media (min-width: 480px) {
  .xs\:inline {
    display: inline;
  }
  .xs\:flex-row {
    flex-direction: row;
  }
  .xs\:max-w-sm {
    max-width: 24rem;
  }
  .xs\:text-left {
    text-align: left;
  }
}

/* Default hidden for xs */
.xs\:inline {
  display: none;
}
</style>