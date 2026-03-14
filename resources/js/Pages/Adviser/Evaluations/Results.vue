<template>
    <OrganizationUserLayout>
      <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Evaluation Results</h1>
            <p class="text-gray-500 mt-1">{{ event.event_name }}</p>
          </div>
          <Link href="/adviser/evaluations" 
                class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">
            Back
          </Link>
        </div>
  
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-sm text-gray-500 mb-1">Total Responses</p>
            <p class="text-3xl font-bold text-gray-800">{{ summary.total_responses }}</p>
          </div>
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-sm text-gray-500 mb-1">Overall Average</p>
            <p class="text-3xl font-bold" :class="getAverageColor(summary.overall_average)">
              {{ summary.overall_average.toFixed(2) }}
            </p>
          </div>
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-sm text-gray-500 mb-1">Highest Rated</p>
            <p class="text-lg font-semibold text-green-600 truncate" :title="summary.highest_question">
              {{ summary.highest_question || 'N/A' }}
            </p>
          </div>
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-sm text-gray-500 mb-1">Lowest Rated</p>
            <p class="text-lg font-semibold text-red-600 truncate" :title="summary.lowest_question">
              {{ summary.lowest_question || 'N/A' }}
            </p>
          </div>
        </div>
  
        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Bar Chart -->
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Average Ratings per Question</h3>
            <div style="height: 300px;">
              <canvas ref="barChartRef"></canvas>
            </div>
          </div>
  
          <!-- Radar Chart -->
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Rating Distribution</h3>
            <div style="height: 300px;">
              <canvas ref="radarChartRef"></canvas>
            </div>
          </div>
        </div>
  
        <!-- Detailed Results Table -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Detailed Results</h3>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Question</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Average</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">1 ★</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">2 ★</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">3 ★</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">4 ★</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">5 ★</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="(item, index) in evaluationData" :key="index" class="hover:bg-gray-50">
                  <td class="px-4 py-3 text-sm text-gray-900">{{ item.question }}</td>
                  <td class="px-4 py-3 text-center font-semibold" :class="getAverageColor(item.average)">
                    {{ item.average.toFixed(2) }}
                  </td>
                  <td class="px-4 py-3 text-center text-sm text-gray-600">{{ item.distribution[1] || 0 }}</td>
                  <td class="px-4 py-3 text-center text-sm text-gray-600">{{ item.distribution[2] || 0 }}</td>
                  <td class="px-4 py-3 text-center text-sm text-gray-600">{{ item.distribution[3] || 0 }}</td>
                  <td class="px-4 py-3 text-center text-sm text-gray-600">{{ item.distribution[4] || 0 }}</td>
                  <td class="px-4 py-3 text-center text-sm text-gray-600">{{ item.distribution[5] || 0 }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
  
        <!-- Recommendations -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Recommendations</h3>
          <ul class="space-y-2">
            <li v-if="summary.overall_average < 3" class="flex items-start gap-2 text-red-600">
              <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <span>Overall satisfaction is low. Consider improving event organization and content.</span>
            </li>
            <li v-for="(item, index) in evaluationData" :key="index" v-if="item.average < 3" class="flex items-start gap-2 text-yellow-600">
              <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Low rating on "{{ item.question }}" - review and improve this aspect.</span>
            </li>
            <li v-if="summary.overall_average >= 4" class="flex items-start gap-2 text-green-600">
              <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Overall positive feedback! Maintain current quality standards.</span>
            </li>
          </ul>
        </div>
      </div>
    </OrganizationUserLayout>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { Link } from '@inertiajs/vue3';
  import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
  import Chart from 'chart.js/auto';
  
  const props = defineProps({
    event: Object,
    evaluationData: Array,
    summary: Object
  });
  
  const barChartRef = ref(null);
  const radarChartRef = ref(null);
  let barChart = null;
  let radarChart = null;
  
  function getAverageColor(average) {
    if (average >= 4) return 'text-green-600';
    if (average >= 3) return 'text-yellow-600';
    return 'text-red-600';
  }
  
  onMounted(() => {
    if (props.evaluationData.length > 0) {
      // Destroy existing charts
      if (barChart) barChart.destroy();
      if (radarChart) radarChart.destroy();
  
      // Bar Chart
      barChart = new Chart(barChartRef.value, {
        type: 'bar',
        data: {
          labels: props.evaluationData.map(d => `Q${d.question_number}`),
          datasets: [{
            label: 'Average Rating',
            data: props.evaluationData.map(d => d.average),
            backgroundColor: 'rgba(16, 185, 129, 0.5)',
            borderColor: 'rgb(16, 185, 129)',
            borderWidth: 1,
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: { 
              beginAtZero: true, 
              max: 5,
              grid: { color: 'rgba(0, 0, 0, 0.05)' }
            },
            x: { grid: { display: false } }
          }
        }
      });
  
      // Radar Chart
      radarChart = new Chart(radarChartRef.value, {
        type: 'radar',
        data: {
          labels: props.evaluationData.map(d => `Q${d.question_number}`),
          datasets: [{
            label: 'Average Rating',
            data: props.evaluationData.map(d => d.average),
            backgroundColor: 'rgba(16, 185, 129, 0.2)',
            borderColor: 'rgb(16, 185, 129)',
            pointBackgroundColor: 'rgb(16, 185, 129)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgb(16, 185, 129)'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            r: { 
              beginAtZero: true, 
              max: 5,
              ticks: { stepSize: 1 }
            }
          }
        }
      });
    }
  });
  </script>