<template>
  <AdminLayout>
    <div class="max-w-3xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">My Profile</h1>
        <p class="text-gray-500 mt-1">Manage your account settings</p>
      </div>

      <!-- Profile Card -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Cover -->
        <div class="h-32 bg-gradient-to-r  from-emerald-500 to-emerald-700"></div>
        
        <!-- Profile Info -->
        <div class="px-8 pb-8">
          <div class="flex items-end -mt-12 mb-6">
            <div class="w-24 h-24 bg-white rounded-2xl shadow-lg p-1">
              <div class="w-full h-full bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center">
                <span class="text-white text-3xl font-bold">{{ admin.name.charAt(0) }}</span>
              </div>
            </div>
            <div class="ml-6">
              <h2 class="text-2xl font-bold text-gray-800">{{ admin.name }}</h2>
              <p class="text-gray-500">{{ admin.email }}</p>
            </div>
            <div class="ml-auto">
              <span class="px-4 py-2 bg-green-100 text-green-700 rounded-xl font-medium text-sm capitalize">Administrator</span>
            </div>
          </div>

          <!-- Update Form -->
          <form @submit.prevent="updateProfile" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input 
                  v-model="form.name" 
                  type="text" 
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                  required 
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input 
                  v-model="form.email" 
                  type="email" 
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                  required 
                />
              </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                  <input 
                    v-model="form.password" 
                    type="password" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                    placeholder="Leave blank to keep current" 
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                  <input 
                    v-model="form.password_confirmation" 
                    type="password" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                    placeholder="Confirm new password" 
                  />
                </div>
              </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
              <button 
                type="submit" 
                class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition disabled:opacity-50" 
                :disabled="form.processing"
              >
                {{ form.processing ? 'Updating...' : 'Update Profile' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const page = usePage();
const admin = page.props.auth.user;

const form = useForm({
  name: admin.name,
  email: admin.email,
  password: '',
  password_confirmation: ''
});

const updateProfile = () => {
  form.put('/admin/profile', {
    onSuccess: () => {
      form.password = '';
      form.password_confirmation = '';
    }
  });
};
</script>