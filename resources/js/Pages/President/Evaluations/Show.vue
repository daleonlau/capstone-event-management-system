<template>
  <OrganizationUserLayout>
    <div class="modern-dashboard">
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

        <!-- Date Selector for Insights -->
        <div v-if="availableDates.length > 0 || (aiInsights && !dateInsights.length)" class="date-selector">
          <div class="date-selector-header">
            <h3>Analysis View</h3>
          </div>
          <div class="date-tabs">
            <button
              v-if="aiInsights"
              @click="selectDate('overall')"
              :class="['date-tab', { active: currentDate === 'overall' }]"
            >
              <span class="date-icon">📊</span>
              <span class="date-label">Overall</span>
              <span class="date-count">{{ aiInsights?.total_respondents || 0 }}</span>
            </button>
            <button
              v-for="insight in dateInsights"
              :key="insight.event_date"
              @click="selectDate(insight.event_date)"
              :class="['date-tab', { active: currentDate === insight.event_date }]"
            >
              <span class="date-icon">📅</span>
              <span class="date-label">{{ formatDateOnly(insight.event_date) }}</span>
              <span class="date-count">{{ insight.total_respondents || 0 }}</span>
            </button>
          </div>
        </div>

        
        <!-- AI Insights Section -->
        <div v-if="currentInsight" class="space-y-6">
          
          <!-- Hero Score Card -->
          <div class="hero-card" :class="getHeroClass(currentInsight.predicted_satisfaction)">
            <div class="hero-bg"></div>
            <div class="hero-content">
              <div class="hero-left">
                <span class="hero-badge">{{ currentDate === 'overall' ? 'Overall Rating' : formatDateOnly(currentDate) }}</span>
                <div class="hero-score">
                  <span class="score-number">{{ currentInsight.predicted_satisfaction || 0 }}</span>
                  <span class="score-max">/5.0</span>
                </div>
                <div class="hero-rating">
                  <span class="rating-badge" :class="getRatingBadgeClass(currentInsight.predicted_satisfaction)">
                    {{ getInterpretationLabel(currentInsight.predicted_satisfaction) }}
                  </span>
                  <span class="rating-verb">{{ getVerbalInterpretation(currentInsight.predicted_satisfaction) }}</span>
                </div>
              </div>
              <div class="hero-stats">
                <div class="stat-card">
                  <div class="stat-icon">👥</div>
                  <div class="stat-info">
                    <div class="stat-value">{{ currentInsight.total_respondents || 0 }}</div>
                    <div class="stat-label">Responses</div>
                  </div>
                </div>
                <div class="stat-card">
                  <div class="stat-icon">📊</div>
                  <div class="stat-info">
                    <div class="stat-value">{{ ((currentInsight.response_rate || 0) * 100).toFixed(0) }}%</div>
                    <div class="stat-label">Response Rate</div>
                  </div>
                </div>
                <div class="stat-card">
                  <div class="stat-icon">🎯</div>
                  <div class="stat-info">
                    <div class="stat-value">{{ getSuccessRate(currentInsight.predicted_satisfaction) }}%</div>
                    <div class="stat-label">Success Rate</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="hero-progress">
              <div class="progress-bar-bg">
                <div class="progress-bar-fill" :style="{ width: ((currentInsight.predicted_satisfaction || 0) / 5 * 100) + '%' }"></div>
              </div>
              <div class="progress-labels">
                <span>Poor</span>
                <span>Satisfactory</span>
                <span>Very Satisfactory</span>
                <span>Outstanding</span>
              </div>
            </div>
          </div>
          <!-- Category Performance Chart -->
        <div class="section-card">
          <div class="section-header">
            <div class="section-title">
              <span class="title-icon">📈</span>
              <h3>Category Performance</h3>
            </div>
            <p class="section-desc">Detailed breakdown of each evaluation category</p>
          </div>
          <div class="category-list">
            <div v-for="(score, category) in displayCategoryBreakdown" :key="category" class="category-item">
              <div class="category-header">
                <span class="category-name">{{ simplifyCategoryName(category) }}</span>
                <div class="category-score">
                  <span class="score-value" :style="{ color: getScoreColor(score) }">{{ score }}</span>
                  <span class="score-max">/5.0</span>
                </div>
              </div>
              <div class="category-bar">
                <div class="bar-bg">
                  <div class="bar-fill" :style="{ width: (score / 5 * 100) + '%', backgroundColor: getScoreColor(score) }"></div>
                </div>
                <span class="bar-label">{{ getInterpretationLabel(score) }}</span>
              </div>
            </div>
          </div>
        </div>


          <!-- Critical Factors -->
          <div v-if="currentInsight.critical_factors && currentInsight.critical_factors.length > 0" class="section-card">
            <div class="section-header">
              <div class="section-title">
                <span class="title-icon">⚠️</span>
                <h3>Critical Success Factors</h3>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="(factor, idx) in currentInsight.critical_factors" :key="idx" class="p-4 bg-red-50 rounded-lg border border-red-200">
                <div class="flex items-center justify-between mb-2">
                  <h4 class="font-semibold text-gray-800">{{ factor.category }}</h4>
                  <span class="text-sm font-bold text-red-600">Impact: {{ (factor.impact * 100).toFixed(0) }}%</span>
                </div>
                <p class="text-sm text-gray-600">{{ factor.description }}</p>
                <div class="mt-2">
                  <div class="w-full bg-red-200 rounded-full h-2">
                    <div class="bg-red-600 rounded-full h-2" :style="{ width: (factor.impact * 100) + '%' }"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Low Scoring Questions -->
          <div v-if="currentInsight.low_scoring_questions && currentInsight.low_scoring_questions.length > 0" class="section-card">
            <div class="section-header">
              <div class="section-title">
                <span class="title-icon">📝</span>
                <h3>Low Scoring Questions</h3>
              </div>
              <p class="section-desc">Questions that need immediate attention</p>
            </div>
            <div class="space-y-3">
              <div v-for="(q, idx) in currentInsight.low_scoring_questions" :key="idx" class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <p class="font-medium text-gray-800">{{ q.question_text }}</p>
                    <p class="text-sm text-gray-600 mt-1">Category: {{ q.category }}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-2xl font-bold text-yellow-700">{{ q.average_rating }}</p>
                    <p class="text-xs text-gray-500">out of 5.0</p>
                  </div>
                </div>
                <div class="mt-2 w-full bg-yellow-200 rounded-full h-2">
                  <div class="bg-yellow-600 rounded-full h-2" :style="{ width: (q.average_rating / 5 * 100) + '%' }"></div>
                </div>
                <p class="text-xs text-yellow-700 mt-2">Priority: {{ q.priority_level }} - {{ q.recommendation }}</p>
              </div>
            </div>
          </div>

          <!-- Strengths & Weaknesses Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="section-card strengths">
              <div class="section-header">
                <div class="section-title">
                  <span class="title-icon">✅</span>
                  <h3>Strengths</h3>
                </div>
              </div>
              <div class="strengths-list">
                <div v-for="(item, idx) in currentInsight.strengths" :key="idx" class="strength-item">
                  <span class="strength-dot"></span>
                  <span>{{ item }}</span>
                </div>
                <div v-if="!currentInsight.strengths?.length" class="empty-state">No strengths identified</div>
              </div>
            </div>

            <div class="section-card weaknesses">
              <div class="section-header">
                <div class="section-title">
                  <span class="title-icon">⚠️</span>
                  <h3>Areas for Improvement</h3>
                </div>
              </div>
              <div class="weaknesses-list">
                <div v-for="(item, idx) in currentInsight.weaknesses" :key="idx" class="weakness-item">
                  <span class="weakness-dot"></span>
                  <span>{{ item }}</span>
                </div>
                <div v-if="!currentInsight.weaknesses?.length" class="empty-state">No weaknesses identified</div>
              </div>
            </div>
          </div>

          <!-- Priority Matrix -->
          <div class="section-card">
            <div class="section-header">
              <div class="section-title">
                <span class="title-icon">🎯</span>
                <h3>Priority Matrix</h3>
              </div>
              <p class="section-desc">Quick overview of what needs attention</p>
            </div>
            <div class="priority-grid">
              <div class="priority-card critical">
                <div class="priority-icon">🔴</div>
                <div class="priority-info">
                  <div class="priority-label">Critical</div>
                  <div class="priority-count">{{ priorityMatrix.critical.length }} issues</div>
                </div>
              </div>
              <div class="priority-card important">
                <div class="priority-icon">🟠</div>
                <div class="priority-info">
                  <div class="priority-label">Important</div>
                  <div class="priority-count">{{ priorityMatrix.important.length }} issues</div>
                </div>
              </div>
              <div class="priority-card urgent">
                <div class="priority-icon">🟡</div>
                <div class="priority-info">
                  <div class="priority-label">Quick Wins</div>
                  <div class="priority-count">{{ priorityMatrix.urgent.length }} issues</div>
                </div>
              </div>
              <div class="priority-card monitor">
                <div class="priority-icon">🟢</div>
                <div class="priority-info">
                  <div class="priority-label">Monitor</div>
                  <div class="priority-count">{{ priorityMatrix.monitor.length }} areas</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Actionable Recommendations -->
          <div class="section-card recommendations">
            <div class="section-header">
              <div class="section-title">
                <span class="title-icon">💡</span>
                <h3>Actionable Recommendations</h3>
              </div>
              <p class="section-desc">Based on participant feedback and scores</p>
            </div>
            <div class="recommendations-list">
              <div v-for="(rec, idx) in currentInsight.recommendations" :key="idx" class="rec-card" :class="rec.priority">
                <div class="rec-header">
                  <div class="rec-priority" :class="rec.priority">
                    <span class="priority-dot"></span>
                    {{ (rec.priority || 'medium').toUpperCase() }} PRIORITY
                  </div>
                  <div class="rec-score">
                    <span class="current">{{ rec.current_score }}/5.0</span>
                    <span class="arrow">→</span>
                    <span class="target">{{ rec.target_score }}/5.0</span>
                  </div>
                </div>
                <div class="rec-body">
                  <h4 class="rec-title">{{ rec.title }}</h4>
                  <p class="rec-description">{{ rec.problem_statement }}</p>
                  <div class="rec-actions">
                    <div class="actions-title">Action Steps:</div>
                    <ul class="actions-list">
                      <li v-for="(action, aidx) in rec.action_items" :key="aidx">
                        <span class="action-arrow">→</span>
                        <span>{{ action }}</span>
                      </li>
                    </ul>
                  </div>
                  <div class="rec-footer">
                    <span class="rec-outcome">🎯 {{ rec.expected_outcome }}</span>
                  </div>
                </div>
              </div>
              <div v-if="!currentInsight.recommendations?.length" class="empty-recommendations">
                <span>🎉</span>
                <p>All categories are performing well! No recommendations needed.</p>
              </div>
            </div>
          </div>

          <!-- Sentiment Analysis Section -->
          <div class="section-card sentiment">
            <div class="section-header">
              <div class="section-title">
                <span class="title-icon">💬</span>
                <h3>Sentiment Analysis</h3>
              </div>
              <p class="section-desc">Based on participant comments</p>
            </div>
            
            <!-- Sentiment Stats -->
            <div class="sentiment-stats">
              <div class="sentiment-stat positive">
                <div class="sentiment-icon">😊</div>
                <div class="sentiment-value">{{ positivePercentage }}%</div>
                <div class="sentiment-label">Positive</div>
                <div class="sentiment-count">{{ positiveCommentsList.length }} comments</div>
              </div>
              <div class="sentiment-stat neutral">
                <div class="sentiment-icon">😐</div>
                <div class="sentiment-value">{{ neutralPercentage }}%</div>
                <div class="sentiment-label">Neutral</div>
                <div class="sentiment-count">{{ neutralCommentsList.length }} comments</div>
              </div>
              <div class="sentiment-stat negative">
                <div class="sentiment-icon">😟</div>
                <div class="sentiment-value">{{ negativePercentage }}%</div>
                <div class="sentiment-label">Negative</div>
                <div class="sentiment-count">{{ negativeCommentsList.length }} comments</div>
              </div>
            </div>

            <!-- Sentiment Bar -->
            <div class="sentiment-bar">
              <div class="bar-positive" :style="{ width: positivePercentage + '%' }"></div>
              <div class="bar-neutral" :style="{ width: neutralPercentage + '%' }"></div>
              <div class="bar-negative" :style="{ width: negativePercentage + '%' }"></div>
            </div>

            <!-- Common Themes -->
            <div v-if="commonThemes.length" class="common-themes">
              <div class="themes-title">📌 Common Themes</div>
              <div class="themes-tags">
                <span v-for="theme in commonThemes" :key="theme" class="theme-tag">#{{ theme }}</span>
              </div>
            </div>

            <!-- Positive Comments -->
            <div v-if="positiveCommentsList.length" class="comment-section">
              <button @click="showPositiveComments = !showPositiveComments" class="comment-toggle positive">
                <span>{{ showPositiveComments ? '▼' : '▶' }}</span>
                <span>✅ Positive Comments</span>
                <span class="comment-count">{{ positiveCommentsList.length }}</span>
              </button>
              <div v-if="showPositiveComments" class="comment-list">
                <div v-for="(comment, idx) in paginatedPositiveComments" :key="idx" class="comment-item positive">
                  “{{ comment }}”
                </div>
                <div v-if="positiveTotalPages > 1" class="pagination">
                  <button @click="positiveCurrentPage--" :disabled="positiveCurrentPage === 1" class="page-btn">← Prev</button>
                  <span class="page-info">{{ positiveCurrentPage }} / {{ positiveTotalPages }}</span>
                  <button @click="positiveCurrentPage++" :disabled="positiveCurrentPage === positiveTotalPages" class="page-btn">Next →</button>
                </div>
              </div>
            </div>

            <!-- Negative Comments -->
            <div v-if="negativeCommentsList.length" class="comment-section">
              <button @click="showNegativeComments = !showNegativeComments" class="comment-toggle negative">
                <span>{{ showNegativeComments ? '▼' : '▶' }}</span>
                <span>❌ Negative Comments</span>
                <span class="comment-count">{{ negativeCommentsList.length }}</span>
              </button>
              <div v-if="showNegativeComments" class="comment-list">
                <div v-for="(comment, idx) in paginatedNegativeComments" :key="idx" class="comment-item negative">
                  “{{ comment }}”
                </div>
                <div v-if="negativeTotalPages > 1" class="pagination">
                  <button @click="negativeCurrentPage--" :disabled="negativeCurrentPage === 1" class="page-btn">← Prev</button>
                  <span class="page-info">{{ negativeCurrentPage }} / {{ negativeTotalPages }}</span>
                  <button @click="negativeCurrentPage++" :disabled="negativeCurrentPage === negativeTotalPages" class="page-btn">Next →</button>
                </div>
              </div>
            </div>

            <!-- Neutral Comments -->
            <div v-if="neutralCommentsList.length" class="comment-section">
              <button @click="showNeutralComments = !showNeutralComments" class="comment-toggle neutral">
                <span>{{ showNeutralComments ? '▼' : '▶' }}</span>
                <span>😐 Neutral Comments</span>
                <span class="comment-count">{{ neutralCommentsList.length }}</span>
              </button>
              <div v-if="showNeutralComments" class="comment-list">
                <div v-for="(comment, idx) in paginatedNeutralComments" :key="idx" class="comment-item neutral">
                  “{{ comment }}”
                </div>
                <div v-if="neutralTotalPages > 1" class="pagination">
                  <button @click="neutralCurrentPage--" :disabled="neutralCurrentPage === 1" class="page-btn">← Prev</button>
                  <span class="page-info">{{ neutralCurrentPage }} / {{ neutralTotalPages }}</span>
                  <button @click="neutralCurrentPage++" :disabled="neutralCurrentPage === neutralTotalPages" class="page-btn">Next →</button>
                </div>
              </div>
            </div>

            <div v-if="totalComments === 0" class="empty-sentiment">
              <span>💬</span>
              <p>No comments provided by participants</p>
            </div>
          </div>

        </div>

        <!-- No AI Insights Message -->
        <div v-else-if="evaluation.status === 'closed'" class="empty-state-card">
          <div class="empty-icon">🤖</div>
          <h3>No AI Insights Yet</h3>
          <p>AI insights are being generated by QUAMS. Please check back later.</p>
        </div>

        <!-- Raw Results Section (When no AI insights available) -->
        <div v-if="!currentInsight && evaluation.responses_count > 0 && evaluation.status !== 'draft'" class="space-y-8">
          <div v-for="category in evaluation.categories" :key="category.id" class="section-card">
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

          <div v-if="evaluation.comments && evaluation.comments.length > 0" class="section-card">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Comments & Feedback</h2>
            <div v-for="comment in evaluation.comments" :key="comment.id" class="mb-6 last:mb-0">
              <h3 class="font-medium text-gray-700 mb-3">{{ comment.text }}</h3>
              <div v-if="comments && comments[comment.id] && comments[comment.id].responses.length > 0" class="space-y-3">
                <div v-for="(response, idx) in comments[comment.id].responses" :key="idx" class="p-4 bg-gray-50 rounded-lg">
                  <p class="text-gray-700">{{ response }}</p>
                </div>
              </div>
              <p v-else class="text-gray-400 italic">No comments yet.</p>
            </div>
          </div>
        </div>

        <div v-else-if="evaluation.responses_count === 0 && evaluation.status !== 'draft'" class="empty-state-card">
          <div class="empty-icon">📭</div>
          <h3>No Responses Yet</h3>
          <p>Share the QR code with students to start collecting feedback.</p>
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
  dateInsights: {
    type: Array,
    default: () => []
  },
  availableDates: {
    type: Array,
    default: () => []
  },
  categoryBreakdown: {
    type: Object,
    default: () => ({})
  }
});

// Current selected date for insights
const currentDate = ref('overall');
const currentInsight = ref(null);

// Comment visibility toggles
const showPositiveComments = ref(false);
const showNegativeComments = ref(false);
const showNeutralComments = ref(false);

// Pagination
const COMMENTS_PER_PAGE = 10;
const positiveCurrentPage = ref(1);
const negativeCurrentPage = ref(1);
const neutralCurrentPage = ref(1);

// Comment lists
const positiveCommentsList = ref([]);
const negativeCommentsList = ref([]);
const neutralCommentsList = ref([]);

// Computed paginated comments
const paginatedPositiveComments = computed(() => {
  const start = (positiveCurrentPage.value - 1) * COMMENTS_PER_PAGE;
  return positiveCommentsList.value.slice(start, start + COMMENTS_PER_PAGE);
});

const paginatedNegativeComments = computed(() => {
  const start = (negativeCurrentPage.value - 1) * COMMENTS_PER_PAGE;
  return negativeCommentsList.value.slice(start, start + COMMENTS_PER_PAGE);
});

const paginatedNeutralComments = computed(() => {
  const start = (neutralCurrentPage.value - 1) * COMMENTS_PER_PAGE;
  return neutralCommentsList.value.slice(start, start + COMMENTS_PER_PAGE);
});

const positiveTotalPages = computed(() => Math.ceil(positiveCommentsList.value.length / COMMENTS_PER_PAGE));
const negativeTotalPages = computed(() => Math.ceil(negativeCommentsList.value.length / COMMENTS_PER_PAGE));
const neutralTotalPages = computed(() => Math.ceil(neutralCommentsList.value.length / COMMENTS_PER_PAGE));

const totalComments = computed(() => positiveCommentsList.value.length + negativeCommentsList.value.length + neutralCommentsList.value.length);

// Priority Matrix
const priorityMatrix = computed(() => {
  const categories = displayCategoryBreakdown.value;
  const matrix = { critical: [], important: [], urgent: [], monitor: [] };
  
  for (const [category, score] of Object.entries(categories)) {
    const urgency = score < 2.5 ? 100 : (score < 3.0 ? 70 : (score < 3.5 ? 40 : 10));
    const importance = getCategoryImportance(category);
    
    if (urgency >= 70 && importance >= 70) matrix.critical.push({ category, score });
    else if (importance >= 70) matrix.important.push({ category, score });
    else if (urgency >= 70) matrix.urgent.push({ category, score });
    else matrix.monitor.push({ category, score });
  }
  return matrix;
});

// Sentiment percentages
const positivePercentage = computed(() => currentInsight.value?.sentiment_analysis?.positive_percentage || 0);
const negativePercentage = computed(() => currentInsight.value?.sentiment_analysis?.negative_percentage || 0);
const neutralPercentage = computed(() => currentInsight.value?.sentiment_analysis?.neutral_percentage || 0);
const commonThemes = computed(() => currentInsight.value?.sentiment_analysis?.common_themes || []);

// Display category breakdown
const displayCategoryBreakdown = computed(() => {
  if (currentInsight.value?.category_breakdown && Object.keys(currentInsight.value.category_breakdown).length > 0) {
    return currentInsight.value.category_breakdown;
  }
  return props.categoryBreakdown;
});

const backUrl = computed(() => '/president/evaluations');
const qrCanvas = ref(null);
const copied = ref(false);
const evaluationUrl = ref('');

const totalQuestions = computed(() => {
  let count = 0;
  if (props.evaluation.categories) {
    props.evaluation.categories.forEach(cat => {
      count += cat.questions?.length || 0;
    });
  }
  return count;
});

// Helper Functions
const getScoreColor = (score) => {
  if (score >= 4.5) return '#10B981';
  if (score >= 3.5) return '#34D399';
  if (score >= 2.5) return '#FBBF24';
  if (score >= 1.5) return '#F97316';
  return '#EF4444';
};

const getHeroClass = (score) => {
  if (score >= 4.5) return 'hero-excellent';
  if (score >= 3.5) return 'hero-good';
  if (score >= 2.5) return 'hero-average';
  return 'hero-poor';
};

const getRatingBadgeClass = (score) => {
  if (score >= 4.5) return 'badge-excellent';
  if (score >= 3.5) return 'badge-good';
  if (score >= 2.5) return 'badge-average';
  return 'badge-poor';
};

const getInterpretationLabel = (score) => {
  if (score >= 4.5) return 'Outstanding';
  if (score >= 3.5) return 'Very Satisfactory';
  if (score >= 2.5) return 'Satisfactory';
  if (score >= 1.5) return 'Poor';
  return 'Very Poor';
};

const getVerbalInterpretation = (score) => {
  if (score >= 4.5) return 'Very Satisfied';
  if (score >= 3.5) return 'Satisfied';
  if (score >= 2.5) return 'Neither Satisfied nor Dissatisfied';
  if (score >= 1.5) return 'Dissatisfied';
  return 'Very Dissatisfied';
};

const getSuccessRate = (score) => {
  if (score >= 4.5) return 95;
  if (score >= 4.0) return 85;
  if (score >= 3.5) return 75;
  if (score >= 3.0) return 60;
  if (score >= 2.5) return 45;
  return 30;
};

const simplifyCategoryName = (name) => {
  return name.replace(/^[IVX]+\.\s*/, '').substring(0, 35);
};

const getCategoryImportance = (category) => {
  const importance = { 'Food': 95, 'Resource Speaker': 90, 'Outcomes': 85, 'Secretariat': 80, 'Facilities': 75, 'Design': 70, 'Information': 65 };
  for (const [key, value] of Object.entries(importance)) {
    if (category.includes(key)) return value;
  }
  return 50;
};

const getHighPriorityCount = () => {
  return currentInsight.value?.recommendations?.filter(r => r.priority?.toLowerCase() === 'high').length || 0;
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatDateOnly = (date) => {
  if (!date) return '';
  if (date === 'overall') return 'Overall';
  try {
    const d = new Date(date);
    if (isNaN(d.getTime())) return date;
    return d.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  } catch (e) {
    return date;
  }
};

const updateSentimentData = () => {
  // Get comments from sentiment_analysis or direct fields
  positiveCommentsList.value = currentInsight.value?.sentiment_analysis?.positive_comments || 
                               currentInsight.value?.positive_comments || [];
  negativeCommentsList.value = currentInsight.value?.sentiment_analysis?.negative_comments || 
                               currentInsight.value?.negative_comments || [];
  neutralCommentsList.value = currentInsight.value?.sentiment_analysis?.neutral_comments || 
                              currentInsight.value?.neutral_comments || [];
  
  // Reset pagination
  positiveCurrentPage.value = 1;
  negativeCurrentPage.value = 1;
  neutralCurrentPage.value = 1;
};

const selectDate = (date) => {
  currentDate.value = date;
  if (date === 'overall') {
    currentInsight.value = props.aiInsights;
  } else {
    currentInsight.value = props.dateInsights.find(i => i.event_date === date);
  }
  updateSentimentData();
};

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(evaluationUrl.value);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
  } catch (err) {
    console.error('Failed to copy:', err);
  }
};

const downloadQRCode = () => {
  if (qrCanvas.value) {
    const link = document.createElement('a');
    link.download = `qrcode-${props.evaluation.id}.png`;
    link.href = qrCanvas.value.toDataURL('image/png');
    link.click();
  }
};

// Watch for changes
watch(() => [props.aiInsights, props.dateInsights], () => {
  if (props.aiInsights) {
    selectDate('overall');
  } else if (props.dateInsights.length > 0) {
    selectDate(props.dateInsights[0].event_date);
  }
}, { immediate: true });

// Initialize QR code
onMounted(async () => {
  evaluationUrl.value = props.evaluation.qr_code_url || 
    `${window.location.origin}/evaluations/${props.evaluation.id}/form`;
  
  if (props.evaluation.status === 'active' && evaluationUrl.value && qrCanvas.value) {
    try {
      await QRCode.toCanvas(qrCanvas.value, evaluationUrl.value, {
        width: 200,
        margin: 1,
        color: { dark: '#000000', light: '#ffffff' }
      });
    } catch (err) {
      console.error('Failed to generate QR code:', err);
    }
  }
});
</script>

<style scoped>
.modern-dashboard {
  background: #f5f7fa;
  min-height: 100vh;
}

/* Date Selector */
.date-selector {
  background: white;
  border-radius: 20px;
  padding: 20px 24px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.date-selector-header h3 {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a2e;
  margin: 0 0 16px 0;
}

.date-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.date-tab {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 40px;
  font-size: 13px;
  font-weight: 500;
  color: #6c757d;
  cursor: pointer;
  transition: all 0.2s;
}

.date-tab:hover {
  background: #e9ecef;
}

.date-tab.active {
  background: #6366f1;
  border-color: #6366f1;
  color: white;
}

.date-count {
  background: rgba(0,0,0,0.1);
  padding: 2px 6px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
}

.date-tab.active .date-count {
  background: rgba(255,255,255,0.2);
}

/* Section Cards */
.section-card {
  background: white;
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.section-header {
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 2px solid #f0f0f0;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 10px;
}

.title-icon {
  font-size: 22px;
}

.section-title h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a2e;
  margin: 0;
}

.section-desc {
  font-size: 13px;
  color: #94a3b8;
  margin-top: 6px;
  margin-left: 32px;
}

/* Category List */
.category-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.category-item {
  width: 100%;
}

.category-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.category-name {
  font-size: 14px;
  font-weight: 500;
  color: #334155;
}

.category-score .score-value {
  font-size: 18px;
  font-weight: 700;
}

.category-score .score-max {
  font-size: 12px;
  color: #94a3b8;
}

.category-bar {
  display: flex;
  align-items: center;
  gap: 12px;
}

.bar-bg {
  flex: 1;
  background: #e9ecef;
  border-radius: 8px;
  height: 8px;
  overflow: hidden;
}

.bar-fill {
  height: 100%;
  border-radius: 8px;
  transition: width 0.5s ease;
}

.bar-label {
  font-size: 11px;
  font-weight: 500;
  color: #94a3b8;
  min-width: 90px;
  text-align: right;
}

/* Hero Card */
.hero-card {
  position: relative;
  border-radius: 24px;
  overflow: hidden;
  margin-bottom: 24px;
}

.hero-bg {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.hero-excellent .hero-bg {
  background: linear-gradient(135deg, #10B981 0%, #059669 100%);
}

.hero-good .hero-bg {
  background: linear-gradient(135deg, #34D399 0%, #10B981 100%);
}

.hero-average .hero-bg {
  background: linear-gradient(135deg, #FBBF24 0%, #D97706 100%);
}

.hero-poor .hero-bg {
  background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
}

.hero-content {
  position: relative;
  padding: 32px;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 24px;
}

.hero-left {
  flex: 1;
}

.hero-badge {
  display: inline-block;
  padding: 4px 12px;
  background: rgba(255,255,255,0.2);
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  color: white;
  margin-bottom: 16px;
}

.hero-score {
  display: flex;
  align-items: baseline;
  gap: 4px;
  margin-bottom: 12px;
}

.score-number {
  font-size: 56px;
  font-weight: 800;
  color: white;
  line-height: 1;
}

.score-max {
  font-size: 20px;
  font-weight: 500;
  color: rgba(255,255,255,0.7);
}

.hero-rating {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.rating-badge {
  padding: 6px 14px;
  border-radius: 40px;
  font-size: 13px;
  font-weight: 600;
}

.badge-excellent {
  background: #10B981;
  color: white;
}

.badge-good {
  background: #34D399;
  color: white;
}

.badge-average {
  background: #FBBF24;
  color: #1a1a2e;
}

.badge-poor {
  background: #EF4444;
  color: white;
}

.rating-verb {
  color: rgba(255,255,255,0.9);
  font-size: 14px;
}

.hero-stats {
  display: flex;
  gap: 16px;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 20px;
  background: rgba(255,255,255,0.15);
  border-radius: 16px;
  backdrop-filter: blur(10px);
}

.stat-icon {
  font-size: 24px;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: white;
  line-height: 1.2;
}

.stat-label {
  font-size: 11px;
  color: rgba(255,255,255,0.8);
}

.hero-progress {
  position: relative;
  padding: 0 32px 24px 32px;
}

.progress-bar-bg {
  background: rgba(255,255,255,0.2);
  border-radius: 10px;
  height: 6px;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  background: white;
  border-radius: 10px;
  transition: width 0.5s ease;
}

.progress-labels {
  display: flex;
  justify-content: space-between;
  margin-top: 8px;
  font-size: 10px;
  color: rgba(255,255,255,0.6);
}

/* Strengths & Weaknesses */
.strengths-list, .weaknesses-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.strength-item, .weakness-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  color: #334155;
  padding: 8px 12px;
  background: #f8f9fa;
  border-radius: 12px;
}

.strength-dot {
  width: 8px;
  height: 8px;
  background: #10B981;
  border-radius: 50%;
}

.weakness-dot {
  width: 8px;
  height: 8px;
  background: #EF4444;
  border-radius: 50%;
}

.empty-state {
  text-align: center;
  padding: 32px;
  color: #94a3b8;
  font-size: 14px;
}

/* Priority Grid */
.priority-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

@media (max-width: 640px) {
  .priority-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

.priority-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  border-radius: 16px;
}

.priority-card.critical { background: #FEF2F2; }
.priority-card.important { background: #FFF7ED; }
.priority-card.urgent { background: #FEFCE8; }
.priority-card.monitor { background: #ECFDF5; }

.priority-icon {
  font-size: 28px;
}

.priority-label {
  font-size: 13px;
  font-weight: 600;
  color: #1a1a2e;
}

.priority-count {
  font-size: 11px;
  color: #64748b;
}

/* Recommendations */
.recommendations-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.rec-card {
  border-radius: 16px;
  overflow: hidden;
  background: white;
  border: 1px solid #e9ecef;
  transition: all 0.2s;
}

.rec-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transform: translateY(-2px);
}

.rec-card.high {
  border-left: 4px solid #EF4444;
}

.rec-card.medium {
  border-left: 4px solid #FBBF24;
}

.rec-card.low {
  border-left: 4px solid #3B82F6;
}

.rec-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 20px;
  background: #f8f9fa;
  flex-wrap: wrap;
  gap: 12px;
}

.rec-priority {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.rec-priority.high { color: #EF4444; }
.rec-priority.medium { color: #FBBF24; }
.rec-priority.low { color: #3B82F6; }

.priority-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.rec-priority.high .priority-dot { background: #EF4444; }
.rec-priority.medium .priority-dot { background: #FBBF24; }
.rec-priority.low .priority-dot { background: #3B82F6; }

.rec-score {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
}

.rec-score .current { color: #EF4444; font-weight: 600; }
.rec-score .target { color: #10B981; font-weight: 600; }
.rec-score .arrow { color: #94a3b8; }

.rec-body {
  padding: 20px;
}

.rec-title {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a2e;
  margin: 0 0 8px 0;
}

.rec-description {
  font-size: 13px;
  color: #64748b;
  margin: 0 0 16px 0;
  line-height: 1.5;
}

.rec-actions {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 16px;
}

.actions-title {
  font-size: 12px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 10px;
}

.actions-list {
  list-style: none;
  margin: 0;
  padding: 0;
}

.actions-list li {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
  color: #475569;
  margin-bottom: 8px;
}

.action-arrow {
  color: #6366f1;
}

.rec-footer {
  padding-top: 12px;
  border-top: 1px solid #e9ecef;
}

.rec-outcome {
  font-size: 12px;
  font-weight: 500;
  color: #10B981;
}

.empty-recommendations {
  text-align: center;
  padding: 40px;
}

.empty-recommendations span {
  font-size: 48px;
  display: block;
  margin-bottom: 12px;
}

/* Sentiment Section */
.sentiment-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}

.sentiment-stat {
  text-align: center;
  padding: 20px;
  border-radius: 16px;
}

.sentiment-stat.positive { background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); }
.sentiment-stat.neutral { background: linear-gradient(135deg, #FEFCE8 0%, #FEF3C7 100%); }
.sentiment-stat.negative { background: linear-gradient(135deg, #FEF2F2 0%, #FEE2E2 100%); }

.sentiment-icon { font-size: 32px; margin-bottom: 8px; }
.sentiment-value { font-size: 28px; font-weight: 800; }
.sentiment-stat.positive .sentiment-value { color: #10B981; }
.sentiment-stat.neutral .sentiment-value { color: #FBBF24; }
.sentiment-stat.negative .sentiment-value { color: #EF4444; }
.sentiment-label { font-size: 12px; font-weight: 500; color: #64748b; }
.sentiment-count { font-size: 11px; color: #94a3b8; margin-top: 4px; }

.sentiment-bar {
  display: flex;
  height: 8px;
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 24px;
}

.bar-positive { background: #10B981; }
.bar-neutral { background: #FBBF24; }
.bar-negative { background: #EF4444; }

.common-themes {
  margin-bottom: 24px;
}

.themes-title {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 12px;
}

.themes-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.theme-tag {
  padding: 6px 14px;
  background: #f0f0f0;
  border-radius: 30px;
  font-size: 12px;
  font-weight: 500;
  color: #4a5568;
}

/* Comment Section */
.comment-section {
  margin-top: 20px;
  border-top: 1px solid #e9ecef;
  padding-top: 20px;
}

.comment-toggle {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 12px 16px;
  background: #f8f9fa;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.comment-toggle.positive { color: #10B981; }
.comment-toggle.negative { color: #EF4444; }
.comment-toggle.neutral { color: #64748b; }

.comment-count {
  margin-left: auto;
  background: rgba(0,0,0,0.05);
  padding: 2px 8px;
  border-radius: 20px;
  font-size: 11px;
}

.comment-list {
  margin-top: 12px;
  padding-left: 16px;
}

.comment-item {
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 13px;
  line-height: 1.5;
  margin-bottom: 8px;
}

.comment-item.positive {
  background: #ECFDF5;
  color: #065F46;
  border-left: 3px solid #10B981;
}

.comment-item.negative {
  background: #FEF2F2;
  color: #991B1B;
  border-left: 3px solid #EF4444;
}

.comment-item.neutral {
  background: #f8f9fa;
  color: #4a5568;
  border-left: 3px solid #cbd5e1;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 12px;
  margin-top: 16px;
}

.page-btn {
  padding: 6px 14px;
  background: #f0f0f0;
  border: none;
  border-radius: 8px;
  font-size: 12px;
  cursor: pointer;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 12px;
  color: #64748b;
}

.empty-sentiment {
  text-align: center;
  padding: 40px;
}

/* Empty State */
.empty-state-card {
  background: white;
  border-radius: 24px;
  padding: 60px;
  text-align: center;
}

.empty-icon {
  font-size: 64px;
  margin-bottom: 20px;
}

.empty-state-card h3 {
  font-size: 20px;
  font-weight: 600;
  color: #1a1a2e;
  margin: 0 0 8px 0;
}

.empty-state-card p {
  color: #64748b;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>