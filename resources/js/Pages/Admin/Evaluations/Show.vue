<template>
  <AdminLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center gap-4 mb-4">
            <Link 
              href="/admin/evaluations" 
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
                {{ evaluation.event.event_name }} | {{ evaluation.form_number }} | {{ getFormTypeFullName(evaluation.form_type) }}
              </p>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-wrap gap-3">
            <Link
              v-if="evaluation.status === 'draft'"
              :href="`/admin/evaluations/${evaluation.id}/edit`"
              class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Edit Form
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
              :href="`/admin/evaluations/${evaluation.id}/qr`"
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
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

        <!-- Request Information Card -->
        <div v-if="evaluation.customizations" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl shadow-lg overflow-hidden mb-8">
          <div class="px-6 py-4 border-b border-blue-100 bg-white/50">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
              <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Event Information
            </h2>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div>
                <p class="text-sm text-gray-500">Title of Event</p>
                <p class="font-medium text-gray-800">{{ evaluation.customizations.original_title || evaluation.event.event_name }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Inclusive Date</p>
                <p class="font-medium text-gray-800">{{ formatDate(evaluation.customizations.activity_date) }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Venue</p>
                <p class="font-medium text-gray-800">{{ evaluation.customizations.venue || 'Not specified' }}</p>
              </div>
              <div v-if="evaluation.customizations.speaker_name">
                <p class="text-sm text-gray-500">Resource Speaker</p>
                <p class="font-medium text-gray-800">{{ evaluation.customizations.speaker_name }}</p>
              </div>
              <div v-if="evaluation.customizations.topics && evaluation.customizations.topics.length > 0">
                <p class="text-sm text-gray-500">Topics</p>
                <div class="flex flex-wrap gap-2 mt-1">
                  <span v-for="topic in evaluation.customizations.topics" :key="topic" 
                        class="px-2 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm">
                    {{ topic }}
                  </span>
                </div>
              </div>
              <div v-if="evaluation.customizations.has_food !== undefined">
                <p class="text-sm text-gray-500">Food Service</p>
                <p class="font-medium text-gray-800">{{ evaluation.customizations.has_food ? 'With Food' : 'No Food' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-sm text-gray-500 mb-1">Total Responses</p>
            <p class="text-3xl font-bold text-gray-800">{{ evaluation.total_responses_overall || evaluation.total_responses || 0 }}</p>
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

        <!-- Response Rate Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm text-gray-500">Overall Response Rate</p>
              <span :class="getRateTextClass(evaluation.overall_response_rate)" class="text-sm font-bold">
                {{ evaluation.overall_response_rate }}%
              </span>
            </div>
            <div class="flex items-baseline gap-2 mb-2">
              <p class="text-2xl font-bold text-gray-800">{{ evaluation.total_responses_overall || evaluation.total_responses }}</p>
              <p class="text-sm text-gray-500">/ {{ evaluation.total_expected_overall }} expected</p>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="h-full rounded-full transition-all" 
                   :class="getRateColorClass(evaluation.overall_response_rate)"
                   :style="{ width: evaluation.overall_response_rate + '%' }">
              </div>
            </div>
            <div class="mt-3 flex justify-between text-xs text-gray-500">
              <span>{{ evaluation.students_count }} Students × {{ evaluation.number_of_dates || 1 }} days = {{ evaluation.students_count * (evaluation.number_of_dates || 1) }}</span>
              <span>{{ evaluation.guests_count }} Guests</span>
            </div>
            <p class="text-xs text-gray-400 mt-2 text-center">
              Expected = Students × {{ evaluation.number_of_dates || 1 }} days + Guests
            </p>
          </div>

          <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-sm text-gray-500 mb-2">Current Status</p>
            <div class="flex items-center gap-3 mb-2">
              <div class="w-3 h-3 rounded-full" :class="{
                'bg-green-500': evaluation.status === 'active',
                'bg-yellow-500': evaluation.status === 'draft',
                'bg-blue-500': evaluation.status === 'closed'
              }"></div>
              <span class="text-lg font-semibold text-gray-800 capitalize">{{ evaluation.status }}</span>
            </div>
            <p class="text-sm text-gray-600">
              {{ getStatusMessage(evaluation) }}
            </p>
          </div>

          <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-sm text-gray-500 mb-2">Target Achievement</p>
            <div class="flex items-center gap-2 mb-2">
              <p class="text-2xl font-bold text-gray-800">{{ evaluation.overall_response_rate }}%</p>
              <p class="text-sm text-gray-500">of target</p>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="h-full rounded-full transition-all" 
                   :class="getTargetColorClass(evaluation.overall_response_rate)"
                   :style="{ width: Math.min(evaluation.overall_response_rate, 100) + '%' }">
              </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">
              {{ getTargetMessage(evaluation) }}
            </p>
          </div>
        </div>

        <!-- Per Date Stats Section -->
        <div v-if="perDateStats && perDateStats.length > 0" class="bg-white rounded-2xl shadow-lg p-6 mb-8">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Response Rate by Date
          </h3>
          
          <div class="space-y-4">
            <div v-for="dateStat in perDateStats" :key="dateStat.date" class="border-b border-gray-100 last:border-0 pb-4 last:pb-0">
              <div class="flex justify-between items-center mb-2">
                <div>
                  <span class="font-medium text-gray-800">{{ dateStat.formatted_date }}</span>
                  <span class="text-xs text-gray-500 ml-2">Day {{ dateStat.date_index }}</span>
                </div>
                <span :class="getRateTextClass(dateStat.response_rate)" class="text-sm font-bold">
                  {{ dateStat.response_rate }}%
                </span>
              </div>
              <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>{{ dateStat.responses }} responses</span>
                <span>{{ dateStat.expected }} expected = {{ dateStat.students }} students + {{ dateStat.guests }} guests</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-full rounded-full transition-all" 
                     :class="getRateColorClass(dateStat.response_rate)"
                     :style="{ width: dateStat.response_rate + '%' }">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Draft Mode Message -->
        <div v-if="evaluation.status === 'draft'" class="bg-yellow-50 border-l-4 border-yellow-500 rounded-xl p-6 text-center mb-8">
          <svg class="w-12 h-12 mx-auto text-yellow-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Draft Mode</h3>
          <p class="text-gray-600 mb-4">
            This evaluation is still in draft mode. Click "Activate & Generate QR" to make it available for students.
          </p>
        </div>

        <!-- Active Mode Message -->
        <div v-if="evaluation.status === 'active'" class="bg-green-50 border-l-4 border-green-500 rounded-xl p-4 mb-8">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-green-800">
              <p class="font-medium">Evaluation is Active!</p>
              <p>Students can submit responses via QR code. You can also use the bulk upload feature to import responses from CSV.</p>
              <p class="mt-1 text-xs">Current response count: <span class="font-bold">{{ evaluation.total_responses_overall || evaluation.total_responses }}</span> out of {{ evaluation.total_expected_overall }} expected ({{ evaluation.overall_response_rate }}%)</p>
            </div>
          </div>
        </div>

        <!-- Closed Mode Message -->
        <div v-if="evaluation.status === 'closed'" class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4 mb-8">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-blue-800">
              <p class="font-medium">Evaluation is Closed</p>
              <p>No more responses can be submitted. You can generate AI insights and view the results below.</p>
              <p class="mt-1 text-xs">Total responses collected: <span class="font-bold">{{ evaluation.total_responses_overall || evaluation.total_responses }}</span> out of {{ evaluation.total_expected_overall }} expected ({{ evaluation.overall_response_rate }}%)</p>
            </div>
          </div>
        </div>

        <!-- Bulk Upload Section -->
        <div v-if="evaluation.status === 'active'" class="mt-8 mb-8">
          <BulkUpload 
            :evaluation-id="evaluation.id" 
            @upload-complete="handleUploadComplete"
          />
        </div>

        <!-- Tabs -->
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
              <button @click="activeTab = 'rawdata'" 
                      :class="activeTab === 'rawdata' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'"
                      class="px-4 py-2 border-b-2 font-medium transition">
                Raw Data
              </button>
            </nav>
          </div>
          
          <div class="mt-6">
            <!-- Results View -->
            <div v-if="activeTab === 'results'">
              <div v-if="(evaluation.total_responses_overall || evaluation.total_responses) > 0" class="space-y-8">
                <div v-for="category in evaluation.categories" :key="category.id" 
                     class="bg-white rounded-2xl shadow-lg p-6">
                  <h2 class="text-xl font-bold text-gray-800 mb-6 pb-2 border-b border-gray-200">{{ category.name }}</h2>
                  
                  <div class="space-y-6">
                    <div v-for="question in category.questions" :key="question.id" class="space-y-2">
                      <p class="font-medium text-gray-700">{{ question.text }}</p>
                      
                      <div v-if="stats && stats[question.id]" class="space-y-2">
                        <div class="flex items-center gap-4 mb-2">
                          <span class="text-sm text-gray-600">Average Rating:</span>
                          <span class="text-2xl font-bold text-emerald-600">{{ stats[question.id].average }}</span>
                          <span class="text-sm text-gray-500">/ 5.0</span>
                          
                          <div class="flex-1 ml-4">
                            <div class="flex items-center gap-1">
                              <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full" 
                                     :style="{ width: (stats[question.id].average / 5 * 100) + '%' }">
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Rating Distribution Bars -->
                        <div class="mt-4 space-y-2">
                          <div v-for="rating in [5,4,3,2,1]" :key="rating" class="flex items-center gap-2">
                            <span class="w-12 text-sm font-medium text-gray-600">{{ rating }} Stars</span>
                            <div class="flex-1 h-8 bg-gray-100 rounded-lg overflow-hidden">
                              <div class="h-full flex items-center justify-end px-2 text-xs font-medium text-white"
                                   :class="{
                                     'bg-emerald-500': rating >= 4,
                                     'bg-yellow-500': rating === 3,
                                     'bg-orange-500': rating === 2,
                                     'bg-red-500': rating === 1
                                   }"
                                   :style="{ width: (stats[question.id].distribution[rating]?.percentage || 0) + '%' }">
                                <span v-if="(stats[question.id].distribution[rating]?.percentage || 0) > 15">
                                  {{ stats[question.id].distribution[rating]?.percentage || 0 }}%
                                </span>
                              </div>
                            </div>
                            <span class="w-24 text-sm text-gray-600">
                              {{ stats[question.id].distribution[rating]?.count || 0 }} responses
                              ({{ stats[question.id].distribution[rating]?.percentage || 0 }}%)
                            </span>
                          </div>
                        </div>
                        
                        <p class="text-xs text-gray-400 mt-2">Total responses for this question: {{ stats[question.id].total }}</p>
                      </div>
                      <div v-else class="text-gray-400 italic">No ratings for this question yet.</div>
                    </div>
                  </div>
                </div>

                <!-- Comments Section -->
                <div v-if="evaluation.comments && evaluation.comments.length > 0" class="bg-white rounded-2xl shadow-lg p-6">
                  <h2 class="text-xl font-bold text-gray-800 mb-6">Comments & Feedback</h2>
                  
                  <div v-for="comment in evaluation.comments" :key="comment.id" class="mb-6 last:mb-0">
                    <h3 class="font-medium text-gray-700 mb-3">{{ comment.text }}</h3>
                    <div v-if="comments && comments[comment.id] && comments[comment.id].responses && comments[comment.id].responses.length > 0" class="space-y-3">
                      <div v-for="(response, idx) in comments[comment.id].responses" :key="idx" 
                           class="p-4 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition">
                        <p class="text-gray-700">{{ response }}</p>
                      </div>
                    </div>
                    <p v-else class="text-gray-400 italic">No comments for this question yet.</p>
                  </div>
                </div>
              </div>

              <div v-else-if="(evaluation.total_responses_overall || evaluation.total_responses) === 0 && evaluation.status !== 'draft'" class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Responses Yet</h3>
                <p class="text-gray-500">Students haven't submitted any responses yet.</p>
                <p class="text-sm text-gray-400 mt-2">Use the QR code or bulk upload to collect responses.</p>
              </div>
              
              <div v-else-if="evaluation.status === 'draft'" class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Evaluation in Draft Mode</h3>
                <p class="text-gray-500">Activate this evaluation to start collecting responses.</p>
              </div>
            </div>
            
            <!-- AI Insights View -->
            <div v-if="activeTab === 'insights'">
              <AIInsights 
                :evaluation-id="evaluation.id" 
                :total-responses="evaluation.total_responses_overall || evaluation.total_responses"
                :stats="stats"
                :comments="comments"
                ref="aiInsightsComponent"
                @insights-loaded="handleInsightsLoaded"
              />
            </div>

            <!-- Raw Data View -->
            <div v-if="activeTab === 'rawdata'">
              <RawData 
                :evaluation-id="evaluation.id"
                :evaluation="evaluation"
                ref="rawDataComponent"
              />
            </div>
          </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
          <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showDeleteModal = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
              <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                  <h3 class="text-xl font-semibold text-white">Delete Evaluation</h3>
                </div>
                <div class="p-6">
                  <p class="text-gray-600 mb-4">Are you sure you want to delete this evaluation? This action cannot be undone.</p>
                  <p class="text-sm text-red-600 mb-4">This will also delete all responses and AI insights associated with this evaluation.</p>
                  <div class="flex justify-end gap-3">
                    <button @click="showDeleteModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                    <button @click="deleteEvaluation" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
               :class="toast.bgClass">
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
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BulkUpload from './BulkUpload.vue';
import AIInsights from './AIINsights.vue';
import RawData from './RawData.vue';
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
  aiInsights: {
    type: Object,
    default: null
  },
  perDateStats: {
    type: Array,
    default: () => []
  },
  canGenerateQR: {
    type: Boolean,
    default: false
  }
});

const activeTab = ref('results');
const qrProcessing = ref(false);
const showDeleteModal = ref(false);
const generatingInsights = ref(false);
const toast = ref({ show: false, message: '', type: 'success', bgClass: '' });
const aiInsightsComponent = ref(null);
const rawDataComponent = ref(null);

const totalQuestions = computed(() => {
  let count = 0;
  if (props.evaluation.categories) {
    props.evaluation.categories.forEach(cat => {
      count += cat.questions?.length || 0;
    });
  }
  return count;
});

function getFormTypeFullName(formType) {
  const types = {
    type1: '7 Quality Dimension (F-EEF-018a)',
    type2: '5 Quality Dimension (F-EEF-018d)',
    type3: '8 Quality Dimension (F-EEF-018e)',
    type4: '6 Quality Dimension without Meals (F-EEF-018b)',
    type5: '6 Quality Dimension without Speaker (F-EEF-018c)'
  };
  return types[formType] || formType;
}

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

function getTargetColorClass(rate) {
  if (rate >= 75) return 'bg-green-500';
  if (rate >= 50) return 'bg-yellow-500';
  return 'bg-orange-500';
}

function getStatusMessage(evaluation) {
  const totalExpected = evaluation.total_expected_overall;
  const responses = evaluation.total_responses_overall || evaluation.total_responses;
  const remaining = totalExpected - responses;
  
  if (evaluation.status === 'active') {
    return `Collecting responses. Target: ${totalExpected} participants (${evaluation.students_count} students × ${evaluation.number_of_dates || 1} days + ${evaluation.guests_count} guests).`;
  }
  if (evaluation.status === 'draft') {
    return 'Evaluation is being prepared. Activate to start collecting responses.';
  }
  return `Evaluation completed with ${responses} out of ${totalExpected} responses.`;
}

function getTargetMessage(evaluation) {
  const rate = evaluation.overall_response_rate;
  const totalExpected = evaluation.total_expected_overall;
  const responses = evaluation.total_responses_overall || evaluation.total_responses;
  const remaining = totalExpected - responses;
  
  if (rate >= 100) {
    return 'Target achieved! All expected participants have responded.';
  }
  return `${remaining} more ${remaining === 1 ? 'response' : 'responses'} needed to reach 100% target.`;
}

function showToast(message, type = 'success') {
  const colors = {
    success: 'border-green-500 bg-green-50 text-green-800',
    error: 'border-red-500 bg-red-50 text-red-800',
    info: 'border-blue-500 bg-blue-50 text-blue-800'
  };
  
  toast.value = { 
    show: true, 
    message, 
    type,
    bgClass: colors[type] || colors.success
  };
  
  setTimeout(() => toast.value.show = false, 3000);
}

function handleUploadComplete(data) {
  showToast('✅ CSV uploaded successfully! Responses count updated.', 'success');
  setTimeout(() => router.reload(), 2000);
}

function handleInsightsLoaded(insights) {
  console.log('AI Insights loaded:', insights);
}

async function activateAndGenerateQR() {
  if (!confirm('Activate this evaluation and generate QR code?')) return;
  
  qrProcessing.value = true;
  
  try {
    showToast('Activating evaluation...', 'info');
    
    const response = await axios.post(`/admin/evaluations/${props.evaluation.id}/activate`, {}, {
      timeout: 30000
    });
    
    if (response.data.success) {
      showToast('✅ Evaluation activated! Redirecting to QR code...', 'success');
      
      setTimeout(() => {
        window.location.href = `/admin/evaluations/${response.data.evaluation_id}/qr`;
      }, 500);
    } else {
      showToast(response.data.error || 'Failed to activate evaluation', 'error');
      qrProcessing.value = false;
    }
  } catch (error) {
    console.error('Activation error:', error);
    
    let errorMessage = 'Failed to activate evaluation';
    if (error.response?.data?.error) {
      errorMessage = error.response.data.error;
    } else if (error.code === 'ECONNABORTED') {
      errorMessage = 'Request timed out. Please try again.';
    }
    
    showToast(`❌ ${errorMessage}`, 'error');
    qrProcessing.value = false;
  }
}

async function closeEvaluation() {
  if (!confirm('Close this evaluation? This will allow AI insights to be generated.')) return;
  try {
    const response = await axios.post(`/admin/evaluations/${props.evaluation.id}/close`);
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
    const response = await axios.post(`/admin/evaluations/${props.evaluation.id}/reopen`);
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
    const response = await axios.post(`/admin/evaluations/${props.evaluation.id}/generate-insights`);
    if (response.data.success) {
      showToast('✅ AI insights generated successfully!', 'success');
      if (aiInsightsComponent.value) {
        await aiInsightsComponent.value.generateInsights();
      }
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
  try {
    const response = await axios.delete(`/admin/evaluations/${props.evaluation.id}`);
    if (response.data.success) {
      showToast('Evaluation deleted!', 'success');
      setTimeout(() => router.visit('/admin/evaluations'), 1000);
    }
  } catch (error) {
    showToast(error.response?.data?.error || 'Failed to delete', 'error');
  } finally {
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