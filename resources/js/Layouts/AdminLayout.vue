<template>
  <div class="min-h-screen flex bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-gradient-to-b from-emerald-700 to-emerald-900 text-white flex flex-col fixed h-screen shadow-2xl">
      <!-- Header with logo -->
      <div class="p-6 flex items-center gap-3 border-b border-emerald-600">
        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
          <span class="text-emerald-700 font-bold text-xl">C</span>
        </div>
        <div>
          <h1 class="text-lg font-bold">CSUCC EMS</h1>
          <p class="text-xs text-emerald-200">Admin Panel</p>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <Link
          href="/admin/dashboard"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
          :class="{'bg-emerald-600 shadow-lg': activeLink === 'dashboard'}"
          @click="setActive('dashboard')"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
          </svg>
          <span>Dashboard</span>
        </Link>

        <Link
          href="/admin/organizations"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
          :class="{'bg-emerald-600 shadow-lg': activeLink === 'organizations'}"
          @click="setActive('organizations')"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
          </svg>
          <span>Organizations</span>
        </Link>

        <Link
          href="/admin/profile"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
          :class="{'bg-emerald-600 shadow-lg': activeLink === 'profile'}"
          @click="setActive('profile')"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
          </svg>
          <span>Profile</span>
        </Link>
      </nav>

      <!-- User Info & Logout -->
      <div class="p-4 border-t border-emerald-800">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center">
            <span class="text-white font-bold">{{ user?.name?.charAt(0) || 'A' }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium truncate">{{ user?.name || 'Admin' }}</p>
            <p class="text-xs text-emerald-300 truncate">{{ user?.email || 'admin@csucc.edu.ph' }}</p>
          </div>
        </div>
        <button
          @click="showLogoutModal = true"
          class="w-full bg-red-600/20 hover:bg-red-600 text-white px-4 py-2 rounded-xl flex items-center justify-center gap-2 transition-all duration-300 group"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-6 0v-1m6-10V3a3 3 0 00-6 0v1" />
          </svg>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64">
      <div class="p-8">
        <slot />
      </div>
    </main>

    <!-- Logout Confirmation Modal - FIXED with blurry backdrop matching Show.vue -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showLogoutModal" class="fixed inset-0 z-50 overflow-y-auto">
          <!-- Blurry backdrop - matching Show.vue -->
          <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="showLogoutModal = false"></div>
          
          <!-- Modal container - matching Show.vue style -->
          <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
              <!-- Header with gradient -->
              <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                      <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-6 0v-1m6-10V3a3 3 0 00-6 0v1" />
                      </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white">Confirm Logout</h3>
                  </div>
                  <button @click="showLogoutModal = false" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Modal Body -->
              <div class="p-6">
                <!-- Warning Alert -->
                <div class="mb-4 rounded-lg bg-red-50 p-4">
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                      </svg>
                    </div>
                    <div class="ml-3">
                      <p class="text-sm text-red-700">
                        Are you sure you want to logout? You will be redirected to the login page.
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Admin Info Card -->
                <div class="rounded-lg bg-gray-50 p-4">
                  <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100">
                      <span class="text-lg font-bold text-emerald-700">{{ user?.name?.charAt(0) || 'A' }}</span>
                    </div>
                    <div>
                      <h4 class="font-medium text-gray-900">{{ user?.name || 'Admin' }}</h4>
                      <p class="text-sm text-gray-500">{{ user?.email || 'admin@csucc.edu.ph' }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer - matching Show.vue -->
              <div class="flex justify-end gap-3 bg-gray-50 px-6 py-4">
                <button
                  @click="showLogoutModal = false"
                  class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                >
                  Cancel
                </button>
                <button
                  @click="logout"
                  class="rounded-lg bg-gradient-to-r from-red-600 to-red-700 px-4 py-2 text-sm font-medium text-white hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500"
                >
                  Logout
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const showLogoutModal = ref(false);

const activeLink = ref(
  window.location.pathname.includes('organizations') ? 'organizations' :
  window.location.pathname.includes('profile') ? 'profile' :
  'dashboard'
);

function setActive(link) {
  activeLink.value = link;
}

function logout() {
  router.post('/logout');
  showLogoutModal.value = false;
}
</script>

<style scoped>
/* Modal transitions are now handled by Tailwind classes in the template */
</style>