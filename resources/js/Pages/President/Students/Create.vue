<template>
  <OrganizationUserLayout>
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Add New Student</h1>
        <p class="text-gray-500 mt-1">Enter student information manually</p>
      </div>

      <!-- Organization Info Card -->
      <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-xl p-4 mb-6">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-emerald-800">Your Organization's Assigned Departments & Courses</h3>
            <div class="mt-2 grid grid-cols-2 gap-4">
              <div>
                <p class="text-xs font-semibold text-emerald-700">Available Departments:</p>
                <p class="text-xs text-emerald-600">{{ departmentNames }}</p>
              </div>
              <div>
                <p class="text-xs font-semibold text-emerald-700">Available Courses:</p>
                <p class="text-xs text-emerald-600">{{ courseNames }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Form Card -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Student ID -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Student ID <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.student_id"
              type="text"
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
              :class="{ 'border-red-500': form.errors.student_id }"
              placeholder="e.g., CTHM-2024-0001"
              required
            />
            <p v-if="form.errors.student_id" class="mt-1 text-sm text-red-600">{{ form.errors.student_id }}</p>
          </div>

          <!-- First Name & Last Name -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                First Name <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.firstname"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                :class="{ 'border-red-500': form.errors.firstname }"
                placeholder="Enter first name"
                required
              />
              <p v-if="form.errors.firstname" class="mt-1 text-sm text-red-600">{{ form.errors.firstname }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Last Name <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.lastname"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                :class="{ 'border-red-500': form.errors.lastname }"
                placeholder="Enter last name"
                required
              />
              <p v-if="form.errors.lastname" class="mt-1 text-sm text-red-600">{{ form.errors.lastname }}</p>
            </div>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Email Address <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.email"
              type="email"
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
              :class="{ 'border-red-500': form.errors.email }"
              placeholder="student@example.com"
              required
            />
            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
          </div>

          <!-- Department Dropdown -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Department <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.department"
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
              :class="{ 'border-red-500': form.errors.department }"
              required
            >
              <option value="">Select Department</option>
              <option v-for="dept in departments" :key="dept.id" :value="dept.name">
                {{ dept.name }} ({{ dept.code }})
              </option>
            </select>
            <p v-if="form.errors.department" class="mt-1 text-sm text-red-600">{{ form.errors.department }}</p>
            <p v-if="departments.length === 0" class="mt-1 text-sm text-yellow-600">
              No departments assigned to your organization. Please contact admin.
            </p>
          </div>

          <!-- Course Dropdown -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Course <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.course"
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
              :class="{ 'border-red-500': form.errors.course }"
              required
            >
              <option value="">Select Course</option>
              <option v-for="course in filteredCourses" :key="course.id" :value="course.name">
                {{ course.name }} ({{ course.code }})
              </option>
            </select>
            <p v-if="form.errors.course" class="mt-1 text-sm text-red-600">{{ form.errors.course }}</p>
            <p v-if="courses.length === 0" class="mt-1 text-sm text-yellow-600">
              No courses assigned to your organization. Please contact admin.
            </p>
          </div>

          <!-- Year Level -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Year Level <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.yearlevel"
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
              :class="{ 'border-red-500': form.errors.yearlevel }"
              required
            >
              <option value="">Select Year Level</option>
              <option v-for="year in yearLevels" :key="year" :value="year">
                {{ year }}
              </option>
            </select>
            <p v-if="form.errors.yearlevel" class="mt-1 text-sm text-red-600">{{ form.errors.yearlevel }}</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <Link
              href="/president/students"
              class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition"
            >
              Cancel
            </Link>
            <button
              type="submit"
              class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all duration-300 transform hover:scale-[1.02] disabled:opacity-50 flex items-center gap-2"
              :disabled="form.processing"
            >
              <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ form.processing ? 'Saving...' : 'Save Student' }}</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Success Toast -->
      <Transition name="fade">
        <div v-if="$page.props.flash?.success" class="fixed bottom-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-lg z-50">
          {{ $page.props.flash.success }}
        </div>
      </Transition>
    </div>
  </OrganizationUserLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';

const props = defineProps({
  courses: {
    type: Array,
    default: () => []
  },
  departments: {
    type: Array,
    default: () => []
  },
  yearLevels: {
    type: Array,
    default: () => ['1st Year', '2nd Year', '3rd Year', '4th Year']
  }
});

// Compute display strings
const departmentNames = computed(() => {
  return props.departments.map(d => d.name).join(', ') || 'None';
});

const courseNames = computed(() => {
  return props.courses.map(c => c.name).join(', ') || 'None';
});

// Filter courses based on selected department (optional - if you want to filter by department)
const filteredCourses = computed(() => {
  if (!form.department) return props.courses;
  
  // If you want to show only courses under the selected department
  // This assumes your course objects have a department_id or department_name field
  // Adjust this logic based on your actual data structure
  return props.courses.filter(course => {
    // Example: if course has department_name property
    // return course.department_name === form.department;
    
    // For now, return all courses (remove this filter if not needed)
    return true;
  });
});

const form = useForm({
  student_id: '',
  firstname: '',
  lastname: '',
  email: '',
  department: '',
  course: '',
  yearlevel: ''
});

const submit = () => {
  // Validate required fields
  if (!form.department && props.departments.length > 0) {
    form.errors.department = 'Please select a department';
    return;
  }

  if (!form.course && props.courses.length > 0) {
    form.errors.course = 'Please select a course';
    return;
  }

  form.post('/president/students', {
    onSuccess: () => {
      form.reset();
    },
    onError: (errors) => {
      console.error('Validation errors:', errors);
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

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>