<template>
  <div
    class="min-h-screen flex"
    :style="{
      backgroundImage: 'url(/images/campus-bg.png)',
      backgroundSize: 'cover',
      backgroundPosition: 'center',
      backgroundRepeat: 'no-repeat',
      backgroundAttachment: 'scroll',
    }"
  >
    <!-- Sidebar -->
    <aside 
      :class="[
        'bg-gradient-to-b from-emerald-700 to-emerald-900 text-white flex flex-col fixed h-screen shadow-2xl z-20 transition-all duration-300',
        isSidebarCollapsed ? 'w-20' : 'w-64'
      ]"
    >
      <!-- Header with logo - Collapsible version -->
      <div class="p-4 flex items-center justify-between border-b border-emerald-600">
        <div class="flex items-center gap-3 overflow-hidden">
          <!-- CSU Logo -->
          <img 
            src="/images/csu-logo.png" 
            alt="CSU Logo" 
            class="w-10 h-10 object-contain rounded-full shadow-md flex-shrink-0 bg-white p-1"
          />
          <div :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">
            <h1 class="text-lg font-bold whitespace-nowrap">EventFlow</h1>
            <p class="text-xs text-emerald-200 whitespace-nowrap capitalize">{{ userRole }} Dashboard</p>
          </div>
        </div>
        <!-- Collapse Toggle Button -->
        <button 
          @click="toggleSidebar" 
          class="text-white/70 hover:text-white transition-colors flex-shrink-0"
          :title="isSidebarCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
        >
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-5 w-5" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              :d="isSidebarCollapsed ? 'M13 5l7 7-7 7M5 5l7 7-7 7' : 'M11 19l-7-7 7-7M13 5l7 7-7 7'" 
            />
          </svg>
        </button>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
        <!-- Dashboard - Always show -->
        <Link
          :href="`/${userRole}/dashboard`"
          class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
          :class="{'bg-emerald-600 shadow-lg': isActive(`/${userRole}/dashboard`)}"
          :title="isSidebarCollapsed ? 'Dashboard' : ''"
        >
          <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Dashboard</span>
        </Link>

        <!-- President Navigation -->
        <template v-if="userRole === 'president'">
          <!-- Events -->
          <Link
            href="/president/events"
            class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
            :class="{'bg-emerald-600 shadow-lg': isActive('/president/events')}"
            :title="isSidebarCollapsed ? 'Events' : ''"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
            </svg>
            <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Events</span>
          </Link>

          <!-- Students -->
          <Link
            href="/president/students"
            class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
            :class="{'bg-emerald-600 shadow-lg': isActive('/president/students')}"
            :title="isSidebarCollapsed ? 'Students' : ''"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Students</span>
          </Link>

          <!-- Evaluations -->
          <Link
            href="/president/evaluations"
            class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
            :class="{'bg-emerald-600 shadow-lg': isActive('/president/evaluations')}"
            :title="isSidebarCollapsed ? 'Evaluations' : ''"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Evaluations</span>
          </Link>
        </template>

        <!-- Adviser Navigation -->
        <template v-else-if="userRole === 'adviser'">
          <!-- Approvals -->
          <Link
            href="/adviser/approvals"
            class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
            :class="{'bg-emerald-600 shadow-lg': isActive('/adviser/approvals')}"
            :title="isSidebarCollapsed ? 'Approvals' : ''"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Approvals</span>
          </Link>

          <!-- Events (Read-only) -->
          <Link
            href="/adviser/events"
            class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
            :class="{'bg-emerald-600 shadow-lg': isActive('/adviser/events')}"
            :title="isSidebarCollapsed ? 'Events' : ''"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
            </svg>
            <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Events</span>
          </Link>

          <!-- Students (Read-only) -->
          <Link
            href="/adviser/students"
            class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
            :class="{'bg-emerald-600 shadow-lg': isActive('/adviser/students')}"
            :title="isSidebarCollapsed ? 'Students' : ''"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Students</span>
          </Link>

          <!-- Evaluations (Read-only) -->
          <Link
            href="/adviser/evaluations"
            class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
            :class="{'bg-emerald-600 shadow-lg': isActive('/adviser/evaluations')}"
            :title="isSidebarCollapsed ? 'Evaluations' : ''"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Evaluations</span>
          </Link>
        </template>

        <!-- Treasurer Navigation -->
        <template v-else-if="userRole === 'treasurer'">
          <!-- Collections -->
          <Link
            href="/treasurer/collections"
            class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
            :class="{'bg-emerald-600 shadow-lg': isActive('/treasurer/collections')}"
            :title="isSidebarCollapsed ? 'Collections' : ''"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Collections</span>
          </Link>

          <!-- Reports -->
          <Link
            href="/treasurer/reports"
            class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
            :class="{'bg-emerald-600 shadow-lg': isActive('/treasurer/reports')}"
            :title="isSidebarCollapsed ? 'Reports' : ''"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Reports</span>
          </Link>
        </template>

        <!-- Profile - Always show at bottom -->
        <div class="pt-4 mt-4 border-t border-emerald-800">
          <Link
            :href="`/${userRole}/profile`"
            class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
            :class="{'bg-emerald-600 shadow-lg': isActive(`/${userRole}/profile`)}"
            :title="isSidebarCollapsed ? 'Profile' : ''"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Profile</span>
          </Link>
        </div>
      </nav>

      <!-- User Info & Logout (Fixed at bottom) -->
      <div v-if="user" class="p-3 border-t border-emerald-800">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
            <span class="text-white font-bold">{{ user.name?.charAt(0) || 'U' }}</span>
          </div>
          <div :class="['flex-1 min-w-0 transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0 hidden' : 'opacity-100']">
            <p class="text-sm font-medium truncate">{{ user.name || 'User' }}</p>
            <p class="text-xs text-emerald-300 truncate">{{ user.email || '' }}</p>
          </div>
        </div>
        <button
          @click="showLogoutModal = true"
          class="w-full bg-red-600/20 hover:bg-red-600 text-white px-3 py-2 rounded-xl flex items-center justify-center gap-2 transition-all duration-300 group"
          :title="isSidebarCollapsed ? 'Logout' : ''"
        >
          <svg class="w-5 h-5 flex-shrink-0 group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-6 0v-1m6-10V3a3 3 0 00-6 0v1" />
          </svg>
          <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Content - Adjusts based on sidebar state -->
    <main 
      :class="[
        'flex-1 transition-all duration-300 z-10',
        isSidebarCollapsed ? 'ml-20' : 'ml-64'
      ]"
    >
      <!-- Full Screen Toggle Button -->
      <div class="fixed top-4 right-4 z-30">
        <button
          @click="toggleFullScreen"
          class="bg-white/90 backdrop-blur-sm hover:bg-white p-2 rounded-full shadow-lg transition-all duration-300 hover:scale-110"
          :title="isFullScreen ? 'Exit Full Screen' : 'Full Screen'"
        >
          <svg 
            v-if="!isFullScreen"
            class="w-5 h-5 text-gray-700" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
          </svg>
          <svg 
            v-else
            class="w-5 h-5 text-gray-700" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H4v4M16 4h4v4M16 20h4v-4M8 20H4v-4" />
          </svg>
        </button>
      </div>

      <!-- Content Container with full width support -->
      <div 
        :class="[
          'transition-all duration-300 relative',
          isFullScreen ? 'p-4' : 'p-8'
        ]"
      >
        <slot />
      </div>
    </main>

    <!-- Logout Confirmation Modal -->
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
          <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="showLogoutModal = false"></div>
          
          <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
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

              <div class="p-6">
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

                <div class="rounded-lg bg-gray-50 p-4">
                  <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100">
                      <span class="text-lg font-bold text-emerald-700">{{ user?.name?.charAt(0) || 'U' }}</span>
                    </div>
                    <div>
                      <h4 class="font-medium text-gray-900">{{ user?.name || 'User' }}</h4>
                      <p class="text-sm text-gray-500">{{ user?.email || '' }}</p>
                    </div>
                  </div>
                </div>
              </div>

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
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const showLogoutModal = ref(false);
const isSidebarCollapsed = ref(false);
const isFullScreen = ref(false);

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

function toggleSidebar() {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
}

function toggleFullScreen() {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen();
    isFullScreen.value = true;
  } else {
    document.exitFullscreen();
    isFullScreen.value = false;
  }
}

// Listen for fullscreen change events
document.addEventListener('fullscreenchange', () => {
  isFullScreen.value = !!document.fullscreenElement;
});
</script>

<style scoped>
/* Smooth transitions for sidebar collapse */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

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