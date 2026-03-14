<template>
  <AdminLayout>
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Register New Organization</h1>
        <p class="text-gray-500 mt-1">Create a new organization with president, adviser, and treasurer accounts</p>
      </div>

      <!-- Error Display -->
      <Transition name="fade">
        <div v-if="form.errors.error" class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
          <p class="text-red-700">{{ form.errors.error }}</p>
        </div>
      </Transition>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Organization Account -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </span>
            Organization Account
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Organization Name <span class="text-red-500">*</span></label>
              <input 
                v-model="form.organization_name" 
                type="text" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                :class="{ 'border-red-500': form.errors.organization_name }"
                required 
              />
              <p v-if="form.errors.organization_name" class="mt-1 text-sm text-red-600">{{ form.errors.organization_name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Organization Email <span class="text-red-500">*</span></label>
              <input 
                v-model="form.organization_email" 
                type="email" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                :class="{ 'border-red-500': form.errors.organization_email }"
                required 
              />
              <p v-if="form.errors.organization_email" class="mt-1 text-sm text-red-600">{{ form.errors.organization_email }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
              <input 
                v-model="form.password" 
                type="password" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                :class="{ 'border-red-500': form.errors.password }"
                required 
              />
              <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
              <input 
                v-model="form.password_confirmation" 
                type="password" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                required 
              />
            </div>
          </div>
        </div>

        <!-- Departments & Courses -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </span>
            Assign Departments & Courses <span class="text-red-500">*</span>
          </h2>
          
          <div v-if="departments && departments.length > 0" class="space-y-4">
            <div v-for="dept in departments" :key="dept.id" class="border rounded-xl p-4">
              <label class="flex items-center gap-2 cursor-pointer">
                <input 
                  type="checkbox" 
                  :value="dept.id" 
                  v-model="form.assigned_departments"
                  @change="toggleDepartment(dept.id)"
                  class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500"
                />
                <span class="font-medium">{{ dept.name }} ({{ dept.code }})</span>
              </label>
              
              <div v-if="form.assigned_departments.includes(dept.id)" class="ml-6 mt-3 grid grid-cols-2 gap-2">
                <label v-for="course in dept.courses" :key="course.id" class="flex items-center gap-2 text-sm cursor-pointer">
                  <input 
                    type="checkbox" 
                    :value="course.id" 
                    v-model="form.assigned_courses"
                    class="w-3 h-3 text-emerald-600 rounded focus:ring-emerald-500"
                  />
                  <span>{{ course.name }} ({{ course.code }})</span>
                </label>
              </div>
            </div>
          </div>
          <div v-else class="text-yellow-600 p-4 bg-yellow-50 rounded-xl">
            ⚠️ No departments loaded. Please run: php artisan db:seed --class=DepartmentCourseSeeder
          </div>
          
          <p v-if="form.errors.assigned_departments" class="mt-2 text-sm text-red-600">{{ form.errors.assigned_departments }}</p>
          <p v-if="form.errors.assigned_courses" class="mt-2 text-sm text-red-600">{{ form.errors.assigned_courses }}</p>
        </div>

        <!-- President Account -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </span>
            President Account <span class="text-red-500">*</span>
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <input 
                v-model="form.president_name" 
                type="text" 
                placeholder="Full Name" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500"
                :class="{ 'border-red-500': form.errors.president_name }"
                required 
              />
              <p v-if="form.errors.president_name" class="mt-1 text-sm text-red-600">{{ form.errors.president_name }}</p>
            </div>
            <div>
              <input 
                v-model="form.president_email" 
                type="email" 
                placeholder="Email" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500"
                :class="{ 'border-red-500': form.errors.president_email }"
                required 
              />
              <p v-if="form.errors.president_email" class="mt-1 text-sm text-red-600">{{ form.errors.president_email }}</p>
            </div>
            <div>
              <input 
                v-model="form.president_password" 
                type="password" 
                placeholder="Password" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500"
                :class="{ 'border-red-500': form.errors.president_password }"
                required 
              />
              <p v-if="form.errors.president_password" class="mt-1 text-sm text-red-600">{{ form.errors.president_password }}</p>
            </div>
            <div>
              <input 
                v-model="form.president_password_confirmation" 
                type="password" 
                placeholder="Confirm Password" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500"
                required 
              />
            </div>
          </div>
        </div>

        <!-- Adviser Account -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </span>
            Adviser Account <span class="text-red-500">*</span>
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <input 
                v-model="form.adviser_name" 
                type="text" 
                placeholder="Full Name" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500"
                :class="{ 'border-red-500': form.errors.adviser_name }"
                required 
              />
              <p v-if="form.errors.adviser_name" class="mt-1 text-sm text-red-600">{{ form.errors.adviser_name }}</p>
            </div>
            <div>
              <input 
                v-model="form.adviser_email" 
                type="email" 
                placeholder="Email" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500"
                :class="{ 'border-red-500': form.errors.adviser_email }"
                required 
              />
              <p v-if="form.errors.adviser_email" class="mt-1 text-sm text-red-600">{{ form.errors.adviser_email }}</p>
            </div>
            <div>
              <input 
                v-model="form.adviser_password" 
                type="password" 
                placeholder="Password" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500"
                :class="{ 'border-red-500': form.errors.adviser_password }"
                required 
              />
              <p v-if="form.errors.adviser_password" class="mt-1 text-sm text-red-600">{{ form.errors.adviser_password }}</p>
            </div>
            <div>
              <input 
                v-model="form.adviser_password_confirmation" 
                type="password" 
                placeholder="Confirm Password" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500"
                required 
              />
            </div>
          </div>
        </div>

        <!-- Treasurer Account -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zM12 2v2m0 16v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
              </svg>
            </span>
            Treasurer Account <span class="text-red-500">*</span>
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <input 
                v-model="form.treasurer_name" 
                type="text" 
                placeholder="Full Name" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500"
                :class="{ 'border-red-500': form.errors.treasurer_name }"
                required 
              />
              <p v-if="form.errors.treasurer_name" class="mt-1 text-sm text-red-600">{{ form.errors.treasurer_name }}</p>
            </div>
            <div>
              <input 
                v-model="form.treasurer_email" 
                type="email" 
                placeholder="Email" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500"
                :class="{ 'border-red-500': form.errors.treasurer_email }"
                required 
              />
              <p v-if="form.errors.treasurer_email" class="mt-1 text-sm text-red-600">{{ form.errors.treasurer_email }}</p>
            </div>
            <div>
              <input 
                v-model="form.treasurer_password" 
                type="password" 
                placeholder="Password" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500"
                :class="{ 'border-red-500': form.errors.treasurer_password }"
                required 
              />
              <p v-if="form.errors.treasurer_password" class="mt-1 text-sm text-red-600">{{ form.errors.treasurer_password }}</p>
            </div>
            <div>
              <input 
                v-model="form.treasurer_password_confirmation" 
                type="password" 
                placeholder="Confirm Password" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500"
                required 
              />
            </div>
          </div>
        </div>

        <!-- Form Validation Summary -->
        <div v-if="Object.keys(form.errors).length > 0" class="bg-red-50 border border-red-200 rounded-xl p-4">
          <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
          <ul class="list-disc list-inside text-sm text-red-600">
            <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
          </ul>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end gap-3">
          <Link 
            href="/admin/organizations" 
            class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all duration-300"
          >
            Cancel
          </Link>
          <button 
            type="submit" 
            class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all duration-300 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            :disabled="form.processing"
          >
            <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ form.processing ? 'Registering...' : 'Register Organization' }}</span>
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
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  departments: {
    type: Array,
    default: () => []
  }
});

const showSuccessModal = ref(false);

const form = useForm({
  // Organization
  organization_name: '',
  organization_email: '',
  password: '',
  password_confirmation: '',
  
  // Assigned departments and courses
  assigned_departments: [],
  assigned_courses: [],
  
  // President
  president_name: '',
  president_email: '',
  president_password: '',
  president_password_confirmation: '',
  
  // Adviser
  adviser_name: '',
  adviser_email: '',
  adviser_password: '',
  adviser_password_confirmation: '',
  
  // Treasurer
  treasurer_name: '',
  treasurer_email: '',
  treasurer_password: '',
  treasurer_password_confirmation: '',
});

function toggleDepartment(deptId) {
  // If department is unchecked, remove its courses from assigned_courses
  if (!form.assigned_departments.includes(deptId)) {
    const dept = props.departments.find(d => d.id === deptId);
    if (dept && dept.courses) {
      const courseIds = dept.courses.map(c => c.id);
      form.assigned_courses = form.assigned_courses.filter(id => !courseIds.includes(id));
    }
  }
}

const submit = () => {
  // Validate at least one department and course selected
  if (form.assigned_departments.length === 0) {
    form.errors.assigned_departments = 'Please select at least one department.';
    return;
  }
  
  if (form.assigned_courses.length === 0) {
    form.errors.assigned_courses = 'Please select at least one course.';
    return;
  }
  
  form.post('/admin/organizations', {
    onSuccess: () => {
      showSuccessModal.value = true;
      form.reset();
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

.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>