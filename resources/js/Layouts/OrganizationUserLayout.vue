<template>
  <div class="min-h-screen flex bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-gradient-to-b from-emerald-700 to-emerald-900 text-white flex flex-col fixed h-screen shadow-2xl z-50">
      <!-- Header with logo -->
      <div class="p-6 flex items-center gap-3 border-b border-emerald-600">
        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
          <span class="text-emerald-700 font-bold text-xl">C</span>
        </div>
        <div>
          <h1 class="text-lg font-bold">CSUCC EMS</h1>
          <p class="text-xs text-emerald-200 capitalize">{{ userRole }} Dashboard</p>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <!-- Dashboard - Always show -->
        <Link
          :href="`/${userRole}/dashboard`"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300"
          :class="{'bg-emerald-600 shadow-lg': isActive(`/${userRole}/dashboard`)}"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span>Dashboard</span>
        </Link>

        <!-- President Navigation -->
        <template v-if="userRole === 'president'">
          <!-- Events -->
          <Link
            href="/president/events"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300"
            :class="{'bg-emerald-600 shadow-lg': isActive('/president/events')}"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
            </svg>
            <span>Events</span>
          </Link>

          <!-- Students -->
          <Link
            href="/president/students"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300"
            :class="{'bg-emerald-600 shadow-lg': isActive('/president/students')}"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span>Students</span>
          </Link>

          <!-- Evaluations -->
          <Link
            href="/president/evaluations"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300"
            :class="{'bg-emerald-600 shadow-lg': isActive('/president/evaluations')}"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Evaluations</span>
          </Link>
        </template>

        <!-- Adviser Navigation -->
        <template v-else-if="userRole === 'adviser'">
          <!-- Approvals -->
          <Link
            href="/adviser/approvals"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300"
            :class="{'bg-emerald-600 shadow-lg': isActive('/adviser/approvals')}"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Approvals</span>
          </Link>

          <!-- Events (Read-only) -->
          <Link
            href="/adviser/events"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300"
            :class="{'bg-emerald-600 shadow-lg': isActive('/adviser/events')}"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
            </svg>
            <span>Events</span>
          </Link>

          <!-- Students (Read-only) -->
          <Link
            href="/adviser/students"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300"
            :class="{'bg-emerald-600 shadow-lg': isActive('/adviser/students')}"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span>Students</span>
          </Link>

          <!-- Evaluations (Read-only) -->
          <Link
            href="/adviser/evaluations"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300"
            :class="{'bg-emerald-600 shadow-lg': isActive('/adviser/evaluations')}"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Evaluations</span>
          </Link>
        </template>

        <!-- Treasurer Navigation -->
        <template v-else-if="userRole === 'treasurer'">
          <!-- Collections -->
          <Link
            href="/treasurer/collections"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300"
            :class="{'bg-emerald-600 shadow-lg': isActive('/treasurer/collections')}"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>Collections</span>
          </Link>

          <!-- Reports -->
          <Link
            href="/treasurer/reports"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300"
            :class="{'bg-emerald-600 shadow-lg': isActive('/treasurer/reports')}"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span>Reports</span>
          </Link>
        </template>

        <!-- Profile - Always show at bottom -->
        <div class="pt-4 mt-4 border-t border-emerald-800">
          <Link
            :href="`/${userRole}/profile`"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300"
            :class="{'bg-emerald-600 shadow-lg': isActive(`/${userRole}/profile`)}"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>Profile</span>
          </Link>
        </div>
      </nav>

      <!-- User Info & Logout (Fixed at bottom) -->
      <div v-if="user" class="p-4 border-t border-emerald-800 mt-auto">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center">
            <span class="text-white font-bold">{{ user.name?.charAt(0) || 'U' }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium truncate">{{ user.name || 'User' }}</p>
            <p class="text-xs text-emerald-300 truncate">{{ user.email || '' }}</p>
          </div>
        </div>
        <button
          @click="showLogoutModal = true"
          class="w-full bg-red-600/20 hover:bg-red-600 text-white px-4 py-2 rounded-xl flex items-center justify-center gap-2 transition-all duration-300 group"
        >
          <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

    <!-- Logout Confirmation Modal - FIXED with transparent blurry backdrop -->
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
          <!-- TRANSPARENT BLURRY BACKDROP - NO GRAY BACKGROUND -->
          <div class="fixed inset-0 bg-transparent backdrop-blur-md" @click="showLogoutModal = false"></div>
          
          <!-- Modal container - centered -->
          <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
              <!-- Modal content -->
              <div class="p-6">
                <div class="flex items-start gap-4">
                  <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900">Confirm Logout</h3>
                    <p class="mt-2 text-sm text-gray-500">
                      Are you sure you want to logout? You will be redirected to the login page.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div class="flex justify-end gap-3 bg-gray-50 px-6 py-4">
                <button
                  @click="showLogoutModal = false"
                  class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                >
                  Cancel
                </button>
                <button
                  @click="logout"
                  class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
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
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const showLogoutModal = ref(false);

// Debug logs - check browser console
onMounted(() => {
  console.log('=== OrganizationUserLayout Debug ===');
  console.log('Page props:', page.props);
  console.log('Auth object:', page.props.auth);
  console.log('User from props:', user.value);
  console.log('User role:', user.value?.role);
  console.log('Current path:', window.location.pathname);
});

const userRole = computed(() => {
  const role = user.value?.role || 'user';
  return role;
});

// Helper function to check if a link is active
function isActive(path) {
  const currentPath = window.location.pathname;
  // Exact match for root paths, partial match for nested paths
  if (path === '/president/evaluations' && currentPath.includes('/president/evaluations/')) {
    return true;
  }
  if (path === '/adviser/evaluations' && currentPath.includes('/adviser/evaluations/')) {
    return true;
  }
  return currentPath === path || currentPath.startsWith(path + '/');
}

function logout() {
  router.post('/logout');
  showLogoutModal.value = false;
}
</script>

<style scoped>
/* Ensure sidebar has proper height */
aside {
  height: 100vh;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: rgba(255,255,255,0.3) transparent;
}

aside::-webkit-scrollbar {
  width: 4px;
}

aside::-webkit-scrollbar-track {
  background: transparent;
}

aside::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.3);
  border-radius: 20px;
}

/* Ensure main content doesn't overlap with fixed sidebar */
main {
  min-height: 100vh;
}
</style>