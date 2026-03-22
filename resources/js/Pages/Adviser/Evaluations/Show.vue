<template>
  <OrganizationUserLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center gap-4 mb-4">
            <Link 
              :href="backUrl" 
              class="flex items-center justify-center w-10 h-10 bg-white rounded-xl shadow-md hover:shadow-lg transition-all hover:scale-105"
            >
              <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
            </Link>
            <div>
              <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                  {{ evaluation.title }}
                </h1>
                <span class="px-3 py-1 text-xs rounded-full" :class="{
                  'bg-gray-100 text-gray-700': evaluation.status === 'draft',
                  'bg-green-100 text-green-700': evaluation.status === 'active',
                  'bg-blue-100 text-blue-700': evaluation.status === 'closed'
                }">
                  {{ evaluation.status === 'closed' ? 'Completed' : evaluation.status }}
                </span>
              </div>
              <p class="text-gray-500 mt-1 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                {{ evaluation.event.event_name }}
              </p>
            </div>
          </div>

          <!-- QR Code Section (Only for active evaluations) -->
          <div v-if="evaluation.status === 'active'" class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row items-center gap-6">
              <div class="flex-shrink-0">
                <canvas ref="qrCanvas" class="bg-white p-4 rounded-2xl shadow-lg" width="200" height="200"></canvas>
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-800 mb-2 flex items-center gap-2">
                  <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                  </svg>
                  Evaluation QR Code
                </h3>
                <p class="text-sm text-gray-600 mb-3">
                  Share this QR code with students so they can access the evaluation form.
                </p>
                <div class="flex flex-wrap gap-3">
                  <button 
                    @click="copyLink"
                    class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition flex items-center gap-2"
                  >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                    </svg>
                    {{ copied ? 'Copied!' : 'Copy Link' }}
                  </button>
                  <button 
                    @click="downloadQRCode"
                    class="px-4 py-2 border border-purple-300 text-purple-700 rounded-xl hover:bg-purple-50 transition flex items-center gap-2"
                  >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download QR Code
                  </button>
                </div>
                <p class="text-xs text-gray-400 mt-3">
                  Link: <span class="font-mono">{{ evaluationUrl }}</span>
                </p>
              </div>
            </div>
          </div>

          <!-- Info Message for Draft -->
          <div v-if="evaluation.status === 'draft'" class="bg-yellow-50 border-l-4 border-yellow-500 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <div class="text-sm text-yellow-800">
                <p class="font-medium">Evaluation is being prepared</p>
                <p>QUAMS is currently creating your evaluation form. QR code will appear here once activated.</p>
              </div>
            </div>
          </div>

          <!-- Info Message for Closed -->
          <div v-if="evaluation.status === 'closed'" class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="text-sm text-blue-800">
                <p class="font-medium">Evaluation Completed</p>
                <p>The evaluation period has ended. View the results and AI insights below.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-sm text-gray-500 mb-1">Total Responses</p>
            <p class="text-3xl font-bold text-gray-800">{{ evaluation.responses_count }}</p>
          </div>
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-sm text-gray-500 mb-1">Categories</p>
            <p class="text-3xl font-bold text-gray-800">{{ evaluation.categories?.length || 0 }}</p>
          </div>
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-sm text-gray-500 mb-1">Questions</p>
            <p class="text-3xl font-bold text-gray-800">{{ totalQuestions }}</p>
          </div>
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-sm text-gray-500 mb-1">Created</p>
            <p class="text-lg font-bold text-gray-800">{{ evaluation.created_at }}</p>
          </div>
        </div>

        <!-- Combined Results & AI Insights View -->
        <div class="space-y-8">
          <!-- Category Performance Dashboard -->
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
              Category Performance Dashboard
            </h3>
            <div style="height: 350px;">
              <canvas ref="categoryChart"></canvas>
            </div>
          </div>

          <!-- AI Insights Section (if available) -->
          <div v-if="aiInsights" class="space-y-8">
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
                  <p class="text-purple-200 mt-1">{{ aiInsights.summary }}</p>
                </div>
                <div class="text-right">
                  <p class="text-sm text-purple-200">Analyzed</p>
                  <p class="text-sm font-mono">{{ formatDate(aiInsights.analyzed_at) }}</p>
                </div>
              </div>
              
              <!-- KPI Cards -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                  <p class="text-sm text-purple-200">Overall Satisfaction</p>
                  <p class="text-3xl font-bold">{{ aiInsights.predicted_satisfaction }}</p>
                  <div class="flex items-center gap-1 mt-1">
                    <span class="text-xs text-purple-200">/5.0</span>
                    <div class="flex-1 h-1 bg-purple-300/30 rounded-full ml-2">
                      <div class="h-full bg-yellow-400 rounded-full" :style="{ width: (aiInsights.predicted_satisfaction / 5 * 100) + '%' }"></div>
                    </div>
                  </div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                  <p class="text-sm text-purple-200">Success Probability</p>
                  <p class="text-3xl font-bold">{{ (aiInsights.success_probability * 100).toFixed(0) }}%</p>
                  <div class="mt-2 h-1 bg-purple-300/30 rounded-full">
                    <div class="h-full bg-green-400 rounded-full" :style="{ width: (aiInsights.success_probability * 100) + '%' }"></div>
                  </div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                  <p class="text-sm text-purple-200">Response Rate</p>
                  <p class="text-3xl font-bold">{{ aiInsights.response_rate ? (aiInsights.response_rate * 100).toFixed(1) : '0' }}%</p>
                  <div class="mt-2 h-1 bg-purple-300/30 rounded-full">
                    <div class="h-full bg-blue-400 rounded-full" :style="{ width: (aiInsights.response_rate ? aiInsights.response_rate * 100 : 0) + '%' }"></div>
                  </div>
                  <p class="text-xs text-purple-200 mt-1">{{ evaluation.responses_count }} respondents</p>
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
                  <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">{{ aiInsights.strengths?.length || 0 }} factors</span>
                </div>
                <div class="space-y-3">
                  <div v-for="(strength, index) in aiInsights.strengths" :key="index"
                       class="p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                    <p class="text-green-800">{{ strength }}</p>
                  </div>
                  <p v-if="!aiInsights.strengths?.length" class="text-gray-400 italic text-center py-4">No strengths identified</p>
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
                  <span class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded-full">{{ aiInsights.weaknesses?.length || 0 }} areas</span>
                </div>
                <div class="space-y-3">
                  <div v-for="(weakness, index) in aiInsights.weaknesses" :key="index"
                       class="p-3 bg-red-50 rounded-lg hover:bg-red-100 transition">
                    <p class="text-red-800">{{ weakness }}</p>
                  </div>
                  <p v-if="!aiInsights.weaknesses?.length" class="text-gray-400 italic text-center py-4">No areas for improvement identified</p>
                </div>
              </div>
            </div>

            <!-- Sentiment Analysis Dashboard -->
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
                  <p class="text-2xl font-bold text-green-600">{{ getSentimentData().positive_percentage || 0 }}%</p>
                  <p class="text-sm text-gray-600">Positive Comments</p>
                  <p class="text-xs text-gray-500">{{ getSentimentData().positive_comments?.length || 0 }} comments</p>
                </div>
                <div class="bg-yellow-50 rounded-xl p-4 text-center border border-yellow-200">
                  <p class="text-2xl font-bold text-yellow-600">{{ getSentimentData().neutral_percentage || 0 }}%</p>
                  <p class="text-sm text-gray-600">Neutral Comments</p>
                  <p class="text-xs text-gray-500">{{ getSentimentData().neutral_comments?.length || 0 }} comments</p>
                </div>
                <div class="bg-red-50 rounded-xl p-4 text-center border border-red-200">
                  <p class="text-2xl font-bold text-red-600">{{ getSentimentData().negative_percentage || 0 }}%</p>
                  <p class="text-sm text-gray-600">Negative Comments</p>
                  <p class="text-xs text-gray-500">{{ getSentimentData().negative_comments?.length || 0 }} comments</p>
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
                    <div class="h-full bg-red-500" :style="{ width: (getSentimentData().negative_percentage || 0) + '%' }"></div>
                    <div class="h-full bg-yellow-500" :style="{ width: (getSentimentData().neutral_percentage || 0) + '%' }"></div>
                    <div class="h-full bg-green-500" :style="{ width: (getSentimentData().positive_percentage || 0) + '%' }"></div>
                  </div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-500">
                  <span>{{ (getSentimentData().negative_percentage || 0).toFixed(1) }}%</span>
                  <span>{{ (getSentimentData().neutral_percentage || 0).toFixed(1) }}%</span>
                  <span>{{ (getSentimentData().positive_percentage || 0).toFixed(1) }}%</span>
                </div>
              </div>

              <!-- Positive Comments List -->
              <div v-if="getSentimentData().positive_comments?.length > 0" class="mb-6">
                <h4 class="font-semibold text-green-700 mb-3 flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  ✅ Positive Comments ({{ getSentimentData().positive_comments.length }})
                </h4>
                <div class="max-h-64 overflow-y-auto space-y-2">
                  <div v-for="(comment, idx) in getSentimentData().positive_comments.slice(0, 20)" :key="idx"
                       class="p-3 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-green-800">“{{ comment }}”</p>
                  </div>
                  <p v-if="getSentimentData().positive_comments.length > 20" class="text-xs text-gray-500 text-center">
                    +{{ getSentimentData().positive_comments.length - 20 }} more positive comments
                  </p>
                </div>
              </div>

              <!-- Negative Comments List -->
              <div v-if="getSentimentData().negative_comments?.length > 0" class="mb-6">
                <h4 class="font-semibold text-red-700 mb-3 flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  ❌ Negative Comments ({{ getSentimentData().negative_comments.length }})
                </h4>
                <div class="max-h-64 overflow-y-auto space-y-2">
                  <div v-for="(comment, idx) in getSentimentData().negative_comments.slice(0, 20)" :key="idx"
                       class="p-3 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-red-800">“{{ comment }}”</p>
                  </div>
                  <p v-if="getSentimentData().negative_comments.length > 20" class="text-xs text-gray-500 text-center">
                    +{{ getSentimentData().negative_comments.length - 20 }} more negative comments
                  </p>
                </div>
              </div>

              <!-- Neutral Comments List -->
              <div v-if="getSentimentData().neutral_comments?.length > 0">
                <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  😐 Neutral Comments ({{ getSentimentData().neutral_comments.length }})
                </h4>
                <div class="max-h-48 overflow-y-auto space-y-2">
                  <div v-for="(comment, idx) in getSentimentData().neutral_comments.slice(0, 10)" :key="idx"
                       class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-gray-700">“{{ comment }}”</p>
                  </div>
                  <p v-if="getSentimentData().neutral_comments.length > 10" class="text-xs text-gray-500 text-center">
                    +{{ getSentimentData().neutral_comments.length - 10 }} more neutral comments
                  </p>
                </div>
              </div>

              <!-- Common Themes -->
              <div v-if="getSentimentData().common_themes?.length > 0" class="mt-6 pt-4 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-2">Common Themes</h4>
                <div class="flex flex-wrap gap-2">
                  <span v-for="(theme, idx) in getSentimentData().common_themes" :key="idx"
                        class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm">
                    {{ theme }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Recommendations -->
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
              </div>
              
              <div class="space-y-4">
                <div v-for="(rec, index) in aiInsights.recommendations" :key="index"
                     class="rounded-xl overflow-hidden border transition-all hover:shadow-md"
                     :class="getRecommendationBorderClass(rec.priority)">
                  
                  <!-- Priority Header -->
                  <div class="px-5 py-3 flex items-center justify-between"
                       :class="getRecommendationHeaderClass(rec.priority)">
                    <div class="flex items-center gap-3">
                      <div class="w-2 h-2 rounded-full animate-pulse" :class="getPriorityDotClass(rec.priority)"></div>
                      <span class="text-sm font-semibold uppercase" :class="getPriorityTextClass(rec.priority)">
                        {{ (rec.priority || 'medium').toUpperCase() }} PRIORITY
                      </span>
                      <span class="text-xs bg-white/50 px-2 py-0.5 rounded-full">{{ rec.category }}</span>
                    </div>
                  </div>
                  
                  <!-- Content -->
                  <div class="p-5">
                    <h4 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                      <span class="text-2xl">{{ getRecommendationIcon(rec.category) }}</span>
                      {{ rec.title }}
                    </h4>
                    
                    <p class="text-sm text-gray-600 mb-4">{{ rec.problem_statement }}</p>
                    
                    <!-- Action Items -->
                    <div v-if="rec.action_items?.length" class="mb-4">
                      <p class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Action Items
                      </p>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div v-for="(item, idx) in rec.action_items" :key="idx" 
                             class="flex items-start gap-2 p-2 bg-gray-50 rounded-lg">
                          <input type="checkbox" class="mt-1 w-4 h-4 text-emerald-600 rounded">
                          <span class="text-sm text-gray-700">{{ item }}</span>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Expected Outcome -->
                    <div v-if="rec.expected_outcome" class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                      <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-blue-800">Expected Outcome</span>
                        <span class="text-sm font-bold text-blue-600">{{ calculateGainPercentage(rec.priority) }}% improvement</span>
                      </div>
                      <p class="text-sm text-blue-800">{{ rec.expected_outcome }}</p>
                      <div class="w-full bg-blue-200 rounded-full h-2 mt-2">
                        <div class="bg-blue-600 rounded-full h-2 transition-all" :style="{ width: calculateGainPercentage(rec.priority) + '%' }"></div>
                      </div>
                    </div>
                    
                    <!-- Resources & Metrics -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                      <div v-if="rec.resources_needed?.length" class="p-3 bg-gray-50 rounded-lg">
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
                      <div v-if="rec.success_metrics?.length" class="p-3 bg-gray-50 rounded-lg">
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

            <!-- What-If Analysis -->
            <div v-if="aiInsights.what_if_targeted || aiInsights.what_if_optimistic" class="bg-white rounded-2xl shadow-lg p-6">
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
                <div v-if="aiInsights.what_if_targeted" class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-5 border border-blue-200">
                  <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">🎯</span>
                    <h4 class="font-bold text-blue-800">{{ aiInsights.what_if_targeted.scenario || 'Targeted Improvements' }}</h4>
                  </div>
                  <div class="flex items-center justify-between mb-3">
                    <div>
                      <p class="text-xs text-gray-500">Current Satisfaction</p>
                      <p class="text-xl font-bold text-gray-700">{{ aiInsights.what_if_targeted.current_satisfaction || 0 }}</p>
                    </div>
                    <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    <div>
                      <p class="text-xs text-gray-500">Projected Satisfaction</p>
                      <p class="text-xl font-bold text-blue-600">{{ aiInsights.what_if_targeted.projected_satisfaction || 0 }}</p>
                    </div>
                  </div>
                  <div class="bg-white rounded-lg p-3 mb-3">
                    <p class="text-sm font-medium text-blue-800">Potential Gain: +{{ aiInsights.what_if_targeted.gain || 0 }}</p>
                    <div class="w-full bg-blue-200 rounded-full h-2 mt-2">
                      <div class="bg-blue-600 rounded-full h-2" :style="{ width: ((aiInsights.what_if_targeted.gain || 0) / 5 * 100) + '%' }"></div>
                    </div>
                  </div>
                </div>
                
                <!-- Optimistic Scenario -->
                <div v-if="aiInsights.what_if_optimistic" class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-200">
                  <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">🚀</span>
                    <h4 class="font-bold text-green-800">{{ aiInsights.what_if_optimistic.scenario || 'Optimistic Scenario' }}</h4>
                  </div>
                  <div class="flex items-center justify-between mb-3">
                    <div>
                      <p class="text-xs text-gray-500">Current Satisfaction</p>
                      <p class="text-xl font-bold text-gray-700">{{ aiInsights.what_if_optimistic.current_satisfaction || 0 }}</p>
                    </div>
                    <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    <div>
                      <p class="text-xs text-gray-500">Projected Satisfaction</p>
                      <p class="text-xl font-bold text-green-600">{{ aiInsights.what_if_optimistic.projected_satisfaction || 0 }}</p>
                    </div>
                  </div>
                  <div class="bg-white rounded-lg p-3 mb-3">
                    <p class="text-sm font-medium text-green-800">Potential Gain: +{{ aiInsights.what_if_optimistic.gain || 0 }}</p>
                    <div class="w-full bg-green-200 rounded-full h-2 mt-2">
                      <div class="bg-green-600 rounded-full h-2" :style="{ width: ((aiInsights.what_if_optimistic.gain || 0) / 5 * 100) + '%' }"></div>
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
                  <p class="text-lg font-semibold">{{ aiInsights.recommendations?.length || 0 }} recommendations</p>
                  <p class="text-xs text-gray-400 mt-1">{{ getHighPriorityCount() }} high priority</p>
                </div>
                <div>
                  <p class="text-sm text-gray-400">Expected Improvement</p>
                  <p class="text-lg font-semibold text-green-400">+{{ getTotalPotentialGain() }}</p>
                  <p class="text-xs text-gray-400 mt-1">satisfaction points</p>
                </div>
                <div>
                  <p class="text-sm text-gray-400">Analysis Status</p>
                  <p class="text-lg font-semibold">AI-Powered</p>
                  <p class="text-xs text-gray-400 mt-1">{{ formatDate(aiInsights.analyzed_at) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- No AI Insights Message -->
          <div v-else-if="evaluation.status === 'closed'" class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No AI Insights Yet</h3>
            <p class="text-gray-500">AI insights are being generated by QUAMS. Please check back later.</p>
          </div>

          <!-- Results Section (Only show when no AI insights or for draft/active) -->
          <div v-if="evaluation.status !== 'closed' || (!aiInsights && evaluation.status === 'closed')" class="space-y-8">
            <div v-if="evaluation.responses_count > 0" class="space-y-8">
              <div v-for="category in evaluation.categories" :key="category.id" 
                   class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6 pb-2 border-b border-gray-200">{{ category.name }}</h2>
                
                <div class="space-y-6">
                  <div v-for="question in category.questions" :key="question.id" class="space-y-2">
                    <p class="font-medium text-gray-700">{{ question.text }}</p>
                    
                    <div v-if="stats && stats[question.id]" class="space-y-2">
                      <div class="flex items-center gap-4 mb-2">
                        <span class="text-sm text-gray-600">Average:</span>
                        <span class="text-2xl font-bold text-emerald-600">{{ stats[question.id].average }}</span>
                        <span class="text-sm text-gray-500">/ 5</span>
                      </div>

                      <div v-for="rating in [5,4,3,2,1]" :key="rating" class="flex items-center gap-2">
                        <span class="w-8 text-sm text-gray-600">{{ rating }}★</span>
                        <div class="flex-1 h-6 bg-gray-100 rounded-lg overflow-hidden">
                          <div class="h-full bg-emerald-500 rounded-lg transition-all" 
                               :style="{ width: (stats[question.id].distribution[rating]?.percentage || 0) + '%' }">
                          </div>
                        </div>
                        <span class="w-20 text-sm text-gray-600">
                          {{ stats[question.id].distribution[rating]?.count || 0 }} 
                          ({{ stats[question.id].distribution[rating]?.percentage || 0 }}%)
                        </span>
                      </div>
                    </div>
                    <div v-else class="text-gray-400 italic">No ratings for this question yet.</div>
                  </div>
                </div>
              </div>

              <div v-if="evaluation.comments && evaluation.comments.length > 0" class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Comments & Feedback</h2>
                
                <div v-for="comment in evaluation.comments" :key="comment.id" class="mb-6 last:mb-0">
                  <h3 class="font-medium text-gray-700 mb-3">{{ comment.text }}</h3>
                  <div v-if="comments && comments[comment.id] && comments[comment.id].responses.length > 0" class="space-y-3">
                    <div v-for="(response, idx) in comments[comment.id].responses" :key="idx" 
                         class="p-4 bg-gray-50 rounded-lg">
                      <p class="text-gray-700">{{ response }}</p>
                    </div>
                  </div>
                  <p v-else class="text-gray-400 italic">No comments yet.</p>
                </div>
              </div>
            </div>

            <div v-else-if="evaluation.responses_count === 0 && evaluation.status !== 'draft'" class="bg-white rounded-2xl shadow-lg p-12 text-center">
              <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
              <h3 class="text-lg font-medium text-gray-900 mb-2">No Responses Yet</h3>
              <p class="text-gray-500">Share the QR code with students to start collecting feedback.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </OrganizationUserLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
import QRCode from 'qrcode';
import Chart from 'chart.js/auto';

const props = defineProps({
  evaluation: {
    type: Object,
    required: true
  },
  stats: {
    type: Object,
    default: () => ({})
  },
  comments: {
    type: Object,
    default: () => ({})
  },
  aiInsights: {
    type: Object,
    default: null
  }
});

const backUrl = computed(() => {
  const userRole = usePage().props.auth?.user?.role;
  if (userRole === 'adviser') {
    return '/adviser/evaluations';
  }
  return '/president/evaluations';
});

const qrCanvas = ref(null);
const copied = ref(false);
const evaluationUrl = ref('');
const categoryChart = ref(null);
let categoryChartInstance = null;

const totalQuestions = computed(() => {
  let count = 0;
  if (props.evaluation.categories) {
    props.evaluation.categories.forEach(cat => {
      count += cat.questions?.length || 0;
    });
  }
  return count;
});

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

function getSentimentData() {
  if (!props.aiInsights) return {};
  if (props.aiInsights.sentiment_analysis) {
    return props.aiInsights.sentiment_analysis;
  }
  return props.aiInsights;
}

function getConfidenceScore() {
  const rate = props.aiInsights?.response_rate || 0;
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
  if (!category) return '📈';
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

function calculateGainPercentage(priority) {
  const priorityMap = { high: 75, medium: 50, low: 25 };
  return priorityMap[priority?.toLowerCase()] || 40;
}

function getHighPriorityCount() {
  return props.aiInsights?.recommendations?.filter(r => r.priority?.toLowerCase() === 'high').length || 0;
}

function getTotalPotentialGain() {
  const targeted = props.aiInsights?.what_if_targeted?.gain || 0;
  return targeted.toFixed(1);
}

function initCategoryChart() {
  if (!props.aiInsights?.category_breakdown) return;
  
  const categories = Object.keys(props.aiInsights.category_breakdown);
  const scores = Object.values(props.aiInsights.category_breakdown);
  
  if (categoryChartInstance) categoryChartInstance.destroy();
  
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
}

async function copyLink() {
  try {
    await navigator.clipboard.writeText(evaluationUrl.value);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (err) {
    console.error('Failed to copy:', err);
  }
}

function downloadQRCode() {
  if (qrCanvas.value) {
    const link = document.createElement('a');
    link.download = `qrcode-${props.evaluation.id}.png`;
    link.href = qrCanvas.value.toDataURL('image/png');
    link.click();
  }
}

// Watch for aiInsights changes to update chart
watch(() => props.aiInsights, () => {
  if (props.aiInsights) {
    setTimeout(() => initCategoryChart(), 100);
  }
});

onMounted(async () => {
  evaluationUrl.value = props.evaluation.qr_code_url || 
    `${window.location.origin}/evaluations/${props.evaluation.id}/form`;
  
  if (props.evaluation.status === 'active' && evaluationUrl.value && qrCanvas.value) {
    try {
      await QRCode.toCanvas(qrCanvas.value, evaluationUrl.value, {
        width: 200,
        margin: 1,
        color: {
          dark: '#000000',
          light: '#ffffff'
        }
      });
    } catch (err) {
      console.error('Failed to generate QR code:', err);
    }
  }
  
  if (props.aiInsights) {
    setTimeout(() => initCategoryChart(), 100);
  }
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
</style>