<template>
  <OrganizationUserLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center gap-4 mb-4">
            <Link 
              href="/president/evaluations" 
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
                  {{ evaluation.status }}
                </span>
              </div>
              <p class="text-gray-500 mt-1 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                {{ evaluation.event.event_name }} | {{ evaluation.form_number }}
              </p>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-wrap gap-3">
            <Link
              v-if="evaluation.status === 'draft'"
              :href="`/president/evaluations/${evaluation.id}/edit`"
              class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Edit Questionnaire
            </Link>

            <button
              v-if="canGenerateQR"
              @click="activateAndGenerateQR"
              class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition flex items-center gap-2"
              :disabled="qrProcessing"
            >
              <svg v-if="qrProcessing" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
              <span>{{ qrProcessing ? 'Activating...' : 'Activate & Generate QR' }}</span>
            </button>

            <Link
              v-if="evaluation.status === 'active'"
              :href="`/president/evaluations/${evaluation.id}/qr`"
              class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
              </svg>
              View QR Code
            </Link>

            <button
              v-if="evaluation.status === 'active'"
              @click="closeEvaluation"
              class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
              </svg>
              Close Evaluation
            </button>

            <button
              v-if="evaluation.status === 'closed'"
              @click="reopenEvaluation"
              class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Reopen Evaluation
            </button>

            <!-- Generate Insights Button (only for closed evaluations) -->
            <button
              v-if="evaluation.status === 'closed'"
              @click="generateInsights"
              class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition flex items-center gap-2"
              :disabled="generatingInsights"
            >
              <svg v-if="generatingInsights" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
              <span>{{ generatingInsights ? 'Generating...' : 'Generate AI Insights' }}</span>
            </button>

            <button
              v-if="evaluation.status === 'draft'"
              @click="confirmDelete"
              class="px-4 py-2 border border-red-300 text-red-600 rounded-xl hover:bg-red-50 transition flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Delete
            </button>
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

        <!-- Bulk Upload Section -->
        <div class="mt-8">
          <BulkUpload 
            :evaluation-id="evaluation.id" 
            @upload-complete="handleUploadComplete"
          />
        </div>

        <!-- No Responses Yet -->
        <div v-if="evaluation.responses_count === 0 && evaluation.status !== 'draft'" class="bg-white rounded-2xl shadow-lg p-12 text-center mb-8">
          <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No Responses Yet</h3>
          <p class="text-gray-500">Students haven't submitted any responses yet.</p>
        </div>

        <!-- Draft Mode Message -->
        <div v-if="evaluation.status === 'draft'" class="bg-yellow-50 border-l-4 border-yellow-500 rounded-xl p-6 text-center mb-8">
          <svg class="w-12 h-12 mx-auto text-yellow-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Draft Mode</h3>
          <p class="text-gray-600 mb-4">
            This evaluation is still in draft mode. 
            {{ canGenerateQR ? 'The event is finished! Click "Activate & Generate QR" to make it available for students.' : 'QR code can only be generated after the event is marked as finished.' }}
          </p>
          <div v-if="!canGenerateQR" class="text-sm text-gray-500">
            Event status: <span class="font-medium">{{ evaluation.event.status }}</span>
          </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="mt-6">
          <div class="border-b border-gray-200">
            <nav class="flex gap-4">
              <button @click="activeTab = 'results'" 
                      :class="activeTab === 'results' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'"
                      class="px-4 py-2 border-b-2 font-medium transition">
                Results
              </button>
              <button @click="activeTab = 'insights'" 
                      :class="activeTab === 'insights' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'"
                      class="px-4 py-2 border-b-2 font-medium transition">
                AI Insights
              </button>
            </nav>
          </div>
          
          <div class="mt-6">
            <!-- AI Insights View -->
            <div v-if="activeTab === 'insights'">
              <div v-if="loading" class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Loading AI insights...</p>
              </div>

              <div v-else-if="error" class="bg-red-50 rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 text-red-800">
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <p class="font-medium">{{ error }}</p>
                </div>
              </div>

              <div v-else-if="evaluation.ai_insights" class="space-y-6">
                <!-- Executive Summary -->
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
                  <div class="flex items-center gap-3 mb-3">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <h2 class="text-xl font-bold">AI-Generated Event Insights</h2>
                  </div>
                  <p class="text-lg">{{ evaluation.ai_insights.summary }}</p>
                  <p class="text-sm text-purple-200 mt-2">
                    Analyzed: {{ formatDate(evaluation.ai_insights.analyzed_at) }}
                  </p>
                </div>

                <!-- Response Rate Card -->
                <div class="bg-white rounded-xl shadow-lg p-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm text-gray-500">Response Rate</p>
                      <p class="text-2xl font-bold" :class="evaluation.ai_insights?.response_rate >= 0.75 ? 'text-green-600' : 'text-yellow-600'">
                        {{ (evaluation.ai_insights?.response_rate * 100).toFixed(1) }}%
                      </p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-500">Respondents</p>
                      <p class="text-2xl font-bold text-gray-800">{{ evaluation.ai_insights?.total_respondents }}</p>
                    </div>
                    <div v-if="evaluation.ai_insights?.response_rate >= 0.75" class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                      ✓ Valid Analysis
                    </div>
                    <div v-else class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">
                      ⚠ Below 75% Threshold
                    </div>
                  </div>
                </div>

                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div class="bg-white rounded-xl shadow-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Predicted Satisfaction</p>
                    <div class="flex items-end gap-2">
                      <span class="text-3xl font-bold text-emerald-600">{{ evaluation.ai_insights.predicted_satisfaction }}</span>
                      <span class="text-gray-400">/5.0</span>
                    </div>
                  </div>
                  <div class="bg-white rounded-xl shadow-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Success Probability</p>
                    <div class="flex items-end gap-2">
                      <span class="text-3xl font-bold text-blue-600">{{ (evaluation.ai_insights.success_probability * 100).toFixed(0) }}%</span>
                    </div>
                  </div>
                  <div class="bg-white rounded-xl shadow-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Total Responses</p>
                    <div class="flex items-end gap-2">
                      <span class="text-3xl font-bold text-gray-800">{{ evaluation.responses_count }}</span>
                    </div>
                  </div>
                </div>

                <!-- Strengths & Weaknesses -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                      <li v-for="(strength, index) in evaluation.ai_insights.strengths" :key="index" 
                          class="flex items-start gap-2 text-green-700">
                        <span class="text-green-600">●</span>
                        <span>{{ strength }}</span>
                      </li>
                      <li v-if="!evaluation.ai_insights.strengths?.length" class="text-gray-400 italic">
                        No specific strengths identified
                      </li>
                    </ul>
                  </div>

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
                      <li v-for="(weakness, index) in evaluation.ai_insights.weaknesses" :key="index" 
                          class="flex items-start gap-2 text-yellow-700">
                        <span class="text-yellow-600">●</span>
                        <span>{{ weakness }}</span>
                      </li>
                      <li v-if="!evaluation.ai_insights.weaknesses?.length" class="text-gray-400 italic">
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
                    <li v-for="(rec, index) in evaluation.ai_insights.recommendations" :key="index" 
                        class="p-3 bg-blue-50 rounded-lg text-blue-800">
                      {{ rec }}
                    </li>
                    <li v-if="!evaluation.ai_insights.recommendations?.length" class="text-gray-400 italic">
                      No recommendations available
                    </li>
                  </ul>
                </div>

                <!-- Category Breakdown -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                  <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Category Performance</h3>
                  <div class="space-y-3">
                    <div v-for="(score, category) in evaluation.ai_insights.category_breakdown" :key="category"
                         class="flex items-center gap-3">
                      <span class="w-48 text-sm text-gray-600">{{ category }}</span>
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

              <div v-else class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No AI Insights Yet</h3>
                <p class="text-gray-500">Click "Generate AI Insights" to analyze the responses.</p>
              </div>
            </div>
            
            <!-- Results View -->
            <div v-if="activeTab === 'results'">
              <!-- Results by Category -->
              <div v-if="evaluation.responses_count > 0" class="space-y-8">
                <div v-for="category in evaluation.categories" :key="category.id" 
                     class="bg-white rounded-2xl shadow-lg p-6">
                  <h2 class="text-xl font-bold text-gray-800 mb-6 pb-2 border-b border-gray-200">{{ category.name }}</h2>
                  
                  <div class="space-y-6">
                    <div v-for="question in category.questions" :key="question.id" class="space-y-2">
                      <p class="font-medium text-gray-700">{{ question.text }}</p>
                      
                      <div v-if="stats && stats[question.id]" class="space-y-2">
                        <!-- Average Rating -->
                        <div class="flex items-center gap-4 mb-2">
                          <span class="text-sm text-gray-600">Average:</span>
                          <span class="text-2xl font-bold text-emerald-600">{{ stats[question.id].average }}</span>
                          <span class="text-sm text-gray-500">/ 5</span>
                        </div>

                        <!-- Distribution Bars -->
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
                        <p class="text-xs text-gray-400 mt-1">Total responses: {{ stats[question.id].total }}</p>
                      </div>
                      <div v-else class="text-gray-400 italic">
                        No ratings for this question yet.
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Comment Sections -->
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

              <!-- No Results Message -->
              <div v-else-if="evaluation.responses_count === 0 && evaluation.status !== 'draft'" class="text-center py-12">
                <p class="text-gray-500">No responses yet.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
          <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
              <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="showDeleteModal = false"></div>
              <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
                  <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                      <h3 class="text-xl font-semibold text-white">Delete Evaluation</h3>
                      <button @click="showDeleteModal = false" class="text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>
                  </div>
                  <div class="p-6">
                    <p class="text-gray-600 mb-4">Are you sure you want to delete this evaluation? This action cannot be undone.</p>
                    <div class="flex justify-end gap-3">
                      <button @click="showDeleteModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                      <button @click="deleteEvaluation" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" :disabled="deleteProcessing">
                        {{ deleteProcessing ? 'Deleting...' : 'Delete' }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </Transition>
        </Teleport>

        <!-- Toast Notification -->
        <Transition
          enter-active-class="transition duration-300 ease-out"
          enter-from-class="translate-x-full opacity-0"
          enter-to-class="translate-x-0 opacity-100"
          leave-active-class="transition duration-200 ease-in"
          leave-from-class="translate-x-0 opacity-100"
          leave-to-class="translate-x-full opacity-0"
        >
          <div v-if="toast.show" 
               class="fixed bottom-4 right-4 z-50 flex min-w-[320px] items-center gap-3 rounded-xl border-l-4 p-4 shadow-2xl backdrop-blur-sm"
               :class="toast.type === 'success' ? 'border-green-500 bg-green-50 text-green-800' : 'border-red-500 bg-red-50 text-red-800'">
            <span class="flex-1">{{ toast.message }}</span>
            <button @click="toast.show = false" class="text-gray-400 hover:text-gray-600">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </Transition>
      </div>
    </div>
  </OrganizationUserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
import BulkUpload from './BulkUpload.vue';
import axios from 'axios';

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
  canGenerateQR: {
    type: Boolean,
    default: false
  }
});

const activeTab = ref('results');
const qrProcessing = ref(false);
const showDeleteModal = ref(false);
const deleteProcessing = ref(false);
const loading = ref(false);
const error = ref(null);
const generatingInsights = ref(false);
const toast = ref({ show: false, message: '', type: 'success' });

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

function showToast(message, type = 'success') {
  toast.value = { show: true, message, type };
  setTimeout(() => toast.value.show = false, 5000);
}

function handleUploadComplete(data) {
  showToast('✅ CSV uploaded successfully! Refreshing...', 'success');
  setTimeout(() => router.reload(), 2000);
}

async function activateAndGenerateQR() {
  if (!confirm('Activate this evaluation and generate QR code?')) return;
  qrProcessing.value = true;
  try {
    const response = await axios.post(`/president/evaluations/${props.evaluation.id}/activate-qr`);
    if (response.data.success) {
      showToast('Evaluation activated!', 'success');
      setTimeout(() => router.visit(`/president/evaluations/${props.evaluation.id}/qr`), 1000);
    }
  } catch (error) {
    showToast(error.response?.data?.error || 'Failed to activate', 'error');
  } finally {
    qrProcessing.value = false;
  }
}

async function closeEvaluation() {
  if (!confirm('Close this evaluation? This will allow AI insights to be generated.')) return;
  try {
    const response = await axios.post(`/president/evaluations/${props.evaluation.id}/close`);
    if (response.data.success) {
      showToast('Evaluation closed!', 'success');
      setTimeout(() => router.reload(), 2000);
    }
  } catch (error) {
    showToast(error.response?.data?.error || 'Failed to close', 'error');
  }
}

async function reopenEvaluation() {
  if (!confirm('Reopen this evaluation? Students can submit again.')) return;
  try {
    const response = await axios.post(`/president/evaluations/${props.evaluation.id}/reopen`);
    if (response.data.success) {
      showToast('Evaluation reopened!', 'success');
      setTimeout(() => router.reload(), 1000);
    }
  } catch (error) {
    showToast(error.response?.data?.error || 'Failed to reopen', 'error');
  }
}

async function generateInsights() {
  if (!confirm('Generate AI insights based on current responses? This may take a moment.')) return;
  
  generatingInsights.value = true;
  try {
    const response = await axios.post(`/president/evaluations/${props.evaluation.id}/generate-insights`);
    if (response.data.success) {
      showToast('✅ AI insights generated successfully!', 'success');
      setTimeout(() => router.reload(), 1500);
    } else {
      showToast(response.data.error || 'Failed to generate insights', 'error');
    }
  } catch (error) {
    const errorMessage = error.response?.data?.error || error.message || 'Unknown error';
    showToast(`❌ Error: ${errorMessage}`, 'error');
  } finally {
    generatingInsights.value = false;
  }
}

function confirmDelete() {
  showDeleteModal.value = true;
}

async function deleteEvaluation() {
  deleteProcessing.value = true;
  try {
    const response = await axios.delete(`/president/evaluations/${props.evaluation.id}`);
    if (response.data.success) {
      showToast('Evaluation deleted!', 'success');
      setTimeout(() => router.visit('/president/evaluations'), 1000);
    }
  } catch (error) {
    showToast(error.response?.data?.error || 'Failed to delete', 'error');
  } finally {
    deleteProcessing.value = false;
    showDeleteModal.value = false;
  }
}
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