<template>
  <div class="modern-dashboard">
    <!-- Date Selector -->
    <div v-if="allInsights && Object.keys(allInsights).length > 0" class="date-selector">
      <div class="date-selector-header">
        <h3>Analysis View</h3>
        <button 
          @click="fetchAllInsights" 
          :disabled="loading"
          class="btn-refresh"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M23 4v6h-6M1 20v-6h6"/>
            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
          </svg>
          <span>Refresh</span>
        </button>
      </div>
      <div class="date-tabs">
        <button
          v-for="(insight, date) in allInsights"
          :key="date"
          @click="selectDate(date)"
          :class="['date-tab', { active: currentDate === date }]"
        >
          <span class="date-icon">{{ date === 'overall' ? '📊' : '📅' }}</span>
          <span class="date-label">{{ formatTabLabel(date) }}</span>
          <span class="date-count">{{ insight.total_respondents || 0 }}</span>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="loading-spinner"></div>
      <p>Loading AI insights...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <div class="error-icon">⚠️</div>
      <p>{{ error }}</p>
    </div>

    <!-- Main Dashboard -->
    <div v-else-if="currentInsight" class="dashboard-content">
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

      <!-- Category Performance Section -->
      <div class="section-card">
        <div class="section-header">
          <div class="section-title">
            <span class="title-icon">📈</span>
            <h3>Category Performance</h3>
          </div>
          <p class="section-desc">Detailed breakdown of each evaluation category</p>
        </div>
        <div class="category-list">
          <div v-for="(score, category) in categoryBreakdown" :key="category" class="category-item">
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

      <!-- Low Scoring Questions -->
      <div v-if="currentInsight.low_scoring_questions && currentInsight.low_scoring_questions.length > 0" class="section-card low-scoring">
        <div class="section-header">
          <div class="section-title">
            <span class="title-icon">⚠️</span>
            <h3>Low Scoring Questions</h3>
          </div>
          <p class="section-desc">Questions with average rating below 3.5/5.0 - Requires immediate attention</p>
        </div>
        <div class="low-scoring-list">
          <div v-for="(q, idx) in currentInsight.low_scoring_questions" :key="idx" class="low-scoring-item">
            <div class="low-scoring-header">
              <div class="question-info">
                <span class="question-number">#{{ idx + 1 }}</span>
                <span class="question-text">{{ q.question_text }}</span>
              </div>
              <div class="question-score">
                <span class="score-value" :class="getScoreTextClass(q.average_rating)">{{ q.average_rating }}</span>
                <span class="score-max">/5.0</span>
              </div>
            </div>
            <div class="question-details">
              <div class="detail-row">
                <span class="detail-label">Category:</span>
                <span class="detail-value">{{ q.category }}</span>
                <span class="priority-badge" :class="getPriorityBadgeClass(q.priority_level)">{{ q.priority_level }} Priority</span>
              </div>
              <div class="progress-bar-container">
                <div class="progress-bar-bg-red">
                  <div class="progress-bar-fill-red" :style="{ width: (q.average_rating / 5 * 100) + '%' }"></div>
                </div>
              </div>
              <div class="recommendation-box">
                <span class="recommendation-icon">💡</span>
                <span class="recommendation-text">{{ q.recommendation }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Critical Factors -->
      <div v-if="currentInsight.critical_factors && currentInsight.critical_factors.length > 0" class="section-card critical-factors">
        <div class="section-header">
          <div class="section-title">
            <span class="title-icon">🔥</span>
            <h3>Critical Success Factors</h3>
          </div>
          <p class="section-desc">Factors that have the highest impact on overall satisfaction</p>
        </div>
        <div class="critical-factors-grid">
          <div v-for="(factor, idx) in currentInsight.critical_factors" :key="idx" class="factor-card" :class="getFactorClass(factor.status)">
            <div class="factor-header">
              <span class="factor-name">{{ factor.category }}</span>
              <span class="factor-impact">{{ (factor.impact * 100).toFixed(0) }}% impact</span>
            </div>
            <div class="factor-description">{{ factor.description }}</div>
            <div class="factor-score-row">
              <span class="factor-label">Current Score:</span>
              <span class="factor-score" :class="getScoreTextClass(factor.score)">{{ factor.score }}/5.0</span>
            </div>
            <div class="impact-bar">
              <div class="impact-bar-bg">
                <div class="impact-bar-fill" :style="{ width: (factor.impact * 100) + '%' }"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Strengths & Weaknesses Grid -->
      <div class="grid-2">
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

      <!-- Recommendations Section -->
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
                {{ rec.priority?.toUpperCase() || 'MEDIUM' }} PRIORITY
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

        <!-- Positive Comments Accordion -->
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

        <!-- Negative Comments Accordion -->
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

        <!-- Neutral Comments Accordion -->
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

    <!-- No Data State -->
    <div v-else class="empty-state-card">
      <div class="empty-icon">📋</div>
      <h3>No Insights Available</h3>
      <p>Click "Generate AI Insights" in the main evaluation page to analyze responses.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
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
const allInsights = ref({});
const currentDate = ref('overall');
const currentInsight = ref(null);

// Comment visibility toggles
const showPositiveComments = ref(false);
const showNegativeComments = ref(false);
const showNeutralComments = ref(false);

// Pagination
const COMMENTS_PER_PAGE = 10;

const positiveCurrentPage = ref(1);
const positiveCommentsList = ref([]);
const negativeCurrentPage = ref(1);
const negativeCommentsList = ref([]);
const neutralCurrentPage = ref(1);
const neutralCommentsList = ref([]);

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

// Computed Properties
const categoryBreakdown = computed(() => currentInsight.value?.category_breakdown || {});

const priorityMatrix = computed(() => {
  if (!categoryBreakdown.value) return { critical: [], important: [], urgent: [], monitor: [] };
  const categories = categoryBreakdown.value;
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

const positivePercentage = computed(() => currentInsight.value?.sentiment_analysis?.positive_percentage || 0);
const negativePercentage = computed(() => currentInsight.value?.sentiment_analysis?.negative_percentage || 0);
const neutralPercentage = computed(() => currentInsight.value?.sentiment_analysis?.neutral_percentage || 0);
const commonThemes = computed(() => currentInsight.value?.sentiment_analysis?.common_themes || []);

// Helper Functions
const getScoreColor = (score) => {
  if (score >= 4.5) return '#10B981';
  if (score >= 3.5) return '#34D399';
  if (score >= 2.5) return '#FBBF24';
  if (score >= 1.5) return '#F97316';
  return '#EF4444';
};

const getScoreTextClass = (score) => {
  if (score >= 4.5) return 'text-green-600';
  if (score >= 3.5) return 'text-green-500';
  if (score >= 2.5) return 'text-yellow-600';
  if (score >= 1.5) return 'text-orange-600';
  return 'text-red-600';
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

const getPriorityBadgeClass = (level) => {
  const l = level?.toLowerCase();
  if (l === 'high') return 'priority-high';
  if (l === 'medium') return 'priority-medium';
  return 'priority-low';
};

const getFactorClass = (status) => {
  if (status === 'critical') return 'factor-critical';
  if (status === 'needs_improvement') return 'factor-improvement';
  return 'factor-good';
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

const formatDateOnly = (date) => {
  if (!date || date === 'overall') return 'Overall';
  try {
    const d = new Date(date);
    return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
  } catch { return date; }
};

const formatTabLabel = (date) => date === 'overall' ? 'Overall' : formatDateOnly(date);

const updateSentimentData = () => {
  positiveCommentsList.value = currentInsight.value?.positive_comments || 
                               currentInsight.value?.sentiment_analysis?.positive_comments || [];
  negativeCommentsList.value = currentInsight.value?.negative_comments || 
                               currentInsight.value?.sentiment_analysis?.negative_comments || [];
  neutralCommentsList.value = currentInsight.value?.neutral_comments || 
                              currentInsight.value?.sentiment_analysis?.neutral_comments || [];
  positiveCurrentPage.value = 1;
  negativeCurrentPage.value = 1;
  neutralCurrentPage.value = 1;
};

const selectDate = (date) => {
  currentDate.value = date;
  currentInsight.value = allInsights.value[date];
  updateSentimentData();
};

const fetchAllInsights = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get(`/admin/evaluations/${props.evaluationId}/ai-insights`);
    if (response.data.insights && Object.keys(response.data.insights).length > 0) {
      allInsights.value = response.data.insights;
      selectDate(allInsights.value.overall ? 'overall' : Object.keys(allInsights.value)[0]);
      emit('insights-loaded', allInsights.value);
    } else if (response.data.available_dates?.length) {
      error.value = 'No insights generated yet. Click "Generate AI Insights" in the main evaluation page to start analysis.';
    } else {
      error.value = 'No responses found. Add responses first to generate insights.';
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load insights';
  } finally {
    loading.value = false;
  }
};

onMounted(() => { 
  if (props.evaluationId) fetchAllInsights(); 
});

defineExpose({ fetchAllInsights });
</script>

<style scoped>
.modern-dashboard {
  max-width: 1400px;
  margin: 0 auto;
  padding: 24px;
  background: #f5f7fa;
  min-height: 100vh;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
}

.date-selector {
  background: white;
  border-radius: 20px;
  padding: 20px 24px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.date-selector-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.date-selector-header h3 {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a2e;
  margin: 0;
}

.btn-refresh {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: #f0f0f0;
  border: none;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 500;
  color: #4a5568;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-refresh:hover:not(:disabled) {
  background: #e0e0e0;
}

.btn-refresh:disabled {
  opacity: 0.5;
  cursor: not-allowed;
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

.date-icon {
  font-size: 14px;
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

.loading-state {
  background: white;
  border-radius: 20px;
  padding: 60px;
  text-align: center;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e9ecef;
  border-top-color: #6366f1;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 16px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.error-state {
  background: white;
  border-radius: 20px;
  padding: 40px;
  text-align: center;
}

.error-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.error-state p {
  color: #ef4444;
  margin-bottom: 20px;
}

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

.category-score {
  font-size: 14px;
  font-weight: 600;
}

.score-value {
  font-size: 18px;
  font-weight: 700;
}

.score-max {
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

.low-scoring-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.low-scoring-item {
  border: 1px solid #fee2e2;
  border-radius: 16px;
  padding: 16px;
  background: #fef2f2;
}

.low-scoring-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 12px;
}

.question-info {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.question-number {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  background: #ef4444;
  color: white;
  border-radius: 10px;
  font-size: 12px;
  font-weight: 700;
}

.question-text {
  font-weight: 600;
  color: #1f2937;
}

.question-score {
  text-align: right;
}

.question-score .score-value {
  font-size: 24px;
  font-weight: 700;
}

.text-green-600 { color: #10B981; }
.text-green-500 { color: #34D399; }
.text-yellow-600 { color: #FBBF24; }
.text-orange-600 { color: #F97316; }
.text-red-600 { color: #EF4444; }

.question-details {
  margin-top: 12px;
}

.detail-row {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 12px;
  font-size: 13px;
}

.detail-label {
  color: #6b7280;
}

.detail-value {
  color: #374151;
  font-weight: 500;
}

.priority-badge {
  padding: 2px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
}

.priority-high {
  background: #fee2e2;
  color: #dc2626;
}

.priority-medium {
  background: #fef3c7;
  color: #d97706;
}

.priority-low {
  background: #dbeafe;
  color: #2563eb;
}

.progress-bar-container {
  margin: 12px 0;
}

.progress-bar-bg-red {
  background: #fee2e2;
  border-radius: 10px;
  height: 8px;
  overflow: hidden;
}

.progress-bar-fill-red {
  height: 100%;
  background: #ef4444;
  border-radius: 10px;
  transition: width 0.5s ease;
}

.recommendation-box {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 12px;
  background: #fff7ed;
  border-radius: 12px;
  margin-top: 12px;
  border-left: 3px solid #f97316;
}

.recommendation-icon {
  font-size: 16px;
}

.recommendation-text {
  font-size: 13px;
  color: #92400e;
  line-height: 1.4;
}

.critical-factors-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
}

.factor-card {
  padding: 16px;
  border-radius: 16px;
  transition: transform 0.2s;
}

.factor-card:hover {
  transform: translateY(-2px);
}

.factor-critical {
  background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
  border-left: 4px solid #ef4444;
}

.factor-improvement {
  background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
  border-left: 4px solid #f97316;
}

.factor-good {
  background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
  border-left: 4px solid #10b981;
}

.factor-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  flex-wrap: wrap;
  gap: 8px;
}

.factor-name {
  font-weight: 700;
  font-size: 16px;
  color: #1f2937;
}

.factor-impact {
  font-size: 12px;
  font-weight: 600;
  padding: 4px 10px;
  background: rgba(0,0,0,0.05);
  border-radius: 20px;
}

.factor-description {
  font-size: 13px;
  color: #4b5563;
  margin-bottom: 12px;
  line-height: 1.4;
}

.factor-score-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
  font-size: 13px;
}

.factor-label {
  color: #6b7280;
}

.factor-score {
  font-weight: 700;
  font-size: 15px;
}

.impact-bar {
  margin-top: 8px;
}

.impact-bar-bg {
  background: rgba(0,0,0,0.1);
  border-radius: 10px;
  height: 6px;
  overflow: hidden;
}

.impact-bar-fill {
  height: 100%;
  background: currentColor;
  border-radius: 10px;
  transition: width 0.5s ease;
}

.factor-critical .impact-bar-fill {
  background: #ef4444;
}

.factor-improvement .impact-bar-fill {
  background: #f97316;
}

.factor-good .impact-bar-fill {
  background: #10b981;
}

.grid-2 {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
}

@media (max-width: 768px) {
  .grid-2 {
    grid-template-columns: 1fr;
  }
}

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
  transition: transform 0.2s;
}

.priority-card:hover {
  transform: translateY(-2px);
}

.priority-card.critical {
  background: #FEF2F2;
}

.priority-card.important {
  background: #FFF7ED;
}

.priority-card.urgent {
  background: #FEFCE8;
}

.priority-card.monitor {
  background: #ECFDF5;
}

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

.rec-priority.high {
  color: #EF4444;
}

.rec-priority.medium {
  color: #FBBF24;
}

.rec-priority.low {
  color: #3B82F6;
}

.priority-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.rec-priority.high .priority-dot {
  background: #EF4444;
}

.rec-priority.medium .priority-dot {
  background: #FBBF24;
}

.rec-priority.low .priority-dot {
  background: #3B82F6;
}

.rec-score {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
}

.rec-score .current {
  color: #EF4444;
  font-weight: 600;
}

.rec-score .target {
  color: #10B981;
  font-weight: 600;
}

.rec-score .arrow {
  color: #94a3b8;
}

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

.empty-recommendations p {
  color: #64748b;
}

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

.sentiment-stat.positive {
  background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%);
}

.sentiment-stat.neutral {
  background: linear-gradient(135deg, #FEFCE8 0%, #FEF3C7 100%);
}

.sentiment-stat.negative {
  background: linear-gradient(135deg, #FEF2F2 0%, #FEE2E2 100%);
}

.sentiment-icon {
  font-size: 32px;
  margin-bottom: 8px;
}

.sentiment-value {
  font-size: 28px;
  font-weight: 800;
}

.sentiment-stat.positive .sentiment-value {
  color: #10B981;
}

.sentiment-stat.neutral .sentiment-value {
  color: #FBBF24;
}

.sentiment-stat.negative .sentiment-value {
  color: #EF4444;
}

.sentiment-label {
  font-size: 12px;
  font-weight: 500;
  color: #64748b;
}

.sentiment-count {
  font-size: 11px;
  color: #94a3b8;
  margin-top: 4px;
}

.sentiment-bar {
  display: flex;
  height: 8px;
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 24px;
}

.bar-positive {
  background: #10B981;
}

.bar-neutral {
  background: #FBBF24;
}

.bar-negative {
  background: #EF4444;
}

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

.comment-toggle.positive {
  color: #10B981;
}

.comment-toggle.negative {
  color: #EF4444;
}

.comment-toggle.neutral {
  color: #64748b;
}

.comment-toggle:hover {
  background: #e9ecef;
}

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
  transition: all 0.2s;
}

.page-btn:hover:not(:disabled) {
  background: #e0e0e0;
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

.empty-sentiment span {
  font-size: 48px;
  display: block;
  margin-bottom: 12px;
}

.empty-sentiment p {
  color: #64748b;
}

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
  margin-bottom: 24px;
}

.btn-primary {
  padding: 10px 24px;
  background: #6366f1;
  color: white;
  border: none;
  border-radius: 40px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary:hover:not(:disabled) {
  background: #4f46e5;
  transform: translateY(-1px);
}

.btn-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-large {
  padding: 14px 32px;
  font-size: 16px;
}
</style>