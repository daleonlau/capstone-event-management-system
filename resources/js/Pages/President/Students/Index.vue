<template>
  <OrganizationUserLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Students Management</h1>
          <p class="text-gray-500 mt-1">Manage your organization's students</p>
        </div>
        <div class="flex gap-3">
          <Link
            href="/president/students/bulk-upload"
            class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-all duration-300 flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Bulk Upload
          </Link>
          <Link
            href="/president/students/create"
            class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all duration-300 flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Student
          </Link>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500">Total Students</p>
              <p class="text-2xl font-bold text-gray-800">{{ students.total || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500">Programs</p>
              <p class="text-2xl font-bold text-gray-800">{{ courses.length }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500">Year Levels</p>
              <p class="text-2xl font-bold text-gray-800">4</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500">Active</p>
              <p class="text-2xl font-bold text-gray-800">{{ students.total || 0 }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-2xl shadow-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Search Students</label>
            <div class="flex gap-2">
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search by ID, name, or email..."
                class="flex-1 px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                @keyup.enter="applyFilters"
              />
              <button
                @click="applyFilters"
                class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition"
              >
                Search
              </button>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Program</label>
            <select
              v-model="filters.course"
              @change="applyFilters"
              class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
            >
              <option value="">All Programs</option>
              <option v-for="course in courses" :key="course.id" :value="course.id">
                {{ course.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Year Level</label>
            <select
              v-model="filters.year_level"
              @change="applyFilters"
              class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
            >
              <option value="">All Years</option>
              <option v-for="year in yearLevels" :key="year" :value="year">
                {{ year }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Students Table -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Level</th>
                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="student in students.data" :key="student.student_id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ student.student_id }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-lg flex items-center justify-center mr-3">
                      <span class="text-white text-xs font-bold">{{ student.firstname?.charAt(0) }}{{ student.lastname?.charAt(0) }}</span>
                    </div>
                    <span class="text-sm text-gray-900">{{ student.firstname }} {{ student.lastname }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ student.email }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ student.course }}</td>
                <td class="px-6 py-4">
                  <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">{{ student.yearlevel }}</span>
                </td>
                <td class="px-6 py-4 text-right space-x-3">
                  <button @click="editStudent(student)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                  <button @click="confirmDelete(student)" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                </td>
              </tr>
              <tr v-if="students.data.length === 0">
                <td colspan="6" class="px-6 py-8 text-center">
                  <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                  <p class="text-gray-500">No students found. Click "Add Student" to get started.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="students.links" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
          <div class="text-sm text-gray-500">
            Showing {{ students.from || 0 }} to {{ students.to || 0 }} of {{ students.total || 0 }} entries
          </div>
          <div class="flex gap-2">
            <button
              v-for="(link, index) in students.links"
              :key="index"
              v-html="link.label"
              @click="goToPage(link.url)"
              class="px-3 py-1 border rounded-lg hover:bg-gray-50 transition disabled:opacity-50"
              :class="{ 'bg-emerald-600 text-white hover:bg-emerald-700': link.active }"
              :disabled="!link.url"
            ></button>
          </div>
        </div>
      </div>

      <!-- Edit Student Modal -->
      <Transition name="modal">
        <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto">
          <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Student</h3>
                <form @submit.prevent="updateStudent" class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
                    <input v-model="editForm.student_id" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-xl bg-gray-100" readonly />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input v-model="editForm.firstname" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input v-model="editForm.lastname" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input v-model="editForm.email" type="email" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                    <select v-model="editForm.course" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required>
                      <option v-for="course in courses" :key="course.id" :value="course.name">{{ course.name }}</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year Level</label>
                    <select v-model="editForm.yearlevel" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required>
                      <option v-for="year in yearLevels" :key="year" :value="year">{{ year }}</option>
                    </select>
                  </div>
                </form>
              </div>
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button @click="updateStudent" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm">
                  Update
                </button>
                <button @click="showEditModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Delete Confirmation Modal -->
      <Transition name="modal">
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
          <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                  </div>
                  <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg font-medium text-gray-900">Delete Student</h3>
                    <div class="mt-2">
                      <p class="text-sm text-gray-500">
                        Are you sure you want to delete <span class="font-semibold">{{ selectedStudent?.firstname }} {{ selectedStudent?.lastname }}</span>? 
                        This action cannot be undone.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button @click="deleteStudent" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                  Delete
                </button>
                <button @click="showDeleteModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>

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
import { ref, reactive } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';

const props = defineProps({
  students: {
    type: Object,
    default: () => ({ data: [], links: [], total: 0 })
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  courses: {
    type: Array,
    default: () => []
  },
  yearLevels: {
    type: Array,
    default: () => ['1st Year', '2nd Year', '3rd Year', '4th Year']
  }
});

// Filters
const filters = reactive({
  search: props.filters?.search || '',
  course: props.filters?.course || '',
  year_level: props.filters?.year_level || ''
});

// Modal states
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedStudent = ref(null);

// Edit Form
const editForm = useForm({
  student_id: '',
  firstname: '',
  lastname: '',
  email: '',
  course: '',
  yearlevel: ''
});

// Apply filters
function applyFilters() {
  router.get('/president/students', filters, { preserveState: true });
}

// Pagination
function goToPage(url) {
  if (url) router.visit(url, { preserveState: true });
}

// Edit student
function editStudent(student) {
  selectedStudent.value = student;
  editForm.student_id = student.student_id;
  editForm.firstname = student.firstname;
  editForm.lastname = student.lastname;
  editForm.email = student.email;
  editForm.course = student.course;
  editForm.yearlevel = student.yearlevel;
  showEditModal.value = true;
}

// Update student
function updateStudent() {
  if (!selectedStudent.value) return;
  
  editForm.put(`/president/students/${selectedStudent.value.student_id}`, {
    onSuccess: () => {
      showEditModal.value = false;
      selectedStudent.value = null;
    }
  });
}

// Confirm delete
function confirmDelete(student) {
  selectedStudent.value = student;
  showDeleteModal.value = true;
}

// Delete student
function deleteStudent() {
  if (!selectedStudent.value) return;
  
  router.delete(`/president/students/${selectedStudent.value.student_id}`, {
    onSuccess: () => {
      showDeleteModal.value = false;
      selectedStudent.value = null;
    }
  });
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>