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
            <p class="text-xs text-emerald-200 whitespace-nowrap">Admin Panel</p>
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
        <Link
          href="/admin/dashboard"
          class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
          :class="{'bg-emerald-600 shadow-lg': activeLink === 'dashboard'}"
          @click="setActive('dashboard')"
          :title="isSidebarCollapsed ? 'Dashboard' : ''"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
          </svg>
          <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Dashboard</span>
        </Link>

        <Link
          href="/admin/organizations"
          class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
          :class="{'bg-emerald-600 shadow-lg': activeLink === 'organizations'}"
          @click="setActive('organizations')"
          :title="isSidebarCollapsed ? 'Organizations' : ''"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
          </svg>
          <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Organizations</span>
        </Link>

        <Link
          href="/admin/evaluations"
          class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
          :class="{'bg-emerald-600 shadow-lg': activeLink === 'evaluations'}"
          @click="setActive('evaluations')"
          :title="isSidebarCollapsed ? 'Evaluations' : ''"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
          </svg>
          <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Evaluations</span>
        </Link>

        <Link
          href="/admin/reports"
          class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
          :class="{'bg-emerald-600 shadow-lg': activeLink === 'reports'}"
          @click="setActive('reports')"
          :title="isSidebarCollapsed ? 'Reports' : ''"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
          </svg>
          <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Reports</span>
        </Link>

        <!-- Logs Link - NEW -->
        <Link
          href="/admin/logs"
          class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
          :class="{'bg-emerald-600 shadow-lg': activeLink === 'logs'}"
          @click="setActive('logs')"
          :title="isSidebarCollapsed ? 'System Logs' : ''"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
          <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">System Logs</span>
        </Link>

        <Link
          href="/admin/profile"
          class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-emerald-600 transition-all duration-300 group"
          :class="{'bg-emerald-600 shadow-lg': activeLink === 'profile'}"
          @click="setActive('profile')"
          :title="isSidebarCollapsed ? 'Profile' : ''"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
          </svg>
          <span :class="['transition-opacity duration-300', isSidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100']">Profile</span>
        </Link>
      </nav>

      <!-- User Info & Logout (Fixed at bottom) -->
      <div class="p-3 border-t border-emerald-800">
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
                      <span class="text-lg font-bold text-emerald-700">{{ user?.name?.charAt(0) || 'A' }}</span>
                    </div>
                    <div>
                      <h4 class="font-medium text-gray-900">{{ user?.name || 'Admin' }}</h4>
                      <p class="text-sm text-gray-500">{{ user?.email || 'admin@csucc.edu.ph' }}</p>
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
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const showLogoutModal = ref(false);
const isSidebarCollapsed = ref(false);
const isFullScreen = ref(false);

const activeLink = ref(
  window.location.pathname.includes('organizations') ? 'organizations' :
  window.location.pathname.includes('profile') ? 'profile' :
  window.location.pathname.includes('evaluations') ? 'evaluations' :
  window.location.pathname.includes('reports') ? 'reports' :
  window.location.pathname.includes('logs') ? 'logs' :  // Add logs detection
  'dashboard'
);

function setActive(link) {
  activeLink.value = link;
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