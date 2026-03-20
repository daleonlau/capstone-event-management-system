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
            </nav>
          </div>
          
          <div class="mt-6">
            <!-- Results View -->
            <div v-if="activeTab === 'results'">
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
            
            <!-- AI Insights View -->
            <div v-if="activeTab === 'insights'">
              <div v-if="aiInsights" class="space-y-6">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
                  <div class="flex items-center gap-3 mb-3">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <h2 class="text-xl font-bold">AI-Generated Event Insights</h2>
                  </div>
                  <p class="text-lg">{{ aiInsights.summary }}</p>
                  <p class="text-sm text-purple-200 mt-2">
                    Analyzed: {{ formatDate(aiInsights.analyzed_at) }}
                  </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div class="bg-white rounded-xl shadow-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Predicted Satisfaction</p>
                    <div class="flex items-end gap-2">
                      <span class="text-3xl font-bold text-emerald-600">{{ aiInsights.predicted_satisfaction }}</span>
                      <span class="text-gray-400">/5.0</span>
                    </div>
                  </div>
                  <div class="bg-white rounded-xl shadow-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Success Probability</p>
                    <div class="flex items-end gap-2">
                      <span class="text-3xl font-bold text-blue-600">{{ (aiInsights.success_probability * 100).toFixed(0) }}%</span>
                    </div>
                  </div>
                  <div class="bg-white rounded-xl shadow-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Total Responses</p>
                    <div class="flex items-end gap-2">
                      <span class="text-3xl font-bold text-gray-800">{{ evaluation.responses_count }}</span>
                    </div>
                  </div>
                </div>

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
                      <li v-for="(strength, index) in aiInsights.strengths" :key="index" 
                          class="flex items-start gap-2 text-green-700">
                        <span class="text-green-600">●</span>
                        <span>{{ strength }}</span>
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
                      <li v-for="(weakness, index) in aiInsights.weaknesses" :key="index" 
                          class="flex items-start gap-2 text-yellow-700">
                        <span class="text-yellow-600">●</span>
                        <span>{{ weakness }}</span>
                      </li>
                    </ul>
                  </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6">
                  <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                      <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                      </svg>
                    </span>
                    Recommendations
                  </h3>
                  <ul class="space-y-3">
                    <li v-for="(rec, index) in aiInsights.recommendations" :key="index" 
                        class="p-3 bg-blue-50 rounded-lg text-blue-800">
                      {{ rec }}
                    </li>
                  </ul>
                </div>
              </div>

              <div v-else class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No AI Insights Yet</h3>
                <p class="text-gray-500">AI insights will be generated after the evaluation is closed by QUAMS.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </OrganizationUserLayout>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import OrganizationUserLayout from '@/Layouts/OrganizationUserLayout.vue';
import QRCode from 'qrcode';

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

const activeTab = ref('results');
const qrCanvas = ref(null);
const copied = ref(false);
const evaluationUrl = ref('');
const qrLoading = ref(true);

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

onMounted(async () => {
  // Set evaluation URL - use the one from backend or construct it
  evaluationUrl.value = props.evaluation.qr_code_url || 
    `${window.location.origin}/evaluations/${props.evaluation.id}/form`;
  
  // Generate QR code if evaluation is active and we have the canvas
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
      qrLoading.value = false;
      console.log('QR Code generated for president view:', evaluationUrl.value);
    } catch (err) {
      console.error('Failed to generate QR code:', err);
      qrLoading.value = false;
    }
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