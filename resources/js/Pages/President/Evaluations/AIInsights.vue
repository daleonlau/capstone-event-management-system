<template>
    <div class="space-y-6">
      <!-- Loading State -->
      <div v-if="loading" class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600 mx-auto mb-4"></div>
        <p class="text-gray-600">AI is analyzing your evaluation data...</p>
        <p class="text-sm text-gray-400 mt-2">This may take a few moments</p>
      </div>
  
      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 rounded-2xl shadow-lg p-6">
        <div class="flex items-center gap-3 text-red-800">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <p class="font-medium">{{ error }}</p>
        </div>
      </div>
  
      <!-- Insights Display -->
      <div v-else-if="insights">
        <!-- Executive Summary -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
          <div class="flex items-center gap-3 mb-3">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            <h2 class="text-xl font-bold">AI-Generated Event Insights</h2>
          </div>
          <p class="text-lg">{{ insights.summary }}</p>
          <p class="text-sm text-purple-200 mt-2">
            Analyzed: {{ formatDate(insights.analyzed_at) }}
          </p>
        </div>
  
        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-sm text-gray-500 mb-1">Predicted Satisfaction</p>
            <div class="flex items-end gap-2">
              <span class="text-3xl font-bold text-emerald-600">{{ insights.predicted_satisfaction }}</span>
              <span class="text-gray-400">/5.0</span>
            </div>
          </div>
          <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-sm text-gray-500 mb-1">Success Probability</p>
            <div class="flex items-end gap-2">
              <span class="text-3xl font-bold text-blue-600">{{ (insights.success_probability * 100).toFixed(0) }}%</span>
            </div>
          </div>
          <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-sm text-gray-500 mb-1">Total Responses</p>
            <div class="flex items-end gap-2">
              <span class="text-3xl font-bold text-gray-800">{{ totalResponses }}</span>
            </div>
          </div>
        </div>
  
        <!-- Strengths & Weaknesses -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Strengths -->
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </span>
              Event Strengths
            </h3>
            <ul class="space-y-2">
              <li v-for="(strength, index) in insights.strengths" :key="index" 
                  class="flex items-start gap-2 text-green-700">
                <span class="text-green-600">●</span>
                <span>{{ strength }}</span>
              </li>
              <li v-if="!insights.strengths.length" class="text-gray-400 italic">
                No specific strengths identified
              </li>
            </ul>
          </div>
  
          <!-- Areas for Improvement -->
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <span class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </span>
              Areas for Improvement
            </h3>
            <ul class="space-y-2">
              <li v-for="(weakness, index) in insights.weaknesses" :key="index" 
                  class="flex items-start gap-2 text-yellow-700">
                <span class="text-yellow-600">●</span>
                <span>{{ weakness }}</span>
              </li>
              <li v-if="!insights.weaknesses.length" class="text-gray-400 italic">
                No areas for improvement identified
              </li>
            </ul>
          </div>
        </div>
  
        <!-- Recommendations -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
              </svg>
            </span>
            AI Recommendations
          </h3>
          <ul class="space-y-3">
            <li v-for="(rec, index) in insights.recommendations" :key="index" 
                class="p-3 bg-blue-50 rounded-lg text-blue-800">
              {{ rec }}
            </li>
            <li v-if="!insights.recommendations.length" class="text-gray-400 italic">
              No recommendations available
            </li>
          </ul>
        </div>
  
        <!-- Critical Factors from Random Forest -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">🎯 Critical Success Factors</h3>
          <p class="text-sm text-gray-500 mb-4">Based on Random Forest feature importance analysis</p>
          
          <div class="space-y-3">
            <div v-for="(factor, index) in insights.critical_factors" :key="index"
                 class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div>
                <span class="font-medium text-gray-800">{{ factor.factor }}</span>
                <p class="text-xs text-gray-500">Current score: {{ factor.current_score }}/5</p>
              </div>
              <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs">
                {{ factor.importance }} importance
              </span>
            </div>
          </div>
        </div>
  
        <!-- Category Breakdown -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Category Performance</h3>
          <div class="space-y-3">
            <div v-for="(score, category) in insights.category_breakdown" :key="category"
                 class="flex items-center gap-3">
              <span class="w-32 text-sm text-gray-600">{{ category }}</span>
              <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all"
                     :class="getScoreColor(score)"
                     :style="{ width: (score / 5 * 100) + '%' }">
                </div>
              </div>
              <span class="text-sm font-medium" :class="getScoreTextColor(score)">
                {{ score.toFixed(1) }}
              </span>
            </div>
          </div>
        </div>
      </div>
  
      <!-- No Data State -->
      <div v-else class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No AI Insights Yet</h3>
        <p class="text-gray-500">Close the evaluation to trigger AI analysis.</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  
  const props = defineProps({
    evaluationId: {
      type: Number,
      required: true
    },
    totalResponses: {
      type: Number,
      default: 0
    }
  });
  
  const loading = ref(false);
  const error = ref(null);
  const insights = ref(null);
  
  function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }
  
  function getScoreColor(score) {
    if (score >= 4) return 'bg-green-500';
    if (score >= 3) return 'bg-yellow-500';
    return 'bg-red-500';
  }
  
  function getScoreTextColor(score) {
    if (score >= 4) return 'text-green-600';
    if (score >= 3) return 'text-yellow-600';
    return 'text-red-600';
  }
  
  async function fetchInsights() {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await axios.get(`/president/evaluations/${props.evaluationId}/ai-insights`);
      insights.value = response.data;
    } catch (err) {
      console.error('Failed to fetch AI insights:', err);
      error.value = err.response?.data?.message || 'Failed to load AI insights';
    } finally {
      loading.value = false;
    }
  }
  
  onMounted(() => {
    fetchInsights();
  });
  </script>