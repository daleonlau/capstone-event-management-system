<template>
  <AdminLayout>
    <div class="max-w-5xl mx-auto py-8">
      <!-- Header Section -->
      <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center p-2 bg-emerald-100 rounded-2xl mb-4">
          <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
        <h1 class="text-4xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
          Register New Organization
        </h1>
        <p class="text-gray-500 mt-2 max-w-2xl mx-auto">
          Create a new organization with president, adviser, and treasurer accounts. All members will receive their login credentials via email.
        </p>
      </div>

      <!-- Progress Steps -->
      <div class="mb-8">
        <div class="flex items-center justify-between max-w-2xl mx-auto">
          <div v-for="(step, index) in steps" :key="index" class="flex flex-col items-center flex-1">
            <div class="relative flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300"
                 :class="{
                   'bg-emerald-600 text-white shadow-lg': currentStep >= index,
                   'bg-gray-200 text-gray-500': currentStep < index
                 }">
              <span class="text-sm font-bold">{{ index + 1 }}</span>
              <div v-if="index < steps.length - 1" 
                   class="absolute left-full w-full h-0.5 -ml-2"
                   :class="currentStep > index ? 'bg-emerald-600' : 'bg-gray-200'"
                   style="top: 50%; transform: translateY(-50%);"></div>
            </div>
            <span class="text-xs mt-2 font-medium" :class="currentStep >= index ? 'text-emerald-600' : 'text-gray-400'">
              {{ step }}
            </span>
          </div>
        </div>
      </div>

      <!-- Error Display -->
      <Transition name="slide-down">
        <div v-if="Object.keys(form.errors).length > 0" class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1">
              <p class="text-sm font-medium text-red-800">Please fix the following errors:</p>
              <ul class="mt-1 list-disc list-inside text-sm text-red-700">
                <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
              </ul>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Main Form -->
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Step 1: Organization Information -->
        <div v-show="currentStep === 0" class="space-y-6">
          <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
                <div>
                  <h2 class="text-xl font-bold text-white">Organization Information</h2>
                  <p class="text-emerald-100 text-sm">Basic details about the organization</p>
                </div>
              </div>
            </div>
            <div class="p-6 space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Organization Name <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                      </svg>
                    </div>
                    <input 
                      v-model="form.organization_name" 
                      type="text" 
                      class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                      :class="{ 'border-red-500': form.errors.organization_name }"
                      placeholder="e.g., College of Engineering Student Council"
                      required 
                    />
                  </div>
                  <p v-if="form.errors.organization_name" class="mt-1 text-sm text-red-600">{{ form.errors.organization_name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Organization Email <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                    </div>
                    <input 
                      v-model="form.organization_email" 
                      type="email" 
                      class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                      :class="{ 'border-red-500': form.errors.organization_email }"
                      placeholder="org@csucc.edu.ph"
                      required 
                    />
                  </div>
                  <p v-if="form.errors.organization_email" class="mt-1 text-sm text-red-600">{{ form.errors.organization_email }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Password <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6-4h12a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6a2 2 0 012-2zm10-4V6a4 4 0 00-8 0v4h8z" />
                      </svg>
                    </div>
                    <input 
                      v-model="form.password" 
                      :type="showPassword ? 'text' : 'password'" 
                      class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                      :class="{ 'border-red-500': form.errors.password }"
                      placeholder="Minimum 6 characters"
                      required 
                    />
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                      <svg v-if="showPassword" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <svg v-else class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                      </svg>
                    </button>
                  </div>
                  <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Confirm Password <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                      </svg>
                    </div>
                    <input 
                      v-model="form.password_confirmation" 
                      :type="showConfirmPassword ? 'text' : 'password'" 
                      class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                      required 
                    />
                    <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                      <svg v-if="showConfirmPassword" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <svg v-else class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Departments & Courses Selection -->
          <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                </div>
                <div>
                  <h2 class="text-xl font-bold text-white">College & Programs</h2>
                  <p class="text-blue-100 text-sm">Select which college and programs this organization can serve</p>
                </div>
              </div>
            </div>
            <div class="p-6">
              <div v-if="departments && departments.length > 0" class="space-y-4 max-h-96 overflow-y-auto">
                <div v-for="dept in departments" :key="dept.id" 
                     class="border rounded-xl p-4 hover:shadow-md transition-all duration-300"
                     :class="{ 'border-emerald-300 bg-emerald-50/30': form.assigned_departments.includes(dept.id) }">
                  <label class="flex items-center gap-3 cursor-pointer">
                    <input 
                      type="checkbox" 
                      :value="dept.id" 
                      v-model="form.assigned_departments"
                      @change="toggleDepartment(dept.id)"
                      class="w-5 h-5 text-emerald-600 rounded focus:ring-emerald-500"
                    />
                    <div class="flex-1">
                      <span class="font-semibold text-gray-800">{{ dept.name }}</span>
                      <span class="ml-2 text-xs text-gray-500">({{ dept.code }})</span>
                    </div>
                    <span class="text-xs text-gray-400">{{ dept.courses?.length || 0 }} courses</span>
                  </label>
                  
                  <Transition name="slide-down">
                    <div v-if="form.assigned_departments.includes(dept.id)" class="ml-8 mt-3">
                      <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <label v-for="course in dept.courses" :key="course.id" 
                               class="flex items-center gap-2 text-sm cursor-pointer p-2 rounded-lg hover:bg-gray-50 transition">
                          <input 
                            type="checkbox" 
                            :value="course.id" 
                            v-model="form.assigned_courses"
                            class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500"
                          />
                          <span class="text-gray-700">{{ course.name }}</span>
                          <span class="text-xs text-gray-400">({{ course.code }})</span>
                        </label>
                      </div>
                    </div>
                  </Transition>
                </div>
              </div>
              <div v-else class="text-center py-8">
                <div class="text-yellow-600 bg-yellow-50 rounded-xl p-4">
                  <svg class="w-8 h-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <p class="font-medium">No College loaded</p>
                  <p class="text-sm mt-1">Please run: php artisan db:seed --class=DepartmentCourseSeeder</p>
                </div>
              </div>
              
              <div v-if="form.errors.assigned_departments" class="mt-3 text-sm text-red-600">{{ form.errors.assigned_departments }}</div>
              <div v-if="form.errors.assigned_courses" class="mt-1 text-sm text-red-600">{{ form.errors.assigned_courses }}</div>
            </div>
          </div>
        </div>

        <!-- Step 2: Member Accounts -->
        <div v-show="currentStep === 1" class="space-y-6">
          <!-- President Account -->
          <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <div>
                  <h2 class="text-xl font-bold text-white">President Account</h2>
                  <p class="text-purple-100 text-sm">The organization's leader who manages events and students</p>
                </div>
              </div>
            </div>
            <div class="p-6 space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                  <input v-model="form.president_name" type="text" 
                         class="w-full px-4 py-3 border rounded-xl focus:ring-purple-500 focus:border-purple-500"
                         :class="{ 'border-red-500': form.errors.president_name }"
                         placeholder="Juan Dela Cruz" required />
                  <p v-if="form.errors.president_name" class="mt-1 text-sm text-red-600">{{ form.errors.president_name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                  <input v-model="form.president_email" type="email" 
                         class="w-full px-4 py-3 border rounded-xl focus:ring-purple-500 focus:border-purple-500"
                         :class="{ 'border-red-500': form.errors.president_email }"
                         placeholder="president@csucc.edu.ph" required />
                  <p v-if="form.errors.president_email" class="mt-1 text-sm text-red-600">{{ form.errors.president_email }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                  <input v-model="form.president_password" type="password" 
                         class="w-full px-4 py-3 border rounded-xl focus:ring-purple-500 focus:border-purple-500"
                         :class="{ 'border-red-500': form.errors.president_password }"
                         placeholder="Minimum 6 characters" required />
                  <p v-if="form.errors.president_password" class="mt-1 text-sm text-red-600">{{ form.errors.president_password }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                  <input v-model="form.president_password_confirmation" type="password" 
                         class="w-full px-4 py-3 border rounded-xl focus:ring-purple-500 focus:border-purple-500" required />
                </div>
              </div>
            </div>
          </div>

          <!-- Adviser Account -->
          <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <h2 class="text-xl font-bold text-white">Adviser Account</h2>
                  <p class="text-yellow-100 text-sm">The faculty adviser who approves events and oversees activities</p>
                </div>
              </div>
            </div>
            <div class="p-6 space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                  <input v-model="form.adviser_name" type="text" 
                         class="w-full px-4 py-3 border rounded-xl focus:ring-yellow-500 focus:border-yellow-500"
                         :class="{ 'border-red-500': form.errors.adviser_name }"
                         placeholder="Prof. Maria Santos" required />
                  <p v-if="form.errors.adviser_name" class="mt-1 text-sm text-red-600">{{ form.errors.adviser_name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                  <input v-model="form.adviser_email" type="email" 
                         class="w-full px-4 py-3 border rounded-xl focus:ring-yellow-500 focus:border-yellow-500"
                         :class="{ 'border-red-500': form.errors.adviser_email }"
                         placeholder="adviser@csucc.edu.ph" required />
                  <p v-if="form.errors.adviser_email" class="mt-1 text-sm text-red-600">{{ form.errors.adviser_email }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                  <input v-model="form.adviser_password" type="password" 
                         class="w-full px-4 py-3 border rounded-xl focus:ring-yellow-500 focus:border-yellow-500"
                         :class="{ 'border-red-500': form.errors.adviser_password }"
                         placeholder="Minimum 6 characters" required />
                  <p v-if="form.errors.adviser_password" class="mt-1 text-sm text-red-600">{{ form.errors.adviser_password }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                  <input v-model="form.adviser_password_confirmation" type="password" 
                         class="w-full px-4 py-3 border rounded-xl focus:ring-yellow-500 focus:border-yellow-500" required />
                </div>
              </div>
            </div>
          </div>

          <!-- Treasurer Account -->
          <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zM12 2v2m0 16v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
                  </svg>
                </div>
                <div>
                  <h2 class="text-xl font-bold text-white">Treasurer Account</h2>
                  <p class="text-green-100 text-sm">Manages financial transactions and collections</p>
                </div>
              </div>
            </div>
            <div class="p-6 space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                  <input v-model="form.treasurer_name" type="text" 
                         class="w-full px-4 py-3 border rounded-xl focus:ring-green-500 focus:border-green-500"
                         :class="{ 'border-red-500': form.errors.treasurer_name }"
                         placeholder="Ana Reyes" required />
                  <p v-if="form.errors.treasurer_name" class="mt-1 text-sm text-red-600">{{ form.errors.treasurer_name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                  <input v-model="form.treasurer_email" type="email" 
                         class="w-full px-4 py-3 border rounded-xl focus:ring-green-500 focus:border-green-500"
                         :class="{ 'border-red-500': form.errors.treasurer_email }"
                         placeholder="treasurer@csucc.edu.ph" required />
                  <p v-if="form.errors.treasurer_email" class="mt-1 text-sm text-red-600">{{ form.errors.treasurer_email }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                  <input v-model="form.treasurer_password" type="password" 
                         class="w-full px-4 py-3 border rounded-xl focus:ring-green-500 focus:border-green-500"
                         :class="{ 'border-red-500': form.errors.treasurer_password }"
                         placeholder="Minimum 6 characters" required />
                  <p v-if="form.errors.treasurer_password" class="mt-1 text-sm text-red-600">{{ form.errors.treasurer_password }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                  <input v-model="form.treasurer_password_confirmation" type="password" 
                         class="w-full px-4 py-3 border rounded-xl focus:ring-green-500 focus:border-green-500" required />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between gap-4 pt-6">
          <button 
            v-if="currentStep > 0"
            type="button"
            @click="currentStep--"
            class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all duration-300 flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Previous
          </button>
          <div class="flex-1"></div>
          <button 
            v-if="currentStep < steps.length - 1"
            type="button"
            @click="currentStep++"
            class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all duration-300 flex items-center gap-2"
            :disabled="!canProceedToNextStep"
          >
            Next
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </button>
          <button 
            v-else
            type="submit" 
            class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            :disabled="form.processing"
          >
            <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ form.processing ? 'Registering...' : 'Register Organization' }}</span>
            <svg v-if="!form.processing" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </button>
        </div>
      </form>

      <!-- Success Modal -->
      <Transition name="modal">
        <div v-if="showSuccessModal" class="fixed inset-0 z-50 overflow-y-auto">
          <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showSuccessModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                  <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Success!</h3>
                    <div class="mt-2">
                      <p class="text-sm text-gray-500">
                        Organization has been registered successfully. The president, adviser, and treasurer accounts have been created.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <Link 
                  href="/admin/organizations" 
                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm"
                >
                  View Organizations
                </Link>
                <button 
                  @click="showSuccessModal = false" 
                  class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                >
                  Close
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  departments: {
    type: Array,
    default: () => []
  }
});

const steps = ['Organization Info', 'Member Accounts'];
const currentStep = ref(0);
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const showSuccessModal = ref(false);

const form = useForm({
  organization_name: '',
  organization_email: '',
  password: '',
  password_confirmation: '',
  assigned_departments: [],
  assigned_courses: [],
  president_name: '',
  president_email: '',
  president_password: '',
  president_password_confirmation: '',
  adviser_name: '',
  adviser_email: '',
  adviser_password: '',
  adviser_password_confirmation: '',
  treasurer_name: '',
  treasurer_email: '',
  treasurer_password: '',
  treasurer_password_confirmation: '',
});

const canProceedToNextStep = computed(() => {
  if (currentStep.value === 0) {
    return form.organization_name && 
           form.organization_email && 
           form.password && 
           form.password_confirmation &&
           form.assigned_departments.length > 0 &&
           form.assigned_courses.length > 0;
  }
  return form.president_name && 
         form.president_email && 
         form.president_password && 
         form.president_password_confirmation &&
         form.adviser_name && 
         form.adviser_email && 
         form.adviser_password && 
         form.adviser_password_confirmation &&
         form.treasurer_name && 
         form.treasurer_email && 
         form.treasurer_password && 
         form.treasurer_password_confirmation;
});

function toggleDepartment(deptId) {
  if (!form.assigned_departments.includes(deptId)) {
    const dept = props.departments.find(d => d.id === deptId);
    if (dept && dept.courses) {
      const courseIds = dept.courses.map(c => c.id);
      form.assigned_courses = form.assigned_courses.filter(id => !courseIds.includes(id));
    }
  }
}

const submit = () => {
  if (form.assigned_departments.length === 0) {
    form.errors.assigned_departments = 'Please select at least one college.';
    return;
  }
  
  if (form.assigned_courses.length === 0) {
    form.errors.assigned_courses = 'Please select at least one program.';
    return;
  }
  
  form.post('/admin/organizations', {
    onSuccess: () => {
      showSuccessModal.value = true;
      form.reset();
      currentStep.value = 0;
    },
    onError: (errors) => {
      console.error('Registration failed:', errors);
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

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}
.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>