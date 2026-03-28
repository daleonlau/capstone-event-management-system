<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto space-y-8">
      <!-- Header with Back Button -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
            Organization Details
          </h1>
          <p class="text-gray-500 mt-1">Manage organization information and members</p>
        </div>
        <Link 
          href="/admin/organizations" 
          class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all duration-300 flex items-center gap-2 group"
        >
          <svg class="w-5 h-5 group-hover:-translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Back to List
        </Link>
      </div>

      <!-- Organization Info Card -->
      <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-emerald-500 to-teal-600"></div>
        <div class="px-8 pb-8">
          <div class="flex items-end -mt-12 mb-6">
            <div class="w-24 h-24 bg-white rounded-2xl shadow-lg p-1">
              <div class="w-full h-full bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                <span class="text-white text-3xl font-bold">{{ organization.name?.charAt(0) || 'O' }}</span>
              </div>
            </div>
            <div class="ml-6">
              <h2 class="text-2xl font-bold text-gray-800">{{ organization.name }}</h2>
              <p class="text-gray-500">{{ organization.email }}</p>
            </div>
            <div class="ml-auto">
              <span class="px-4 py-2 bg-emerald-100 text-emerald-700 rounded-xl font-medium text-sm">
                Registered {{ formatDate(organization.created_at) }}
              </span>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">
              <p class="text-sm text-gray-500">Total Members</p>
              <p class="text-2xl font-bold text-gray-800">{{ organization.organization_users?.length || 0 }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">
              <p class="text-sm text-gray-500">Assigned Departments</p>
              <p class="text-2xl font-bold text-gray-800">{{ organization.organization_settings?.assigned_departments?.length || 0 }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">
              <p class="text-sm text-gray-500">Assigned Courses</p>
              <p class="text-2xl font-bold text-gray-800">{{ organization.organization_settings?.assigned_courses?.length || 0 }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">
              <p class="text-sm text-gray-500">Total Events</p>
              <p class="text-2xl font-bold text-gray-800">{{ activityStats?.total_events || 0 }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="border-b border-gray-200">
        <nav class="flex gap-4">
          <button 
            @click="activeTab = 'members'" 
            :class="activeTab === 'members' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'"
            class="px-4 py-2 border-b-2 font-medium transition flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Members
          </button>
          <button 
            @click="activeTab = 'departments'" 
            :class="activeTab === 'departments' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'"
            class="px-4 py-2 border-b-2 font-medium transition flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            Departments & Courses
          </button>
          <button 
            @click="activeTab = 'activity'" 
            :class="activeTab === 'activity' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'"
            class="px-4 py-2 border-b-2 font-medium transition flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Activity
          </button>
        </nav>
      </div>

      <!-- Members Tab -->
      <div v-if="activeTab === 'members'" class="space-y-6">
        <!-- Add Member Button -->
        <div class="flex justify-end">
          <button 
            @click="showAddMemberModal = true"
            class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition flex items-center gap-2 shadow-lg hover:shadow-xl"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add New Member
          </button>
        </div>

        <!-- Members Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div v-for="member in organization.organization_users" :key="member.id" 
               class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group">
            <div class="p-6">
              <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                  <div class="w-16 h-16 rounded-2xl bg-gradient-to-br" 
                       :class="{
                         'from-purple-500 to-purple-700': member.role === 'president',
                         'from-yellow-500 to-yellow-700': member.role === 'adviser',
                         'from-orange-500 to-orange-700': member.role === 'treasurer'
                       }">
                    <div class="w-full h-full flex items-center justify-center">
                      <span class="text-white text-2xl font-bold">{{ member.name.charAt(0) }}</span>
                    </div>
                  </div>
                  <div>
                    <p class="font-bold text-gray-800 text-lg">{{ member.name }}</p>
                    <p class="text-sm text-gray-500">{{ member.email }}</p>
                    <div class="flex items-center gap-2 mt-1">
                      <span class="text-xs px-2 py-1 rounded-full" 
                        :class="{
                          'bg-purple-100 text-purple-700': member.role === 'president',
                          'bg-yellow-100 text-yellow-700': member.role === 'adviser',
                          'bg-orange-100 text-orange-700': member.role === 'treasurer'
                        }">
                        {{ member.role.charAt(0).toUpperCase() + member.role.slice(1) }}
                      </span>
                      <span v-if="member.is_blocked" class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded-full">
                        Blocked
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex gap-2">
                  <button 
                    @click="editMember(member)"
                    class="p-2 text-gray-400 hover:text-blue-600 transition rounded-lg hover:bg-blue-50"
                    title="Edit Member"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button 
                    v-if="!member.is_blocked"
                    @click="blockMember(member)"
                    class="p-2 text-gray-400 hover:text-red-600 transition rounded-lg hover:bg-red-50"
                    title="Block Member"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                  </button>
                  <button 
                    v-else
                    @click="unblockMember(member)"
                    class="p-2 text-gray-400 hover:text-green-600 transition rounded-lg hover:bg-green-50"
                    title="Unblock Member"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </button>
                  <button 
                    @click="resetPassword(member)"
                    class="p-2 text-gray-400 hover:text-yellow-600 transition rounded-lg hover:bg-yellow-50"
                    title="Reset Password"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                  </button>
                  <button 
                    @click="deleteMember(member)"
                    class="p-2 text-gray-400 hover:text-red-600 transition rounded-lg hover:bg-red-50"
                    title="Remove Member"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </div>
              <div v-if="member.is_blocked && member.blocked_at" class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-red-600 flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Blocked on {{ formatDate(member.blocked_at) }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Departments & Courses Tab -->
      <div v-if="activeTab === 'departments'" class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </span>
          Assigned Departments & Courses
        </h2>
        
        <div v-if="organization.organization_settings" class="space-y-4">
          <div v-for="deptId in organization.organization_settings.assigned_departments" :key="deptId" class="border rounded-xl p-4">
            <h3 class="font-semibold text-emerald-600 mb-2">{{ getDepartmentName(deptId) }}</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
              <span v-for="courseId in getCoursesForDepartment(deptId)" :key="courseId" class="text-sm text-gray-600 flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span>
                {{ getCourseName(courseId) }}
              </span>
            </div>
          </div>
          <div v-if="!organization.organization_settings.assigned_departments?.length" class="text-gray-500 text-center py-4">
            No departments assigned to this organization.
          </div>
        </div>
      </div>

      <!-- Activity Tab -->
      <div v-if="activeTab === 'activity'" class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </span>
          Activity Summary
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 text-center">
            <p class="text-sm text-gray-600">Total Events Created</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ activityStats?.total_events || 0 }}</p>
          </div>
          <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 text-center">
            <p class="text-sm text-gray-600">Total Students</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ activityStats?.total_students || 0 }}</p>
          </div>
          <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 text-center">
            <p class="text-sm text-gray-600">Total Evaluations</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">{{ activityStats?.total_evaluations || 0 }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Member Modal -->
    <Teleport to="body">
      <div v-if="showAddMemberModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showAddMemberModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
              <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-white">Add New Member</h3>
                <button @click="showAddMemberModal = false" class="text-white/80 hover:text-white">
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
            <form @submit.prevent="addMember" class="p-6 space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                <input v-model="newMember.name" type="text" required
                       class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input v-model="newMember.email" type="email" required
                       class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                <input v-model="newMember.password" type="password" required minlength="6"
                       class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                <select v-model="newMember.role" required
                        class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                  <option value="president">President</option>
                  <option value="adviser">Adviser</option>
                  <option value="treasurer">Treasurer</option>
                </select>
              </div>
              <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="showAddMemberModal = false"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" :disabled="addingMember"
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50">
                  {{ addingMember ? 'Adding...' : 'Add Member' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Edit Member Modal -->
    <Teleport to="body">
      <div v-if="showEditMemberModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showEditMemberModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
              <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-white">Edit Member</h3>
                <button @click="showEditMemberModal = false" class="text-white/80 hover:text-white">
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
            <form @submit.prevent="updateMember" class="p-6 space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input v-model="editMemberData.name" type="text"
                       class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input v-model="editMemberData.email" type="email"
                       class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select v-model="editMemberData.role"
                        class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                  <option value="president">President</option>
                  <option value="adviser">Adviser</option>
                  <option value="treasurer">Treasurer</option>
                </select>
              </div>
              <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="showEditMemberModal = false"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" :disabled="editingMember"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                  {{ editingMember ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Reset Password Modal -->
    <Teleport to="body">
      <div v-if="showResetPasswordModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showResetPasswordModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 px-6 py-4">
              <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-white">Reset Password</h3>
                <button @click="showResetPasswordModal = false" class="text-white/80 hover:text-white">
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
            <form @submit.prevent="submitResetPassword" class="p-6 space-y-4">
              <p class="text-sm text-gray-600">Reset password for <strong>{{ resetPasswordMember?.name }}</strong></p>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                <input v-model="resetPasswordData.password" type="password" required minlength="6
                       class="w-full px-4>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                <input v-model="resetPasswordData.password_confirmation" type="password" required
                       class="w-full px-4 py-2 border rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
              </div>
              <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="showResetPasswordModal = false"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" :disabled="resettingPassword"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 disabled:opacity-50">
                  {{ resettingPassword ? 'Resetting...' : 'Reset Password' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Toast Notification -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="translate-x-full opacity-0"
      enter-to-class="translate-x-0 opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="translate-x-0 opacity-100"
      leave-to-class="translate-x-full opacity-0"
    >
      <div v-if="toast.show" 
           class="fixed bottom-4 right-4 z-50 flex min-w-[320px] items-center gap-3 rounded-xl border-l-4 p-4 shadow-2xl backdrop-blur-sm"
           :class="toast.bgClass">
        <span class="flex-1">{{ toast.message }}</span>
        <button @click="toast.show = false" class="text-gray-400 hover:text-gray-600">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </Transition>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import axios from 'axios';

const props = defineProps({
  organization: {
    type: Object,
    default: () => ({})
  },
  departments: {
    type: Array,
    default: () => []
  },
  courses: {
    type: Array,
    default: () => []
  },
  activityStats: {
    type: Object,
    default: () => ({})
  }
});

const activeTab = ref('members');

// Modals
const showAddMemberModal = ref(false);
const showEditMemberModal = ref(false);
const showResetPasswordModal = ref(false);

// Form Data
const newMember = ref({
  name: '',
  email: '',
  password: '',
  role: 'adviser'
});

const editMemberData = ref({
  id: null,
  name: '',
  email: '',
  role: ''
});

const resetPasswordMember = ref(null);
const resetPasswordData = ref({
  password: '',
  password_confirmation: ''
});

// Loading states
const addingMember = ref(false);
const editingMember = ref(false);
const resettingPassword = ref(false);

// Toast
const toast = ref({ show: false, message: '', type: 'success', bgClass: '' });

function showToast(message, type = 'success') {
  const colors = {
    success: 'border-green-500 bg-green-50 text-green-800',
    error: 'border-red-500 bg-red-50 text-red-800',
    info: 'border-blue-500 bg-blue-50 text-blue-800'
  };
  
  toast.value = { 
    show: true, 
    message, 
    type,
    bgClass: colors[type] || colors.success
  };
  
  setTimeout(() => toast.value.show = false, 3000);
}

function formatDate(date) {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

function getDepartmentName(deptId) {
  const dept = props.departments.find(d => d.id === deptId);
  return dept ? dept.name : 'Unknown Department';
}

function getCourseName(courseId) {
  const course = props.courses.find(c => c.id === courseId);
  return course ? course.name : 'Unknown Course';
}

function getCoursesForDepartment(deptId) {
  const dept = props.departments.find(d => d.id === deptId);
  if (!dept) return [];
  
  const assignedCourseIds = props.organization?.organization_settings?.assigned_courses || [];
  return dept.courses
    .filter(course => assignedCourseIds.includes(course.id))
    .map(course => course.id);
}

async function addMember() {
  addingMember.value = true;
  try {
    const response = await axios.post(`/admin/organizations/${props.organization.id}/members`, newMember.value);
    if (response.data.success) {
      showToast('Member added successfully!', 'success');
      showAddMemberModal.value = false;
      router.reload();
    }
  } catch (error) {
    showToast(error.response?.data?.error || 'Failed to add member', 'error');
  } finally {
    addingMember.value = false;
  }
}

async function blockMember(member) {
  if (!confirm(`Are you sure you want to block ${member.name}? They will not be able to access their account.`)) return;
  
  try {
    const response = await axios.post(`/admin/users/${member.id}/block`);
    if (response.data.success) {
      showToast(`${member.name} has been blocked`, 'success');
      router.reload();
    }
  } catch (error) {
    showToast('Failed to block user', 'error');
  }
}

async function unblockMember(member) {
  if (!confirm(`Are you sure you want to unblock ${member.name}? They will be able to access their account again.`)) return;
  
  try {
    const response = await axios.post(`/admin/users/${member.id}/unblock`);
    if (response.data.success) {
      showToast(`${member.name} has been unblocked`, 'success');
      router.reload();
    }
  } catch (error) {
    showToast('Failed to unblock user', 'error');
  }
}

function editMember(member) {
  editMemberData.value = {
    id: member.id,
    name: member.name,
    email: member.email,
    role: member.role
  };
  showEditMemberModal.value = true;
}

async function updateMember() {
  editingMember.value = true;
  try {
    const response = await axios.put(`/admin/users/${editMemberData.value.id}`, {
      name: editMemberData.value.name,
      email: editMemberData.value.email,
      role: editMemberData.value.role
    });
    if (response.data.success) {
      showToast('Member updated successfully!', 'success');
      showEditMemberModal.value = false;
      router.reload();
    }
  } catch (error) {
    showToast(error.response?.data?.error || 'Failed to update member', 'error');
  } finally {
    editingMember.value = false;
  }
}

function resetPassword(member) {
  resetPasswordMember.value = member;
  resetPasswordData.value = { password: '', password_confirmation: '' };
  showResetPasswordModal.value = true;
}

async function submitResetPassword() {
  if (resetPasswordData.value.password !== resetPasswordData.value.password_confirmation) {
    showToast('Passwords do not match', 'error');
    return;
  }
  
  resettingPassword.value = true;
  try {
    const response = await axios.post(`/admin/users/${resetPasswordMember.value.id}/reset-password`, {
      password: resetPasswordData.value.password
    });
    if (response.data.success) {
      showToast('Password reset successfully!', 'success');
      showResetPasswordModal.value = false;
    }
  } catch (error) {
    showToast(error.response?.data?.error || 'Failed to reset password', 'error');
  } finally {
    resettingPassword.value = false;
  }
}

async function deleteMember(member) {
  if (!confirm(`Are you sure you want to remove ${member.name} from this organization? This action cannot be undone.`)) return;
  
  try {
    const response = await axios.delete(`/admin/users/${member.id}`);
    if (response.data.success) {
      showToast(`${member.name} has been removed`, 'success');
      router.reload();
    }
  } catch (error) {
    showToast('Failed to remove member', 'error');
  }
}
</script>