<template>
    <AdminLayout>
      <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-8">
          <h1 class="text-3xl font-extrabold text-red-600 tracking-tight">
            My Profile
          </h1>
          <div class="mt-3 md:mt-0 flex items-center gap-4">
            <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 font-semibold text-sm">
              Admin
            </span>
            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold text-sm">
              {{ props.admin.email_verified_at ? 'Verified' : 'Unverified' }}
            </span>
          </div>
        </div>
  
        <!-- Profile Card -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 p-8 max-w-3xl mx-auto">
          <!-- User Info -->
          <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
            <div class="flex-shrink-0">
              <div
                class="w-24 h-24 rounded-full bg-red-600 text-white flex items-center justify-center text-3xl font-bold shadow-md"
              >
                {{ props.admin.name.charAt(0) }}
              </div>
            </div>
            <div class="flex-1 w-full">
              <h2 class="text-2xl font-bold text-gray-800 mb-1">{{ props.admin.name }}</h2>
              <p class="text-gray-500 mb-2">{{ props.admin.email }}</p>
              <p class="text-gray-400 text-sm">Last login: {{ props.admin.last_login || 'N/A' }}</p>
            </div>
          </div>
  
          <hr class="my-6 border-gray-200" />
  
          <!-- Update Form -->
          <form @submit.prevent="updateProfile" class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-1">Full Name</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 placeholder-gray-300"
                placeholder="Enter full name"
                required
              />
            </div>
  
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-1">Email Address</label>
              <input
                v-model="form.email"
                type="email"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 placeholder-gray-300"
                placeholder="Enter email"
                required
              />
            </div>
  
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-1">New Password</label>
              <input
                v-model="form.password"
                type="password"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 placeholder-gray-300"
                placeholder="Leave blank to keep current password"
              />
            </div>
  
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-1">Confirm New Password</label>
              <input
                v-model="form.password_confirmation"
                type="password"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 placeholder-gray-300"
                placeholder="Confirm new password"
              />
            </div>
  
            <div class="flex justify-end">
              <button
                type="submit"
                class="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition transform hover:scale-105"
              >
                Update Profile
              </button>
            </div>
          </form>
        </div>
  
        <!-- Password Update Confirmation Modal -->
        <transition name="fade">
          <div
            v-if="showPasswordConfirmModal"
            class="fixed inset-0 flex items-center justify-center z-50 bg-black/30"
            @click.self="cancelPasswordUpdate"
          >
            <div
              class="bg-white border border-gray-200 rounded-xl shadow-2xl p-8 w-full max-w-md text-center"
            >
              <h2 class="text-2xl font-bold text-blue-600 mb-3">
                Confirm Password Update
              </h2>
              <p class="text-gray-600 mb-6">
                Are you sure you want to update your password?
              </p>
              <div class="flex justify-center gap-4">
                <button
                  @click="cancelPasswordUpdate"
                  class="px-5 py-2 rounded-lg border border-gray-400 hover:bg-gray-100 transition"
                >
                  Cancel
                </button>
                <button
                  @click="confirmPasswordUpdate"
                  class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition"
                >
                  Yes, Update
                </button>
              </div>
            </div>
          </div>
        </transition>
  
        <!-- Success Modal -->
        <transition name="fade">
          <div
            v-if="showSuccessModal"
            class="fixed inset-0 flex items-center justify-center z-50 bg-black/20"
            @click.self="closeSuccessModal"
          >
            <div
              class="bg-white border border-green-300 rounded-xl shadow-2xl p-8 w-full max-w-md text-center"
            >
              <h2 class="text-2xl font-bold text-green-600 mb-3">
                🎉 Success!
              </h2>
              <p class="text-gray-600 mb-6">
                {{ successMessage }}
              </p>
              <button
                @click="closeSuccessModal"
                class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition"
              >
                OK
              </button>
            </div>
          </div>
        </transition>
      </div>
    </AdminLayout>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import AdminLayout from "@/Layouts/AdminLayout.vue";
  import { useForm } from "@inertiajs/vue3";
  
  const props = defineProps({
    admin: Object
  });
  
  const form = useForm({
    name: props.admin.name,
    email: props.admin.email,
    password: "",
    password_confirmation: "",
  });
  
  const successMessage = ref("");
  const showSuccessModal = ref(false);
  const showPasswordConfirmModal = ref(false);
  
  // Trigger update
  function updateProfile() {
    if (form.password || form.password_confirmation) {
      // Show confirmation modal if password is being changed
      showPasswordConfirmModal.value = true;
    } else {
      // Update profile without password
      submitProfileUpdate();
    }
  }
  
  function cancelPasswordUpdate() {
    showPasswordConfirmModal.value = false;
  }
  
  function confirmPasswordUpdate() {
    showPasswordConfirmModal.value = false;
    submitProfileUpdate();
  }
  
  function submitProfileUpdate() {
    form.put("/admin/profile", {
      onSuccess: () => {
        form.password = "";
        form.password_confirmation = "";
        successMessage.value = "Profile updated successfully!";
        showSuccessModal.value = true;
      },
    });
  }
  
  function closeSuccessModal() {
    showSuccessModal.value = false;
  }
  </script>
  
  <style scoped>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.25s ease;
  }
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
  </style>
  