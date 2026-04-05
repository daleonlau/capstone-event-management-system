<template>
  <OrganizationUserLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
          <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              Evaluation Results
            </h1>
            <p class="text-gray-500 mt-1">View evaluation results for your finished events</p>
          </div>
        </div>

        <!-- Info Alert -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4 mb-6">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-blue-800">
              <p class="font-medium">Evaluation Results</p>
              <p>You can view evaluation results and AI insights for your events. To create a new evaluation, please submit a request through the event details page after the event is finished.</p>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-2xl shadow-md p-4 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500">Total Responses</p>
                <p class="text-2xl font-bold text-gray-800">{{ totalResponses }}</p>
              </div>
              <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-white rounded-2xl shadow-md p-4 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500">Total Expected</p>
                <p class="text-2xl font-bold text-gray-800">{{ totalExpected }}</p>
              </div>
              <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-white rounded-2xl shadow-md p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500">Active Evaluations</p>
                <p class="text-2xl font-bold text-blue-600">{{ activeCount }}</p>
              </div>
              <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-white rounded-2xl shadow-md p-4 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500">Avg. Response Rate</p>
                <p class="text-2xl font-bold text-indigo-600">{{ avgResponseRate }}%</p>
              </div>
              <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
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
              @click="activeTab = 'closed'"
              class="flex-1 px-6 py-4 text-sm font-medium transition-colors relative"
              :class="activeTab === 'closed' ? 'text-emerald-600' : 'text-gray-500 hover:text-gray-700'"
            >
              Completed
              <span v-if="activeTab === 'closed'" class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-600"></span>
            </button>
          </div>
        </div>

        <!-- Evaluations Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="evaluation in filteredEvaluations" 
            :key="evaluation.id" 
            class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden cursor-pointer transform hover:-translate-y-2"
            @click="goToEvaluation(evaluation.id)"
          >
            <div class="relative h-1.5" :class="{
              'bg-gray-400': evaluation.status === 'draft',
              'bg-gradient-to-r from-green-500 to-emerald-500': evaluation.status === 'active',
              'bg-gradient-to-r from-blue-500 to-indigo-500': evaluation.status === 'closed'
            }"></div>
            
            <div class="p-6">
              <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-md flex items-center justify-center">
                      <span class="text-white font-bold text-lg">{{ evaluation.title?.charAt(0) || 'E' }}</span>
                    </div>
                    <div>
                      <h3 class="font-bold text-gray-800 line-clamp-1 group-hover:text-emerald-600 transition-colors">
                        {{ evaluation.title }}
                      </h3>
                      <p class="text-xs text-gray-500 line-clamp-1">{{ evaluation.event_name }}</p>
                    </div>
                  </div>
                </div>
                <span class="px-3 py-1 text-xs font-bold rounded-full shadow-sm" :class="{
                  'bg-gray-100 text-gray-700 border border-gray-200': evaluation.status === 'draft',
                  'bg-green-100 text-green-700 border border-green-200': evaluation.status === 'active',
                  'bg-blue-100 text-blue-700 border border-blue-200': evaluation.status === 'closed'
                }">
                  {{ evaluation.status === 'closed' ? 'Completed' : evaluation.status }}
                </span>
              </div>

              <div class="flex flex-wrap gap-2 mb-4">
                <span class="text-xs px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full">
                  {{ getFormTypeName(evaluation.form_type) }}
                </span>
                <span class="text-xs px-2.5 py-1 bg-purple-50 text-purple-600 rounded-full">
                  {{ evaluation.organization_name }}
                </span>
              </div>

              <!-- Response Stats with Progress Bar -->
              <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                  <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="text-sm font-semibold text-gray-700">{{ evaluation.responses_count }} / {{ evaluation.expected_count }}</span>
                  </div>
                  <span :class="getRateTextClass(evaluation.response_rate)" class="text-sm font-bold">
                    {{ evaluation.response_rate }}%
                  </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div class="h-full rounded-full transition-all" 
                       :class="getRateColorClass(evaluation.response_rate)"
                       :style="{ width: evaluation.response_rate + '%' }">
                  </div>
                </div>
                <div class="flex justify-between text-xs text-gray-400 mt-1">
                  <span>{{ evaluation.students_count }} students × {{ evaluation.number_of_dates || 1 }} days</span>
                  <span>{{ evaluation.guests_count }} guests</span>
                </div>
                <p class="text-xs text-gray-400 mt-1 text-center">
                  Expected = ({{ evaluation.students_count }} × {{ evaluation.number_of_dates || 1 }}) + {{ evaluation.guests_count }} = {{ evaluation.expected_count }}
                </p>
              </div>

              <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                <div class="flex items-center gap-1.5">
                  <div class="w-2 h-2 rounded-full" :class="{
                    'bg-gray-400': evaluation.status === 'draft',
                    'bg-green-500': evaluation.status === 'active',
                    'bg-blue-500': evaluation.status === 'closed'
                  }"></div>
                  <span class="text-xs text-gray-500">{{ getStatusMessage(evaluation.status) }}</span>
                </div>
                <span class="text-emerald-600 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                  View Results
                  <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </span>
              </div>
            </div>
          </div>

          <div v-if="filteredEvaluations.length === 0" class="col-span-3">
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
              <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <h3 class="text-lg font-medium text-gray-900 mb-2">No Evaluations Found</h3>
              <p class="text-gray-500 mb-4">Evaluation results will appear here after QUAMS processes your evaluation request.</p>
              <Link 
                href="/adviser/events" 
                class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition"
              >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                </svg>
                View Your Events
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
  }
});

const activeTab = ref('all');

const filteredEvaluations = computed(() => {
  if (activeTab.value === 'all') {
    return props.evaluations;
  }
  return props.evaluations.filter(e => e.status === activeTab.value);
});

const activeCount = computed(() => props.evaluations.filter(e => e.status === 'active').length);
const closedCount = computed(() => props.evaluations.filter(e => e.status === 'closed').length);
const draftCount = computed(() => props.evaluations.filter(e => e.status === 'draft').length);

const totalResponses = computed(() => {
  return props.evaluations.reduce((sum, e) => sum + (e.responses_count || 0), 0);
});

const totalExpected = computed(() => {
  return props.evaluations.reduce((sum, e) => sum + (e.expected_count || 0), 0);
});

const avgResponseRate = computed(() => {
  if (props.evaluations.length === 0) return 0;
  const total = props.evaluations.reduce((sum, e) => sum + (e.response_rate || 0), 0);
  return Math.round(total / props.evaluations.length);
});

function getRateColorClass(rate) {
  if (rate >= 75) return 'bg-green-500';
  if (rate >= 50) return 'bg-yellow-500';
  if (rate >= 25) return 'bg-orange-500';
  return 'bg-red-500';
}

function getRateTextClass(rate) {
  if (rate >= 75) return 'text-green-600';
  if (rate >= 50) return 'text-yellow-600';
  if (rate >= 25) return 'text-orange-600';
  return 'text-red-600';
}

function getFormTypeName(formType) {
  const types = {
    type1: '7 Quality Dimension',
    type2: '5 Quality Dimension',
    type3: '8 Quality Dimension',
    type4: '6 Quality Dimension (With Speaker)',
    type5: '6 Quality Dimension (With Food)'
  };
  return types[formType] || formType;
}

function getStatusMessage(status) {
  switch(status) {
    case 'draft': return 'Ready for activation';
    case 'active': return 'Collecting responses';
    case 'closed': return 'Analysis ready';
    default: return '';
  }
}

function goToEvaluation(id) {
  router.visit(`/adviser/evaluations/${id}`);
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