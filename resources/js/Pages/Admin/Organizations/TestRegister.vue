<template>
    <AdminLayout>
      <div class="p-6 max-w-lg mx-auto">
        <h1 class="text-2xl font-bold mb-6">Test Organization Registration</h1>
        
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block mb-1">Organization Name</label>
            <input v-model="form.organization_name" class="w-full border p-2" required>
          </div>
          
          <div>
            <label class="block mb-1">Organization Email</label>
            <input v-model="form.organization_email" type="email" class="w-full border p-2" required>
          </div>
          
          <div>
            <label class="block mb-1">Password</label>
            <input v-model="form.password" type="password" class="w-full border p-2" required>
          </div>
          
          <div>
            <label class="block mb-1">Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" class="w-full border p-2" required>
          </div>
          
          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
            Register
          </button>
        </form>
        
        <div v-if="form.errors" class="mt-4 text-red-600">
          {{ form.errors }}
        </div>
      </div>
    </AdminLayout>
  </template>
  
  <script setup>
  import { useForm } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';
  
  const form = useForm({
    organization_name: '',
    organization_email: '',
    password: '',
    password_confirmation: '',
    // Add minimal required fields
    assigned_departments: [1], // Temporary
    assigned_courses: [1], // Temporary
    president_name: 'Test President',
    president_email: 'president@test.com',
    president_password: 'password123',
    president_password_confirmation: 'password123',
    adviser_name: 'Test Adviser',
    adviser_email: 'adviser@test.com',
    adviser_password: 'password123',
    adviser_password_confirmation: 'password123',
    treasurer_name: 'Test Treasurer',
    treasurer_email: 'treasurer@test.com',
    treasurer_password: 'password123',
    treasurer_password_confirmation: 'password123',
  });
  
  const submit = () => {
    form.post('/admin/organizations', {
      onSuccess: () => {
        alert('Success!');
      },
      onError: (errors) => {
        console.log('Errors:', errors);
        alert('Error: ' + JSON.stringify(errors));
      }
    });
  };
  </script> 