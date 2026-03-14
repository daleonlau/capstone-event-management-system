<template>
    <OrganizationUserLayout>
      <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <!-- Header -->
          <div class="flex justify-between items-center mb-8">
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                Event Evaluations
              </h1>
              <p class="text-gray-500 mt-1">Create and manage evaluation questionnaires</p>
            </div>
            <Link 
              href="/president/evaluations/create" 
              class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all hover:shadow-lg flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Create Evaluation
            </Link>
          </div>
  
          <!-- Available Events Alert (Finished events without evaluations) -->
          <div v-if="availableEvents.length > 0" class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4 mb-8">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="flex-1">
                <p class="text-sm text-blue-800 font-medium mb-2">
                  Finished events ready for evaluation:
                </p>
                <div class="flex flex-wrap gap-2">
                  <Link 
                    v-for="event in availableEvents" 
                    :key="event.id"
                    :href="`/president/evaluations/create?event_id=${event.id}`"
                    class="inline-flex items-center gap-1 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm hover:bg-blue-200 transition group"
                  >
                    {{ event.event_name }}
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </Link>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Tabs -->
          <div class="bg-white rounded-t-xl shadow-lg overflow-hidden mb-6">
            <div class="flex border-b border-gray-200">
              <button 
                @click="activeTab = 'all'"
                class="flex-1 px-6 py-4 text-sm font-medium transition-colors relative"
                :class="activeTab === 'all' ? 'text-emerald-600' : 'text-gray-500 hover:text-gray-700'"
              >
                All Evaluations
                <span v-if="activeTab === 'all'" class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-600"></span>
              </button>
              <button 
                @click="activeTab = 'draft'"
                class="flex-1 px-6 py-4 text-sm font-medium transition-colors relative"
                :class="activeTab === 'draft' ? 'text-emerald-600' : 'text-gray-500 hover:text-gray-700'"
              >
                Draft
                <span v-if="activeTab === 'draft'" class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-600"></span>
              </button>
              <button 
                @click="activeTab = 'active'"
                class="flex-1 px-6 py-4 text-sm font-medium transition-colors relative"
                :class="activeTab === 'active' ? 'text-emerald-600' : 'text-gray-500 hover:text-gray-700'"
              >
                Active
                <span v-if="activeTab === 'active'" class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-600"></span>
              </button>
              <button 
                @click="activeTab = 'closed'"
                class="flex-1 px-6 py-4 text-sm font-medium transition-colors relative"
                :class="activeTab === 'closed' ? 'text-emerald-600' : 'text-gray-500 hover:text-gray-700'"
              >
                Closed
                <span v-if="activeTab === 'closed'" class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-600"></span>
              </button>
            </div>
          </div>
  
          <!-- Evaluations Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="evaluation in filteredEvaluations" 
              :key="evaluation.id" 
              class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all overflow-hidden cursor-pointer"
              @click="goToEvaluation(evaluation.id)"
            >
              <!-- Status Header Bar -->
              <div class="h-2" :class="{
                'bg-gray-400': evaluation.status === 'draft',
                'bg-green-500': evaluation.status === 'active',
                'bg-blue-500': evaluation.status === 'closed'
              }"></div>
              
              <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-start mb-3">
                  <div>
                    <h3 class="font-semibold text-gray-800 line-clamp-1">{{ evaluation.title }}</h3>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ evaluation.event_name }}</p>
                  </div>
                  <span class="px-3 py-1 text-xs rounded-full whitespace-nowrap" :class="{
                    'bg-gray-100 text-gray-700': evaluation.status === 'draft',
                    'bg-green-100 text-green-700': evaluation.status === 'active',
                    'bg-blue-100 text-blue-700': evaluation.status === 'closed'
                  }">
                    {{ evaluation.status }}
                  </span>
                </div>
  
                <!-- Form Number -->
                <div class="mb-2 text-xs text-gray-400">
                  {{ evaluation.form_number }}
                </div>
  
                <!-- Event Status Badge -->
                <div class="mb-3">
                  <span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded-full">
                    Event: {{ evaluation.event_status }}
                  </span>
                </div>
  
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-2 my-4">
                  <div class="text-center">
                    <p class="text-lg font-bold text-gray-800">{{ evaluation.categories_count }}</p>
                    <p class="text-xs text-gray-500">Categories</p>
                  </div>
                  <div class="text-center">
                    <p class="text-lg font-bold text-emerald-600">{{ evaluation.responses_count }}</p>
                    <p class="text-xs text-gray-500">Responses</p>
                  </div>
                  <div class="text-center">
                    <p class="text-lg font-bold text-blue-600">{{ evaluation.questions_count }}</p>
                    <p class="text-xs text-gray-500">Questions</p>
                  </div>
                </div>
  
                <!-- QR Status Indicator for Draft -->
                <div v-if="evaluation.status === 'draft'" class="mb-3">
                  <div class="flex items-center gap-2 text-sm" :class="evaluation.can_generate_qr ? 'text-green-600' : 'text-yellow-600'">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path v-if="evaluation.can_generate_qr" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs">
                      {{ evaluation.can_generate_qr ? 'Ready for QR generation' : 'Waiting for event to be finished' }}
                    </span>
                  </div>
                </div>
  
                <!-- Footer -->
                <div class="flex justify-between items-center text-sm text-gray-500 pt-4 border-t border-gray-100">
                  <span>{{ evaluation.created_at }}</span>
                  <span class="text-emerald-600 font-medium flex items-center gap-1 group-hover:gap-2 transition-all">
                    {{ evaluation.status === 'draft' ? 'Edit / Setup' : 'View Results' }}
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </span>
                </div>
              </div>
            </div>
  
            <!-- Empty State -->
            <div v-if="filteredEvaluations.length === 0" class="col-span-3">
              <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Evaluations Found</h3>
                <p class="text-gray-500 mb-4">
                  {{ activeTab === 'all' ? 'Create your first evaluation questionnaire.' : `No ${activeTab} evaluations available.` }}
                </p>
                <Link 
                  v-if="activeTab === 'all' || activeTab === 'draft'"
                  href="/president/evaluations/create" 
                  class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition"
                >
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  Create Evaluation
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </OrganizationUserLayout>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue';
  import { Link, router } from '@inertiajs/vue3';
  import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
  
  const props = defineProps({
    evaluations: {
      type: Array,
      default: () => []
    },
    availableEvents: {
      type: Array,
      default: () => []
    }
  });
  
  const activeTab = ref('all');
  
  const filteredEvaluations = computed(() => {
    if (activeTab.value === 'all') {
      return props.evaluations;
    }
    return props.evaluations.filter(e => e.status === activeTab.value);
  });
  
  function goToEvaluation(id) {
    router.visit(`/president/evaluations/${id}`);
  }
  </script>
  
  <style scoped>
  .line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  </style>