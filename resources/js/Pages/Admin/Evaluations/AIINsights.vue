<template>
    <div class="space-y-6">
      <!-- Loading State -->
      <div v-if="loading" class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
        <p class="text-gray-600">AI is analyzing your evaluation data...</p>
        <p class="text-sm text-gray-500 mt-2">Using Logistic Regression with NLP sentiment analysis</p>
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
      <div v-else-if="insights" class="space-y-6">
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
  
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-sm text-gray-500 mb-1">Predicted Satisfaction</p>
            <div class="flex items-end gap-2">
              <span class="text-3xl font-bold" :class="getSatisfactionColor(insights.predicted_satisfaction)">
                {{ insights.predicted_satisfaction }}
              </span>
              <span class="text-gray-400">/5.0</span>
            </div>
          </div>
          <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-sm text-gray-500 mb-1">Success Probability</p>
            <span class="text-3xl font-bold text-blue-600">{{ (insights.success_probability * 100).toFixed(0) }}%</span>
          </div>
          <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-sm text-gray-500 mb-1">Response Rate</p>
            <span class="text-3xl font-bold" :class="getResponseRateColor(insights.response_rate)">
              {{ (insights.response_rate * 100).toFixed(1) }}%
            </span>
          </div>
        </div>
  
        <!-- Category Performance -->
        <div v-if="insights.category_breakdown" class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Category Performance</h3>
          
          <div class="space-y-3">
            <div v-for="(score, category) in insights.category_breakdown" :key="category"
                 class="flex items-center gap-3">
              <span class="w-48 text-sm text-gray-600">{{ category }}</span>
              <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all"
                     :class="getScoreBarColor(score)"
                     :style="{ width: (score / 5 * 100) + '%' }">
                </div>
              </div>
              <span class="text-sm font-medium" :class="getScoreColor(score)">
                {{ score.toFixed(1) }}
              </span>
            </div>
          </div>
        </div>
  
        <!-- Feature Importance -->
        <div v-if="insights.feature_importance" class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">📈 Feature Importance (What Drives Satisfaction)</h3>
          <p class="text-sm text-gray-500 mb-4">Based on Logistic Regression coefficients - higher values indicate stronger impact</p>
          
          <div class="space-y-3">
            <div v-for="(importance, feature) in insights.feature_importance" :key="feature"
                 class="flex items-center gap-3">
              <span class="w-48 text-sm text-gray-600">{{ formatFeatureName(feature) }}</span>
              <div class="flex-1 h-3 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full rounded-full bg-purple-600" :style="{ width: (importance * 100) + '%' }"></div>
              </div>
              <span class="text-sm font-medium text-purple-600 w-16">{{ (importance * 100).toFixed(1) }}%</span>
            </div>
          </div>
        </div>
  
        <!-- Strengths Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </span>
            ✅ Event Strengths
          </h3>
          
          <div class="space-y-3">
            <div v-for="(strength, index) in insights.strengths" :key="index"
                 class="p-3 bg-green-50 rounded-lg">
              <p class="text-green-800">{{ strength }}</p>
            </div>
            <p v-if="!insights.strengths?.length" class="text-gray-400 italic">No strengths identified</p>
          </div>
        </div>
  
        <!-- Areas for Improvement -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-yellow-500">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </span>
            ⚠️ Areas for Improvement
          </h3>
          
          <div class="space-y-3">
            <div v-for="(weakness, index) in insights.weaknesses" :key="index"
                 class="p-3 bg-yellow-50 rounded-lg">
              <p class="text-yellow-800">{{ weakness }}</p>
            </div>
            <p v-if="!insights.weaknesses?.length" class="text-gray-400 italic">No weaknesses identified</p>
          </div>
        </div>
  
        <!-- AI Recommendations -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
              </svg>
            </span>
            💡 AI Recommendations
          </h3>
          
          <div class="space-y-4">
            <div v-for="(rec, index) in insights.recommendations" :key="index"
                 class="rounded-lg overflow-hidden border transition-all hover:shadow-md"
                 :class="getRecommendationBorderClass(rec.priority)">
              
              <div class="px-4 py-2 flex items-center justify-between"
                   :class="getRecommendationHeaderClass(rec.priority)">
                <div class="flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full animate-pulse" :class="getPriorityDotClass(rec.priority)"></span>
                  <span class="text-sm font-semibold uppercase" :class="getPriorityTextClass(rec.priority)">
                    {{ rec.priority }} Priority
                  </span>
                </div>
                <span class="text-xs text-gray-600 bg-white bg-opacity-50 px-2 py-0.5 rounded-full">
                  {{ rec.category }}
                </span>
              </div>
              
              <div class="p-5">
                <h4 class="text-lg font-bold text-gray-800 mb-2">{{ rec.title }}</h4>
                <p class="text-sm text-gray-600 mb-4">{{ rec.problem_statement }}</p>
                
                <div class="mb-4">
                  <p class="text-sm font-semibold text-gray-700 mb-2">📋 Action Items:</p>
                  <ul class="space-y-2">
                    <li v-for="(item, idx) in rec.action_items" :key="idx" 
                        class="text-sm text-gray-700 flex items-start gap-2">
                      <span class="text-green-500 mt-0.5">✓</span>
                      <span>{{ item }}</span>
                    </li>
                  </ul>
                </div>
                
                <div class="mb-3 p-3 bg-blue-50 rounded-lg">
                  <p class="text-sm text-blue-800">
                    <span class="font-semibold">Expected Outcome:</span> {{ rec.expected_outcome }}
                  </p>
                </div>
                
                <div class="mt-3">
                  <p class="text-xs font-medium text-gray-500 mb-2">Resources Needed:</p>
                  <div class="flex flex-wrap gap-2">
                    <span v-for="(resource, idx) in rec.resources_needed" :key="idx"
                          class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">
                      {{ resource }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Sentiment Analysis -->
        <div v-if="insights.sentiment_analysis && insights.sentiment_analysis.total_comments > 0" 
             class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
            </span>
            💬 Sentiment Analysis ({{ insights.sentiment_analysis.total_comments }} comments)
          </h3>
          
          <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="text-center p-3 bg-green-50 rounded-lg">
              <p class="text-2xl font-bold text-green-600">{{ insights.sentiment_analysis.positive_percentage }}%</p>
              <p class="text-sm text-gray-600">Positive</p>
            </div>
            <div class="text-center p-3 bg-yellow-50 rounded-lg">
              <p class="text-2xl font-bold text-yellow-600">{{ insights.sentiment_analysis.neutral_percentage }}%</p>
              <p class="text-sm text-gray-600">Neutral</p>
            </div>
            <div class="text-center p-3 bg-red-50 rounded-lg">
              <p class="text-2xl font-bold text-red-600">{{ insights.sentiment_analysis.negative_percentage }}%</p>
              <p class="text-sm text-gray-600">Negative</p>
            </div>
          </div>
  
          <div v-if="insights.sentiment_analysis.common_themes?.length > 0" class="mt-4">
            <p class="text-sm font-medium text-gray-700 mb-2">Common themes mentioned:</p>
            <div class="flex flex-wrap gap-2">
              <span v-for="(theme, idx) in insights.sentiment_analysis.common_themes" :key="idx"
                    class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                {{ theme }}
              </span>
            </div>
          </div>
        </div>
  
        <!-- What-If Analysis -->
        <div v-if="insights.what_if_analysis" class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">🔮 What-If Analysis</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="(scenario, key) in insights.what_if_analysis" :key="key"
                 class="p-4 bg-gradient-to-br from-blue-50 to-white rounded-lg border border-blue-200">
              <h4 class="font-bold text-blue-800 mb-2">{{ key === 'targeted' ? '🎯 Targeted Improvements' : '🚀 Optimistic Scenario' }}</h4>
              <p class="text-sm text-gray-600 mb-3">{{ scenario.scenario }}</p>
              
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-gray-500">Current</span>
                <span class="text-xs text-gray-500">Projected</span>
              </div>
              <div class="flex items-center justify-between mb-3">
                <span class="text-xl font-bold text-gray-400">{{ scenario.current_satisfaction }}</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
                <span class="text-xl font-bold text-blue-600">{{ scenario.projected_satisfaction }}</span>
              </div>
              
              <div class="bg-blue-100 rounded-lg p-2 text-center">
                <span class="text-sm font-semibold text-blue-700">Potential Gain: +{{ scenario.gain }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- No Data State -->
      <div v-else class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No AI Insights Available</h3>
        <p class="text-gray-500">Click "Generate AI Insights" to analyze the responses.</p>
        <p class="text-sm text-gray-400 mt-2">AI analysis requires at least 75% response rate or can be forced.</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch, onMounted } from 'vue';
  import axios from 'axios';
  
  const props = defineProps({
    evaluationId: {
      type: Number,
      required: true
    },
    totalResponses: {
      type: Number,
      default: 0
    },
    refreshTrigger: {
      type: Number,
      default: 0
    }
  });
  
  const emit = defineEmits(['insights-loaded']);
  
  const loading = ref(false);
  const error = ref(null);
  const insights = ref(null);
  
  // Helper functions
  function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleString('en-US', {
      month: 'long',
      day: 'numeric',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }
  
  function formatFeatureName(feature) {
    const names = {
      info_timeliness: 'Information Timeliness',
      info_adequacy: 'Information Adequacy',
      design_program: 'Program Design',
      design_relevance: 'Content Relevance',
      design_pacing: 'Event Pacing',
      outcomes_attendance: 'Attendance',
      outcomes_participation: 'Participation',
      outcomes_interaction: 'Interaction',
      outcomes_teamwork: 'Teamwork',
      secretariat_sensitivity: 'Secretariat Sensitivity',
      secretariat_management: 'Secretariat Management',
      secretariat_communication: 'Communication',
      facilities_appearance: 'Facilities Appearance',
      facilities_cleanliness: 'Cleanliness',
      facilities_equipment: 'Equipment Quality',
      food_quality: 'Food Quality',
      food_presentation: 'Food Presentation',
      food_timeliness: 'Food Timeliness',
      food_service: 'Food Service',
      food_sufficiency: 'Food Sufficiency',
      food_quantity: 'Food Quantity'
    };
    return names[feature] || feature.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
  }
  
  function getSatisfactionColor(score) {
    if (score >= 4) return 'text-green-600';
    if (score >= 3) return 'text-yellow-600';
    return 'text-red-600';
  }
  
  function getResponseRateColor(rate) {
    return rate >= 0.75 ? 'text-green-600' : 'text-yellow-600';
  }
  
  function getScoreColor(score) {
    if (score >= 4) return 'text-green-600';
    if (score >= 3) return 'text-yellow-600';
    return 'text-red-600';
  }
  
  function getScoreBarColor(score) {
    if (score >= 4) return 'bg-green-500';
    if (score >= 3) return 'bg-yellow-500';
    return 'bg-red-500';
  }
  
  function getRecommendationBorderClass(priority) {
    const priorityLower = priority ? priority.toString().toLowerCase() : 'medium';
    const classes = {
      'high': 'border-red-200',
      'medium': 'border-yellow-200',
      'low': 'border-blue-200'
    };
    return classes[priorityLower] || 'border-gray-200';
  }
  
  function getRecommendationHeaderClass(priority) {
    const priorityLower = priority ? priority.toString().toLowerCase() : 'medium';
    const classes = {
      'high': 'bg-red-100',
      'medium': 'bg-yellow-100',
      'low': 'bg-blue-100'
    };
    return classes[priorityLower] || 'bg-gray-100';
  }
  
  function getPriorityDotClass(priority) {
    const priorityLower = priority ? priority.toString().toLowerCase() : 'medium';
    const classes = {
      'high': 'bg-red-500',
      'medium': 'bg-yellow-500',
      'low': 'bg-blue-500'
    };
    return classes[priorityLower] || 'bg-gray-500';
  }
  
  function getPriorityTextClass(priority) {
    const priorityLower = priority ? priority.toString().toLowerCase() : 'medium';
    const classes = {
      'high': 'text-red-700',
      'medium': 'text-yellow-700',
      'low': 'text-blue-700'
    };
    return classes[priorityLower] || 'text-gray-700';
  }
  
  // Fetch insights from API
  async function fetchInsights() {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await axios.get(`/admin/evaluations/${props.evaluationId}/ai-insights`);
      insights.value = response.data;
      emit('insights-loaded', response.data);
    } catch (err) {
      console.error('Failed to fetch AI insights:', err);
      if (err.response?.status === 404) {
        insights.value = null;
      } else {
        error.value = err.response?.data?.message || 'Failed to load AI insights';
      }
    } finally {
      loading.value = false;
    }
  }
  
  // Generate new insights
  async function generateInsights(evaluationData) {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await axios.post('/analyze', evaluationData);
      insights.value = response.data;
      emit('insights-loaded', response.data);
      return response.data;
    } catch (err) {
      console.error('Failed to generate AI insights:', err);
      error.value = err.response?.data?.message || 'Failed to generate insights';
      throw err;
    } finally {
      loading.value = false;
    }
  }
  
  // Watch for changes
  watch(() => [props.evaluationId, props.refreshTrigger], () => {
    if (props.evaluationId) {
      fetchInsights();
    }
  }, { immediate: true });
  
  // Initial fetch
  onMounted(() => {
    if (props.evaluationId) {
      fetchInsights();
    }
  });
  
  // Expose methods to parent component
  defineExpose({
    generateInsights,
    fetchInsights
  });
  </script>
  
  <style scoped>
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  .animate-spin {
    animation: spin 1s linear infinite;
  }
  
  @keyframes pulse-slow {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
  }
  
  .animate-pulse-slow {
    animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }
  </style>