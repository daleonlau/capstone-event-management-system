<template>
    <div class="space-y-8">
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
      <div v-else-if="insights" class="space-y-8">
        <!-- Executive Dashboard Header -->
        <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 rounded-2xl shadow-xl p-6 text-white">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-2xl font-bold flex items-center gap-2">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                AI-Powered Decision Support System
              </h2>
              <p class="text-purple-200 mt-1">{{ insights.summary }}</p>
            </div>
            <div class="text-right">
              <p class="text-sm text-purple-200">Analyzed</p>
              <p class="text-sm font-mono">{{ formatDate(insights.analyzed_at) }}</p>
            </div>
          </div>
          
          <!-- KPI Cards -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
              <p class="text-sm text-purple-200">Overall Satisfaction</p>
              <p class="text-3xl font-bold">{{ insights.predicted_satisfaction }}</p>
              <div class="flex items-center gap-1 mt-1">
                <span class="text-xs text-purple-200">/5.0</span>
                <div class="flex-1 h-1 bg-purple-300/30 rounded-full ml-2">
                  <div class="h-full bg-yellow-400 rounded-full" :style="{ width: (insights.predicted_satisfaction / 5 * 100) + '%' }"></div>
                </div>
              </div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
              <p class="text-sm text-purple-200">Success Probability</p>
              <p class="text-3xl font-bold">{{ (insights.success_probability * 100).toFixed(0) }}%</p>
              <div class="mt-2 h-1 bg-purple-300/30 rounded-full">
                <div class="h-full bg-green-400 rounded-full" :style="{ width: (insights.success_probability * 100) + '%' }"></div>
              </div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
              <p class="text-sm text-purple-200">Response Rate</p>
              <p class="text-3xl font-bold">{{ (insights.response_rate * 100).toFixed(1) }}%</p>
              <div class="mt-2 h-1 bg-purple-300/30 rounded-full">
                <div class="h-full bg-blue-400 rounded-full" :style="{ width: (insights.response_rate * 100) + '%' }"></div>
              </div>
              <p class="text-xs text-purple-200 mt-1">{{ insights.total_respondents }} respondents</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
              <p class="text-sm text-purple-200">Confidence Score</p>
              <p class="text-3xl font-bold">{{ getConfidenceScore() }}%</p>
              <div class="mt-2 h-1 bg-purple-300/30 rounded-full">
                <div class="h-full bg-emerald-400 rounded-full" :style="{ width: getConfidenceScore() + '%' }"></div>
              </div>
              <p class="text-xs text-purple-200 mt-1">Based on response rate</p>
            </div>
          </div>
        </div>
  
        <!-- Category Performance Grid with Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Category Performance Chart -->
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
              Category Performance Dashboard
            </h3>
            <div style="height: 300px;">
              <canvas ref="categoryChart"></canvas>
            </div>
          </div>
  
          <!-- Feature Importance Radar -->
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
              </svg>
              Impact Analysis (Feature Importance)
            </h3>
            <div style="height: 300px;">
              <canvas ref="importanceChart"></canvas>
            </div>
            <p class="text-xs text-gray-500 mt-2 text-center">Higher percentage = Greater impact on overall satisfaction</p>
          </div>
        </div>
  
        <!-- Strengths & Weaknesses Analysis -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Strengths -->
          <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </span>
                Strengths & Success Factors
              </h3>
              <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">{{ insights.strengths?.length || 0 }} factors</span>
            </div>
            <div class="space-y-3">
              <div v-for="(strength, index) in insights.strengths" :key="index"
                   class="p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                <p class="text-green-800">{{ strength }}</p>
              </div>
              <p v-if="!insights.strengths?.length" class="text-gray-400 italic text-center py-4">No strengths identified</p>
            </div>
          </div>
  
          <!-- Weaknesses -->
          <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <span class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </span>
                Areas for Improvement
              </h3>
              <span class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded-full">{{ insights.weaknesses?.length || 0 }} areas</span>
            </div>
            <div class="space-y-3">
              <div v-for="(weakness, index) in insights.weaknesses" :key="index"
                   class="p-3 bg-red-50 rounded-lg hover:bg-red-100 transition">
                <p class="text-red-800">{{ weakness }}</p>
              </div>
              <p v-if="!insights.weaknesses?.length" class="text-gray-400 italic text-center py-4">No areas for improvement identified</p>
            </div>
          </div>
        </div>
  
       
  
        <!-- Sentiment Analysis Dashboard with Pagination - Same as President/Adviser -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
            </span>
            Sentiment Analysis Dashboard
          </h3>
          
          <!-- Sentiment Stats with 3 cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-green-50 rounded-xl p-4 text-center border border-green-200">
              <p class="text-2xl font-bold text-green-600">{{ positivePercentage }}%</p>
              <p class="text-sm text-gray-600">Positive Comments</p>
              <p class="text-xs text-gray-500">{{ positiveCommentsList.length }} comments</p>
            </div>
            <div class="bg-yellow-50 rounded-xl p-4 text-center border border-yellow-200">
              <p class="text-2xl font-bold text-yellow-600">{{ neutralPercentage }}%</p>
              <p class="text-sm text-gray-600">Neutral Comments</p>
              <p class="text-xs text-gray-500">{{ neutralCommentsList.length }} comments</p>
            </div>
            <div class="bg-red-50 rounded-xl p-4 text-center border border-red-200">
              <p class="text-2xl font-bold text-red-600">{{ negativePercentage }}%</p>
              <p class="text-sm text-gray-600">Negative Comments</p>
              <p class="text-xs text-gray-500">{{ negativeCommentsList.length }} comments</p>
            </div>
          </div>

          <!-- Sentiment Gauge Bar -->
          <div class="mb-6">
            <div class="flex justify-between mb-2 text-xs">
              <span class="text-red-600">Negative</span>
              <span class="text-yellow-600">Neutral</span>
              <span class="text-green-600">Positive</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
              <div class="h-full flex">
                <div class="h-full bg-red-500" :style="{ width: negativePercentage + '%' }"></div>
                <div class="h-full bg-yellow-500" :style="{ width: neutralPercentage + '%' }"></div>
                <div class="h-full bg-green-500" :style="{ width: positivePercentage + '%' }"></div>
              </div>
            </div>
            <div class="flex justify-between mt-2 text-xs text-gray-500">
              <span>{{ negativePercentage.toFixed(1) }}%</span>
              <span>{{ neutralPercentage.toFixed(1) }}%</span>
              <span>{{ positivePercentage.toFixed(1) }}%</span>
            </div>
          </div>

          <!-- Positive Comments with Pagination -->
          <div v-if="positiveCommentsList.length > 0" class="mb-6">
            <h4 class="font-semibold text-green-700 mb-3 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              ✅ Positive Comments ({{ positiveCommentsList.length }})
            </h4>
            <div class="space-y-2">
              <div v-for="(comment, idx) in paginatedPositiveComments" :key="idx"
                   class="p-3 bg-green-50 rounded-lg border border-green-200">
                <p class="text-green-800">“{{ comment }}”</p>
              </div>
            </div>
            
            <!-- Pagination Controls for Positive Comments -->
            <div v-if="positiveTotalPages > 1" class="flex justify-center items-center gap-2 mt-4">
              <button 
                @click="positiveCurrentPage--"
                :disabled="positiveCurrentPage === 1"
                class="px-3 py-1 text-sm bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                Previous
              </button>
              <span class="text-sm text-gray-600">
                Page {{ positiveCurrentPage }} of {{ positiveTotalPages }}
              </span>
              <button 
                @click="positiveCurrentPage++"
                :disabled="positiveCurrentPage === positiveTotalPages"
                class="px-3 py-1 text-sm bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                Next
              </button>
            </div>
          </div>

          <!-- Negative Comments with Pagination -->
          <div v-if="negativeCommentsList.length > 0" class="mb-6">
            <h4 class="font-semibold text-red-700 mb-3 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              ❌ Negative Comments ({{ negativeCommentsList.length }})
            </h4>
            <div class="space-y-2">
              <div v-for="(comment, idx) in paginatedNegativeComments" :key="idx"
                   class="p-3 bg-red-50 rounded-lg border border-red-200">
                <p class="text-red-800">“{{ comment }}”</p>
              </div>
            </div>
            
            <!-- Pagination Controls for Negative Comments -->
            <div v-if="negativeTotalPages > 1" class="flex justify-center items-center gap-2 mt-4">
              <button 
                @click="negativeCurrentPage--"
                :disabled="negativeCurrentPage === 1"
                class="px-3 py-1 text-sm bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                Previous
              </button>
              <span class="text-sm text-gray-600">
                Page {{ negativeCurrentPage }} of {{ negativeTotalPages }}
              </span>
              <button 
                @click="negativeCurrentPage++"
                :disabled="negativeCurrentPage === negativeTotalPages"
                class="px-3 py-1 text-sm bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                Next
              </button>
            </div>
          </div>

          <!-- Neutral Comments with Pagination -->
          <div v-if="neutralCommentsList.length > 0">
            <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              😐 Neutral Comments ({{ neutralCommentsList.length }})
            </h4>
            <div class="space-y-2">
              <div v-for="(comment, idx) in paginatedNeutralComments" :key="idx"
                   class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-700">“{{ comment }}”</p>
              </div>
            </div>
            
            <!-- Pagination Controls for Neutral Comments -->
            <div v-if="neutralTotalPages > 1" class="flex justify-center items-center gap-2 mt-4">
              <button 
                @click="neutralCurrentPage--"
                :disabled="neutralCurrentPage === 1"
                class="px-3 py-1 text-sm bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                Previous
              </button>
              <span class="text-sm text-gray-600">
                Page {{ neutralCurrentPage }} of {{ neutralTotalPages }}
              </span>
              <button 
                @click="neutralCurrentPage++"
                :disabled="neutralCurrentPage === neutralTotalPages"
                class="px-3 py-1 text-sm bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                Next
              </button>
            </div>
          </div>

          <!-- Common Themes -->
          <div v-if="commonThemes.length > 0" class="mt-6 pt-4 border-t border-gray-200">
            <h4 class="font-semibold text-gray-700 mb-2">Common Themes</h4>
            <div class="flex flex-wrap gap-2">
              <span v-for="(theme, idx) in commonThemes" :key="idx"
                    class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm">
                {{ theme }}
              </span>
            </div>
          </div>
        </div>

         <!-- AI Recommendations - Decision Support -->
         <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
              <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
              </span>
              AI-Powered Recommendations & Action Plan
            </h3>
            <div class="flex gap-2">
              <button @click="exportRecommendations" class="text-sm text-blue-600 hover:text-blue-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
              </button>
            </div>
          </div>
          
          <div class="space-y-4">
            <div v-for="(rec, index) in insights.recommendations" :key="index"
                 class="rounded-xl overflow-hidden border transition-all hover:shadow-md"
                 :class="getRecommendationBorderClass(rec.priority)">
              
              <!-- Priority Header -->
              <div class="px-5 py-3 flex items-center justify-between"
                   :class="getRecommendationHeaderClass(rec.priority)">
                <div class="flex items-center gap-3">
                  <div class="w-2 h-2 rounded-full animate-pulse" :class="getPriorityDotClass(rec.priority)"></div>
                  <span class="text-sm font-semibold uppercase" :class="getPriorityTextClass(rec.priority)">
                    {{ rec.priority.toUpperCase() }} PRIORITY
                  </span>
                  <span class="text-xs bg-white/50 px-2 py-0.5 rounded-full">{{ rec.category }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <span class="text-xs text-gray-600">Impact Score</span>
                  <span class="text-sm font-bold" :class="getPriorityTextClass(rec.priority)">
                    {{ calculateImpactScore(rec) }}%
                  </span>
                </div>
              </div>
              
              <!-- Content -->
              <div class="p-5">
                <h4 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                  <span class="text-2xl">{{ getRecommendationIcon(rec.category) }}</span>
                  {{ rec.title }}
                </h4>
                
                <p class="text-sm text-gray-600 mb-4">{{ rec.problem_statement }}</p>
                
                <!-- Action Items with Progress -->
                <div class="mb-4">
                  <p class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Action Items
                  </p>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div v-for="(item, idx) in rec.action_items" :key="idx" 
                         class="flex items-start gap-2 p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                      <input type="checkbox" :id="`action-${index}-${idx}`" class="mt-1 w-4 h-4 text-emerald-600 rounded">
                      <label :for="`action-${index}-${idx}`" class="text-sm text-gray-700 cursor-pointer group-hover:text-emerald-600 transition">
                        {{ item }}
                      </label>
                    </div>
                  </div>
                </div>
                
                <!-- Expected Outcome with Progress Bar -->
                <div class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-blue-800">Expected Outcome</span>
                    <span class="text-sm font-bold text-blue-600">{{ calculateGainPercentage(rec) }}% improvement</span>
                  </div>
                  <p class="text-sm text-blue-800 mb-2">{{ rec.expected_outcome }}</p>
                  <div class="w-full bg-blue-200 rounded-full h-2">
                    <div class="bg-blue-600 rounded-full h-2 transition-all" :style="{ width: calculateGainPercentage(rec) + '%' }"></div>
                  </div>
                </div>
                
                <!-- Resources Needed & Success Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                  <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs font-medium text-gray-500 mb-2 flex items-center gap-1">
                      <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                      </svg>
                      Resources Needed
                    </p>
                    <div class="flex flex-wrap gap-1">
                      <span v-for="(resource, idx) in rec.resources_needed" :key="idx"
                            class="text-xs px-2 py-1 bg-white text-gray-600 rounded-full border">
                        {{ resource }}
                      </span>
                    </div>
                  </div>
                  <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs font-medium text-gray-500 mb-2 flex items-center gap-1">
                      <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      Success Metrics
                    </p>
                    <div class="flex flex-wrap gap-1">
                      <span v-for="(metric, idx) in rec.success_metrics" :key="idx"
                            class="text-xs px-2 py-1 bg-green-50 text-green-700 rounded-full">
                        {{ metric }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
  
        <!-- What-If Analysis - Decision Support -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
            </span>
            What-If Analysis & Scenario Planning
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Targeted Improvements -->
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-5 border border-blue-200">
              <div class="flex items-center gap-2 mb-3">
                <span class="text-2xl">🎯</span>
                <h4 class="font-bold text-blue-800">{{ insights.what_if_targeted?.scenario || 'Targeted Improvements' }}</h4>
              </div>
              <div class="flex items-center justify-between mb-3">
                <div>
                  <p class="text-xs text-gray-500">Current Satisfaction</p>
                  <p class="text-xl font-bold text-gray-700">{{ insights.what_if_targeted?.current_satisfaction || 0 }}</p>
                </div>
                <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
                <div>
                  <p class="text-xs text-gray-500">Projected Satisfaction</p>
                  <p class="text-xl font-bold text-blue-600">{{ insights.what_if_targeted?.projected_satisfaction || 0 }}</p>
                </div>
              </div>
              <div class="bg-white rounded-lg p-3 mb-3">
                <p class="text-sm font-medium text-blue-800">Potential Gain: +{{ insights.what_if_targeted?.gain || 0 }}</p>
                <div class="w-full bg-blue-200 rounded-full h-2 mt-2">
                  <div class="bg-blue-600 rounded-full h-2" :style="{ width: ((insights.what_if_targeted?.gain || 0) / 5 * 100) + '%' }"></div>
                </div>
              </div>
              <div class="space-y-2">
                <p class="text-xs font-medium text-gray-600">Focus Areas:</p>
                <div v-for="imp in insights.what_if_targeted?.improvements" :key="imp.category" 
                     class="flex items-center justify-between text-xs">
                  <span class="text-gray-600">{{ imp.category }}</span>
                  <span class="text-blue-600 font-medium">{{ imp.from }} → {{ imp.to }} (+{{ imp.gain }})</span>
                </div>
              </div>
            </div>
            
            <!-- Optimistic Scenario -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-200">
              <div class="flex items-center gap-2 mb-3">
                <span class="text-2xl">🚀</span>
                <h4 class="font-bold text-green-800">{{ insights.what_if_optimistic?.scenario || 'Optimistic Scenario' }}</h4>
              </div>
              <div class="flex items-center justify-between mb-3">
                <div>
                  <p class="text-xs text-gray-500">Current Satisfaction</p>
                  <p class="text-xl font-bold text-gray-700">{{ insights.what_if_optimistic?.current_satisfaction || 0 }}</p>
                </div>
                <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
                <div>
                  <p class="text-xs text-gray-500">Projected Satisfaction</p>
                  <p class="text-xl font-bold text-green-600">{{ insights.what_if_optimistic?.projected_satisfaction || 0 }}</p>
                </div>
              </div>
              <div class="bg-white rounded-lg p-3 mb-3">
                <p class="text-sm font-medium text-green-800">Potential Gain: +{{ insights.what_if_optimistic?.gain || 0 }}</p>
                <div class="w-full bg-green-200 rounded-full h-2 mt-2">
                  <div class="bg-green-600 rounded-full h-2" :style="{ width: ((insights.what_if_optimistic?.gain || 0) / 5 * 100) + '%' }"></div>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-2">If all categories are improved to 4.0</p>
            </div>
          </div>
        </div>
  
        <!-- Decision Summary Card -->
        <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-2xl shadow-xl p-6 text-white">
          <div class="flex items-center gap-3 mb-4">
            <svg class="w-8 h-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-xl font-bold">Executive Decision Summary</h3>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <p class="text-sm text-gray-400">Priority Actions</p>
              <p class="text-lg font-semibold">{{ getPriorityActionsCount() }} recommendations</p>
              <p class="text-xs text-gray-400 mt-1">{{ getHighPriorityCount() }} high priority</p>
            </div>
            <div>
              <p class="text-sm text-gray-400">Expected Improvement</p>
              <p class="text-lg font-semibold text-green-400">+{{ getTotalPotentialGain() }}</p>
              <p class="text-xs text-gray-400 mt-1">satisfaction points</p>
            </div>
            <div>
              <p class="text-sm text-gray-400">Recommendation Status</p>
              <p class="text-lg font-semibold">{{ insights.recommendations?.length || 0 }} actionable insights</p>
              <button @click="generateReport" class="mt-2 text-xs bg-white/20 hover:bg-white/30 px-3 py-1 rounded-full transition">
                Generate Full Report
              </button>
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
        <p class="text-gray-500">Click "Generate AI Insights" to analyze the responses and get actionable recommendations.</p>
        <p class="text-sm text-gray-400 mt-2">AI analysis requires at least 75% response rate or can be forced.</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch, onMounted, onUnmounted, computed } from 'vue';
  import axios from 'axios';
  import Chart from 'chart.js/auto';
  
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
    },
    stats: {
      type: Object,
      default: () => ({})
    },
    comments: {
      type: Object,
      default: () => ({})
    }
  });
  
  const emit = defineEmits(['insights-loaded']);
  
  const loading = ref(false);
  const error = ref(null);
  const insights = ref(null);
  
  // Chart refs
  const categoryChart = ref(null);
  const importanceChart = ref(null);
  let categoryChartInstance = null;
  let importanceChartInstance = null;
  
  // Pagination state for comments
  const COMMENTS_PER_PAGE = 10;
  
  const positiveCommentsList = ref([]);
  const negativeCommentsList = ref([]);
  const neutralCommentsList = ref([]);
  
  const positiveCurrentPage = ref(1);
  const negativeCurrentPage = ref(1);
  const neutralCurrentPage = ref(1);
  
  // Computed paginated comments
  const paginatedPositiveComments = computed(() => {
    const start = (positiveCurrentPage.value - 1) * COMMENTS_PER_PAGE;
    const end = start + COMMENTS_PER_PAGE;
    return positiveCommentsList.value.slice(start, end);
  });
  
  const paginatedNegativeComments = computed(() => {
    const start = (negativeCurrentPage.value - 1) * COMMENTS_PER_PAGE;
    const end = start + COMMENTS_PER_PAGE;
    return negativeCommentsList.value.slice(start, end);
  });
  
  const paginatedNeutralComments = computed(() => {
    const start = (neutralCurrentPage.value - 1) * COMMENTS_PER_PAGE;
    const end = start + COMMENTS_PER_PAGE;
    return neutralCommentsList.value.slice(start, end);
  });
  
  const positiveTotalPages = computed(() => Math.ceil(positiveCommentsList.value.length / COMMENTS_PER_PAGE));
  const negativeTotalPages = computed(() => Math.ceil(negativeCommentsList.value.length / COMMENTS_PER_PAGE));
  const neutralTotalPages = computed(() => Math.ceil(neutralCommentsList.value.length / COMMENTS_PER_PAGE));
  
  // Helper to safely get sentiment analysis data
  const getSentimentData = () => {
    if (!insights.value) return {};
    if (insights.value.sentiment_analysis) {
      return insights.value.sentiment_analysis;
    }
    return insights.value;
  };
  
  // Computed properties for sentiment data (using the ref lists for counts)
  const positivePercentage = computed(() => {
    const data = getSentimentData();
    return data.positive_percentage || 0;
  });
  
  const negativePercentage = computed(() => {
    const data = getSentimentData();
    return data.negative_percentage || 0;
  });
  
  const neutralPercentage = computed(() => {
    const data = getSentimentData();
    return data.neutral_percentage || (100 - positivePercentage.value - negativePercentage.value);
  });
  
  const commonThemes = computed(() => {
    const data = getSentimentData();
    return data.common_themes || [];
  });
  
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
  
  function getConfidenceScore() {
    const rate = insights.value?.response_rate || 0;
    if (rate >= 0.75) return 95;
    if (rate >= 0.5) return 75;
    if (rate >= 0.25) return 50;
    return 30;
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
  
  function getRecommendationIcon(category) {
    if (category.includes('Information')) return '📢';
    if (category.includes('Design')) return '🎯';
    if (category.includes('Outcomes')) return '🎉';
    if (category.includes('Secretariat')) return '👥';
    if (category.includes('Facilities')) return '🏛️';
    if (category.includes('Food')) return '🍽️';
    if (category.includes('Speaker')) return '🎤';
    if (category.includes('Traffic')) return '🚦';
    return '📈';
  }
  
  function calculateImpactScore(rec) {
    const priorityMap = { high: 85, medium: 60, low: 35 };
    return priorityMap[rec.priority?.toLowerCase()] || 50;
  }
  
  function calculateGainPercentage(rec) {
    const priorityMap = { high: 75, medium: 50, low: 25 };
    return priorityMap[rec.priority?.toLowerCase()] || 40;
  }
  
  function getPriorityActionsCount() {
    return insights.value?.recommendations?.length || 0;
  }
  
  function getHighPriorityCount() {
    return insights.value?.recommendations?.filter(r => r.priority?.toLowerCase() === 'high').length || 0;
  }
  
  function getTotalPotentialGain() {
    const targeted = insights.value?.what_if_targeted?.gain || 0;
    return targeted.toFixed(1);
  }
  
  function initCharts() {
    if (!insights.value?.category_breakdown) return;
    
    const categories = Object.keys(insights.value.category_breakdown);
    const scores = Object.values(insights.value.category_breakdown);
    const importance = Object.values(insights.value.feature_importance || {});
    
    if (categoryChartInstance) categoryChartInstance.destroy();
    if (importanceChartInstance) importanceChartInstance.destroy();
    
    if (categoryChart.value) {
      categoryChartInstance = new Chart(categoryChart.value, {
        type: 'bar',
        data: {
          labels: categories.map(c => c.replace(/^[IVX]+\.\s*/, '').substring(0, 25)),
          datasets: [{
            label: 'Score (out of 5)',
            data: scores,
            backgroundColor: scores.map(s => s >= 4 ? 'rgba(16, 185, 129, 0.8)' : 
                                       s >= 3 ? 'rgba(245, 158, 11, 0.8)' : 
                                       'rgba(239, 68, 68, 0.8)'),
            borderColor: scores.map(s => s >= 4 ? 'rgb(16, 185, 129)' : 
                                       s >= 3 ? 'rgb(245, 158, 11)' : 
                                       'rgb(239, 68, 68)'),
            borderWidth: 1,
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: (ctx) => `${ctx.raw}/5.0` } }
          },
          scales: {
            y: { beginAtZero: true, max: 5, title: { display: true, text: 'Score (1-5)' } },
            x: { ticks: { maxRotation: 45, minRotation: 45 } }
          }
        }
      });
    }
    
    if (importanceChart.value && importance.length > 0) {
      importanceChartInstance = new Chart(importanceChart.value, {
        type: 'radar',
        data: {
          labels: Object.keys(insights.value.feature_importance || {}).map(c => c.replace(/^[IVX]+\.\s*/, '').substring(0, 20)),
          datasets: [{
            label: 'Impact Weight (%)',
            data: importance,
            backgroundColor: 'rgba(99, 102, 241, 0.2)',
            borderColor: 'rgb(99, 102, 241)',
            pointBackgroundColor: 'rgb(99, 102, 241)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgb(99, 102, 241)',
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: { r: { beginAtZero: true, max: 30, ticks: { stepSize: 5 } } },
          plugins: { tooltip: { callbacks: { label: (ctx) => `${ctx.raw}% impact` } } }
        }
      });
    }
  }
  
  function exportRecommendations() {
    const data = insights.value?.recommendations || [];
    const csv = [
      ['Priority', 'Category', 'Title', 'Action Items', 'Expected Outcome', 'Resources Needed', 'Success Metrics'],
      ...data.map(r => [
        r.priority,
        r.category,
        r.title,
        r.action_items?.join('; '),
        r.expected_outcome,
        r.resources_needed?.join('; '),
        r.success_metrics?.join('; ')
      ])
    ].map(row => row.join(',')).join('\n');
    
    const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `recommendations_${props.evaluationId}.csv`;
    link.click();
    URL.revokeObjectURL(link.href);
  }
  
  function generateReport() {
    window.print();
  }
  
  async function fetchInsights() {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await axios.get(`/admin/evaluations/${props.evaluationId}/ai-insights`);
      insights.value = response.data;
      
      // Extract comments from sentiment analysis
      const sentimentData = response.data.sentiment_analysis || response.data;
      positiveCommentsList.value = sentimentData.positive_comments || [];
      negativeCommentsList.value = sentimentData.negative_comments || [];
      neutralCommentsList.value = sentimentData.neutral_comments || [];
      
      // Reset pagination
      positiveCurrentPage.value = 1;
      negativeCurrentPage.value = 1;
      neutralCurrentPage.value = 1;
      
      console.log('🔍 AI INSIGHTS DATA RECEIVED:', {
        has_sentiment_analysis: !!response.data.sentiment_analysis,
        positive_percentage: sentimentData.positive_percentage,
        negative_percentage: sentimentData.negative_percentage,
        neutral_percentage: sentimentData.neutral_percentage,
        positive_comments_count: positiveCommentsList.value.length,
        negative_comments_count: negativeCommentsList.value.length,
        neutral_comments_count: neutralCommentsList.value.length
      });
      
      emit('insights-loaded', response.data);
      
      await new Promise(resolve => setTimeout(resolve, 100));
      initCharts();
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
  
  watch(() => [props.evaluationId, props.refreshTrigger], () => {
    if (props.evaluationId) {
      fetchInsights();
    }
  }, { immediate: true });
  
  onMounted(() => {
    if (props.evaluationId) {
      fetchInsights();
    }
  });
  
  onUnmounted(() => {
    if (categoryChartInstance) categoryChartInstance.destroy();
    if (importanceChartInstance) importanceChartInstance.destroy();
  });
  
  defineExpose({ generateInsights: fetchInsights });
  </script>
  
  <style scoped>
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  .animate-spin {
    animation: spin 1s linear infinite;
  }
  </style>