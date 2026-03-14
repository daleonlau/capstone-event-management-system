<template>
    <OrganizationUserLayout>
      <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Event Evaluations</h1>
            <p class="text-gray-500 mt-1">View feedback and ratings for finished events</p>
          </div>
        </div>
  
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500">Total Evaluations</p>
                <p class="text-2xl font-bold text-gray-800">{{ totalEvaluations }}</p>
              </div>
            </div>
          </div>
  
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500">Average Rating</p>
                <p class="text-2xl font-bold text-gray-800">{{ averageRating.toFixed(1) }}</p>
              </div>
            </div>
          </div>
  
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500">Finished Events</p>
                <p class="text-2xl font-bold text-gray-800">{{ events.length }}</p>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="event in events" :key="event.id" class="bg-white rounded-2xl shadow-lg overflow-hidden transform hover:scale-[1.02] transition-all duration-300">
            <div class="h-32 bg-gradient-to-r from-emerald-500 to-emerald-700 p-4">
              <div class="flex justify-between items-start">
                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white rounded-full text-xs">
                  {{ formatDate(event.event_date_end) }}
                </span>
                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white rounded-full text-xs">
                  {{ event.evaluations_count }} responses
                </span>
              </div>
            </div>
            
            <div class="p-6">
              <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ event.event_name }}</h3>
              
              <div class="flex items-center gap-2 mb-4">
                <div class="flex items-center">
                  <span v-for="n in 5" :key="n" class="text-lg">
                    <span :class="n <= Math.round(event.average_rating || 0) ? 'text-yellow-400' : 'text-gray-300'">★</span>
                  </span>
                </div>
                <span class="text-sm text-gray-600">({{ event.average_rating ? event.average_rating.toFixed(1) : '0.0' }})</span>
              </div>
  
              <div class="flex gap-2">
                <Link :href="`/adviser/evaluations/${event.id}/results`" 
                      class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition text-center text-sm">
                  View Results
                </Link>
              </div>
            </div>
          </div>
  
          <div v-if="events.length === 0" class="col-span-3 text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-gray-500">No finished events with evaluations yet.</p>
          </div>
        </div>
      </div>
    </OrganizationUserLayout>
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3';
  import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
  import { computed } from 'vue';
  
  const props = defineProps({
    events: {
      type: Array,
      default: () => []
    }
  });
  
  const totalEvaluations = computed(() => {
    return props.events.reduce((sum, event) => sum + (event.evaluations_count || 0), 0);
  });
  
  const averageRating = computed(() => {
    const ratedEvents = props.events.filter(e => e.average_rating);
    if (ratedEvents.length === 0) return 0;
    const sum = ratedEvents.reduce((acc, e) => acc + (e.average_rating || 0), 0);
    return sum / ratedEvents.length;
  });
  
  function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  }
  </script>