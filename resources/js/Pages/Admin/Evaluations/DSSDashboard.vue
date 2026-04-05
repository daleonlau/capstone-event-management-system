<template>
    <div class="dss-dashboard">
      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading dashboard data...</p>
      </div>
  
      <!-- Error State -->
      <div v-else-if="error" class="error-state">
        <p class="error-message">{{ error }}</p>
        <button @click="fetchDashboardData" class="retry-btn">Retry</button>
      </div>
  
      <!-- Dashboard Content -->
      <div v-else class="dashboard-content">
        <!-- Executive Summary Card -->
        <div class="executive-card" :style="{ borderTopColor: executiveSummary.status_color }">
          <h2>📊 Executive Dashboard</h2>
          <div class="kpi-grid">
            <div class="kpi">
              <span class="label">Overall Score</span>
              <span class="value" :style="{ color: executiveSummary.status_color }">
                {{ overallMetrics.score }}/5.0
              </span>
              <span class="interpretation">{{ overallMetrics.interpretation }}</span>
              <span class="verbal">{{ overallMetrics.verbal }}</span>
            </div>
            <div class="kpi">
              <span class="label">Success Probability</span>
              <span class="value">{{ overallMetrics.success_probability }}%</span>
              <div class="progress-bar">
                <div class="progress" :style="{ width: overallMetrics.success_probability + '%', backgroundColor: executiveSummary.status_color }"></div>
              </div>
            </div>
            <div class="kpi">
              <span class="label">Trend</span>
              <span class="value trend" :class="executiveSummary.trend">
                {{ getTrendIcon(executiveSummary.trend) }} {{ executiveSummary.trend_percentage }}%
              </span>
              <span class="small">vs previous period</span>
            </div>
            <div class="kpi">
              <span class="label">Response Rate</span>
              <span class="value">{{ executiveSummary.total_respondents }} respondents</span>
              <span class="small">total participants</span>
            </div>
          </div>
  
          <!-- Benchmark Comparison -->
          <div class="benchmark" v-if="executiveSummary.vs_benchmark">
            <span>📈 {{ executiveSummary.vs_benchmark.ranking }} | </span>
            <span>{{ Math.abs(executiveSummary.vs_benchmark.difference) }} points {{ executiveSummary.vs_benchmark.difference >= 0 ? 'above' : 'below' }} industry average</span>
          </div>
        </div>
  
        <!-- Gauge Visualization -->
        <div class="gauge-container">
          <div class="gauge">
            <div class="gauge-value" :style="gaugeStyle">
              <span class="gauge-number">{{ overallMetrics.score }}</span>
            </div>
            <div class="gauge-labels">
              <span>Very Poor</span>
              <span>Poor</span>
              <span>Satisfactory</span>
              <span>Very Satisfactory</span>
              <span>Outstanding</span>
            </div>
          </div>
        </div>
  
        <!-- Priority Matrix -->
        <div class="priority-matrix">
          <h3>🎯 Priority Action Matrix</h3>
          <div class="matrix-grid">
            <div class="quadrant critical">
              <h4>🔴 CRITICAL (Act Now)</h4>
              <div v-for="item in priorityMatrix.critical" :key="item.category" class="matrix-item">
                <strong>{{ item.category }}</strong>
                <span class="score-badge" :style="{ backgroundColor: getScoreColor(item.score) }">{{ item.score }}/5</span>
              </div>
              <div v-if="priorityMatrix.critical.length === 0" class="empty">None - Good job!</div>
            </div>
            <div class="quadrant important">
              <h4>🟠 IMPORTANT (Plan)</h4>
              <div v-for="item in priorityMatrix.important" :key="item.category" class="matrix-item">
                <strong>{{ item.category }}</strong>
                <span class="score-badge" :style="{ backgroundColor: getScoreColor(item.score) }">{{ item.score }}/5</span>
              </div>
              <div v-if="priorityMatrix.important.length === 0" class="empty">None</div>
            </div>
            <div class="quadrant urgent">
              <h4>🟡 URGENT (Quick Wins)</h4>
              <div v-for="item in priorityMatrix.urgent" :key="item.category" class="matrix-item">
                <strong>{{ item.category }}</strong>
                <span class="score-badge" :style="{ backgroundColor: getScoreColor(item.score) }">{{ item.score }}/5</span>
              </div>
              <div v-if="priorityMatrix.urgent.length === 0" class="empty">None</div>
            </div>
            <div class="quadrant monitor">
              <h4>🟢 MONITOR (Maintain)</h4>
              <div v-for="item in priorityMatrix.monitor" :key="item.category" class="matrix-item">
                <strong>{{ item.category }}</strong>
                <span class="score-badge" :style="{ backgroundColor: getScoreColor(item.score) }">{{ item.score }}/5</span>
              </div>
              <div v-if="priorityMatrix.monitor.length === 0" class="empty">None</div>
            </div>
          </div>
        </div>
  
        <!-- Category Analysis Cards -->
        <h3>📋 Category Performance Analysis</h3>
        <div class="categories-grid">
          <div v-for="(category, name) in categoryAnalysis" :key="name" 
               class="category-card" :class="category.effort_level.toLowerCase().replace('-', '')">
            <div class="category-header">
              <span class="icon">{{ category.effort_icon }}</span>
              <h4>{{ name }}</h4>
              <span class="score-badge" :style="{ backgroundColor: getScoreColor(category.score) }">
                {{ category.score }}/5.0
              </span>
            </div>
            <div class="interpretation-badge" :class="getInterpretationClass(category.interpretation)">
              {{ category.interpretation }}
            </div>
            <div class="progress-section">
              <div class="progress-bar">
                <div class="progress" :style="{ width: category.percentage + '%', backgroundColor: getScoreColor(category.score) }"></div>
              </div>
              <div class="progress-labels">
                <span>Poor</span>
                <span>Satisfactory</span>
                <span>Very Satisfactory</span>
                <span>Outstanding</span>
              </div>
            </div>
            <div class="metrics-grid">
              <div class="metric">
                <span class="metric-label">Effort:</span>
                <strong :class="getEffortClass(category.effort_level)">{{ category.effort_level }}</strong>
              </div>
              <div class="metric">
                <span class="metric-label">ROI:</span>
                <strong class="text-green">{{ category.roi_potential.percentage }}%</strong>
              </div>
              <div class="metric">
                <span class="metric-label">Time:</span>
                <strong>{{ category.time_to_improve }}</strong>
              </div>
              <div class="metric">
                <span class="metric-label">Priority:</span>
                <strong :class="getPriorityClass(category.priority_score)">{{ category.priority_score }}%</strong>
              </div>
            </div>
            <div class="gap-info" v-if="category.gap_to_next_level > 0">
              Need {{ category.gap_to_next_level }} points to reach {{ category.next_level_target === 3.50 ? 'Very Satisfactory' : 'Outstanding' }}
            </div>
            <button @click="showQuickWins(name, category.quick_wins)" class="quick-wins-btn">
              ⚡ Quick Wins ({{ category.quick_wins.length }})
            </button>
          </div>
        </div>
  
        <!-- Improvement Potential -->
        <div class="improvement-card">
          <h3>🚀 Improvement Potential Analysis</h3>
          <div class="potential-gauge">
            <div class="current">
              <span class="label">Current</span>
              <span class="value">{{ improvementPotential.current_score }}/5.0</span>
              <span class="interpretation">{{ getInterpretationFromScore(improvementPotential.current_score) }}</span>
            </div>
            <div class="arrow">→</div>
            <div class="potential">
              <span class="label">Potential</span>
              <span class="value">{{ improvementPotential.potential_score }}/5.0</span>
              <span class="interpretation">{{ improvementPotential.new_interpretation }}</span>
            </div>
            <div class="gain">
              <span class="gain-value">+{{ improvementPotential.potential_gain }}</span>
              <span class="gain-label">possible improvement</span>
            </div>
          </div>
          <div class="improvement-breakdown" v-if="improvementPotential.breakdown.length">
            <h4>Focus Areas for Maximum Impact:</h4>
            <div v-for="item in improvementPotential.breakdown" :key="item.category" class="breakdown-item">
              <span class="category">{{ item.category }}</span>
              <div class="gap-bar">
                <div class="gap-fill" :style="{ width: (item.gap / 2.5 * 100) + '%' }"></div>
              </div>
              <span class="gap-text">{{ item.current }} → {{ item.target }} (+{{ item.gap }})</span>
            </div>
          </div>
        </div>
  
        <!-- Smart Recommendations -->
        <div class="recommendations-section">
          <h3>💡 AI-Powered Recommendations</h3>
          <div v-for="(rec, idx) in recommendations" :key="idx" 
               class="recommendation-card" :class="rec.type">
            <div class="rec-header">
              <span class="rec-title">{{ rec.title }}</span>
              <span class="roi-badge" v-if="rec.roi">ROI: {{ rec.roi }}%</span>
            </div>
            <p class="rec-description">{{ rec.description }}</p>
            <div class="rec-meta" v-if="rec.timeframe">
              <span>⏱️ {{ rec.timeframe }}</span>
              <span>🎯 Target: +{{ rec.gap }} points</span>
              <span v-if="rec.effort">💪 Effort: {{ rec.effort }}</span>
            </div>
            <div class="action-items" v-if="rec.action_items">
              <strong>Quick Actions:</strong>
              <ul>
                <li v-for="action in rec.action_items.slice(0, 3)" :key="action">{{ action }}</li>
              </ul>
            </div>
            <div class="rec-footer" v-if="rec.expected_impact">
              <span class="impact-badge">Expected Impact: {{ rec.expected_impact }}</span>
            </div>
          </div>
        </div>
  
        <!-- Progress Tracking -->
        <div class="progress-tracking" v-if="progressTracking.history.length > 1">
          <h3>📈 Historical Performance Trend</h3>
          <div class="trend-chart">
            <div class="trend-line">
              <div v-for="(point, idx) in progressTracking.history" :key="idx" class="trend-point" 
                   :style="{ left: (idx / (progressTracking.history.length - 1)) * 100 + '%', bottom: ((point.score - 1) / 4) * 100 + '%' }">
                <div class="point-tooltip">
                  <div class="date">{{ formatDate(point.date) }}</div>
                  <div class="score">{{ point.score }}/5.0</div>
                  <div class="interpretation">{{ point.interpretation }}</div>
                </div>
              </div>
              <div class="trend-line-path" :style="{ height: ((progressTracking.history[progressTracking.history.length - 1]?.score - 1) / 4 * 100) + '%' }"></div>
            </div>
          </div>
          <div class="trend-stats">
            <div class="stat">
              <span>Moving Average (3 events):</span>
              <strong>{{ progressTracking.moving_average }}/5.0</strong>
            </div>
            <div class="stat">
              <span>Consistency Score:</span>
              <strong>{{ progressTracking.consistency_score }}%</strong>
            </div>
            <div class="stat" v-if="progressTracking.prediction.score">
              <span>Next Event Prediction:</span>
              <strong>{{ progressTracking.prediction.score }}/5.0 ({{ progressTracking.prediction.interpretation }})</strong>
              <span class="confidence" :class="progressTracking.prediction.confidence">
                {{ progressTracking.prediction.confidence }} confidence
              </span>
            </div>
          </div>
        </div>
  
        <!-- Risk Assessment -->
        <div class="risk-assessment" v-if="riskAssessment.length">
          <h3>⚠️ Risk Assessment</h3>
          <div v-for="risk in riskAssessment" :key="risk.area" class="risk-card" :class="risk.level.toLowerCase()">
            <div class="risk-header">
              <span class="risk-level">{{ risk.level }} Risk</span>
              <span class="risk-area">{{ risk.area }}</span>
            </div>
            <p class="risk-impact">{{ risk.impact }}</p>
            <p class="risk-mitigation">Mitigation: {{ risk.mitigation }}</p>
          </div>
        </div>
      </div>
  
      <!-- Quick Wins Modal -->
      <div v-if="showModal" class="modal" @click.self="closeModal">
        <div class="modal-content">
          <div class="modal-header">
            <h3>⚡ Quick Wins for {{ selectedCategory }}</h3>
            <button @click="closeModal" class="close-btn">&times;</button>
          </div>
          <div class="modal-body">
            <ul>
              <li v-for="(win, idx) in selectedQuickWins" :key="idx">{{ win }}</li>
            </ul>
          </div>
          <div class="modal-footer">
            <button @click="closeModal" class="btn-secondary">Close</button>
          </div>
        </div>
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
    eventDate: {
      type: String,
      default: null
    }
  });
  
  const loading = ref(true);
  const error = ref(null);
  const dashboardData = ref(null);
  const showModal = ref(false);
  const selectedCategory = ref('');
  const selectedQuickWins = ref([]);
  
  const executiveSummary = computed(() => dashboardData.value?.executive_summary || {});
  const overallMetrics = computed(() => dashboardData.value?.overall_metrics || {});
  const categoryAnalysis = computed(() => dashboardData.value?.category_analysis || {});
  const priorityMatrix = computed(() => dashboardData.value?.priority_matrix || { critical: [], important: [], urgent: [], monitor: [] });
  const recommendations = computed(() => dashboardData.value?.recommendations || []);
  const improvementPotential = computed(() => dashboardData.value?.improvement_potential || {});
  const progressTracking = computed(() => dashboardData.value?.progress_tracking || {});
  const riskAssessment = computed(() => dashboardData.value?.risk_assessment || []);
  
  const gaugeStyle = computed(() => {
    const score = overallMetrics.value.score || 0;
    const percentage = (score / 5) * 100;
    return {
      transform: `rotate(${percentage * 1.8}deg)`
    };
  });
  
  const fetchDashboardData = async () => {
    loading.value = true;
    error.value = null;
    
    try {
      const url = `/admin/evaluations/${props.evaluationId}/dss-dashboard`;
      const params = props.eventDate ? { event_date: props.eventDate } : {};
      const response = await axios.get(url, { params });
      dashboardData.value = response.data;
    } catch (err) {
      console.error('Failed to fetch dashboard data:', err);
      error.value = err.response?.data?.error || 'Failed to load dashboard data';
    } finally {
      loading.value = false;
    }
  };
  
  const getScoreColor = (score) => {
    if (score >= 4.5) return '#10B981';
    if (score >= 3.5) return '#34D399';
    if (score >= 2.5) return '#FBBF24';
    if (score >= 1.5) return '#F97316';
    return '#EF4444';
  };
  
  const getInterpretationClass = (interpretation) => {
    const classes = {
      'Outstanding': 'outstanding',
      'Very Satisfactory': 'very-satisfactory',
      'Satisfactory': 'satisfactory',
      'Poor': 'poor',
      'Very Poor': 'very-poor'
    };
    return classes[interpretation] || 'satisfactory';
  };
  
  const getEffortClass = (effort) => {
    const classes = {
      'Low': 'text-green',
      'Medium-Low': 'text-light-green',
      'Medium': 'text-yellow',
      'Medium-High': 'text-orange',
      'High': 'text-red'
    };
    return classes[effort] || '';
  };
  
  const getPriorityClass = (priority) => {
    if (priority >= 80) return 'text-red';
    if (priority >= 60) return 'text-orange';
    if (priority >= 40) return 'text-yellow';
    return 'text-green';
  };
  
  const getTrendIcon = (trend) => {
    if (trend === 'improving') return '📈';
    if (trend === 'declining') return '📉';
    return '➡️';
  };
  
  const getInterpretationFromScore = (score) => {
    if (score >= 4.5) return 'Outstanding';
    if (score >= 3.5) return 'Very Satisfactory';
    if (score >= 2.5) return 'Satisfactory';
    if (score >= 1.5) return 'Poor';
    return 'Very Poor';
  };
  
  const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString();
  };
  
  const showQuickWins = (category, wins) => {
    selectedCategory.value = category;
    selectedQuickWins.value = wins;
    showModal.value = true;
  };
  
  const closeModal = () => {
    showModal.value = false;
    selectedCategory.value = '';
    selectedQuickWins.value = [];
  };
  
  onMounted(() => {
    fetchDashboardData();
  });
  </script>
  
  <style scoped>
  .dss-dashboard {
    padding: 24px;
    font-family: system-ui, -apple-system, sans-serif;
    background: #f3f4f6;
    min-height: 100vh;
  }
  
  .loading-state, .error-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 400px;
    background: white;
    border-radius: 16px;
    padding: 48px;
  }
  
  .spinner {
    width: 48px;
    height: 48px;
    border: 4px solid #e5e7eb;
    border-top-color: #6366f1;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    to { transform: rotate(360deg); }
  }
  
  .error-message {
    color: #ef4444;
    margin-bottom: 16px;
  }
  
  .retry-btn {
    padding: 8px 24px;
    background: #6366f1;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
  }
  
  .executive-card {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    border-radius: 20px;
    padding: 28px;
    margin-bottom: 24px;
    color: white;
    border-top-width: 4px;
    border-top-style: solid;
  }
  
  .executive-card h2 {
    margin: 0 0 20px 0;
    font-size: 24px;
  }
  
  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
  }
  
  .kpi {
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 16px;
  }
  
  .kpi .label {
    font-size: 12px;
    opacity: 0.8;
    display: block;
    margin-bottom: 8px;
  }
  
  .kpi .value {
    font-size: 32px;
    font-weight: bold;
    display: block;
  }
  
  .kpi .interpretation {
    font-size: 14px;
    font-weight: 500;
    display: block;
    margin-top: 4px;
  }
  
  .kpi .verbal {
    font-size: 12px;
    opacity: 0.7;
    display: block;
  }
  
  .kpi .small {
    font-size: 11px;
    opacity: 0.7;
  }
  
  .progress-bar {
    background: rgba(255,255,255,0.2);
    border-radius: 10px;
    height: 8px;
    margin-top: 12px;
    overflow: hidden;
  }
  
  .progress {
    height: 100%;
    border-radius: 10px;
    transition: width 0.3s ease;
  }
  
  .trend.improving { color: #10B981; }
  .trend.declining { color: #EF4444; }
  
  .benchmark {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid rgba(255,255,255,0.1);
    font-size: 13px;
    opacity: 0.8;
  }
  
  .gauge-container {
    background: white;
    border-radius: 20px;
    padding: 32px;
    margin-bottom: 24px;
    text-align: center;
  }
  
  .gauge {
    position: relative;
    width: 200px;
    height: 100px;
    margin: 0 auto;
    overflow: hidden;
  }
  
  .gauge-value {
    position: absolute;
    bottom: 0;
    left: 50%;
    transform-origin: bottom center;
    width: 4px;
    height: 80px;
    background: #6366f1;
    border-radius: 4px;
    transition: transform 0.5s ease;
  }
  
  .gauge-number {
    position: absolute;
    top: -30px;
    left: -20px;
    font-size: 24px;
    font-weight: bold;
    color: #1f2937;
    width: 40px;
    text-align: center;
  }
  
  .gauge-labels {
    display: flex;
    justify-content: space-between;
    margin-top: 60px;
    font-size: 10px;
    color: #6b7280;
  }
  
  .priority-matrix {
    background: white;
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 24px;
  }
  
  .priority-matrix h3 {
    margin: 0 0 20px 0;
  }
  
  .matrix-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
  }
  
  .quadrant {
    padding: 16px;
    border-radius: 12px;
    min-height: 180px;
  }
  
  .quadrant.critical { background: #FEE2E2; border-left: 4px solid #EF4444; }
  .quadrant.important { background: #FEF3C7; border-left: 4px solid #F59E0B; }
  .quadrant.urgent { background: #FEF9C3; border-left: 4px solid #EAB308; }
  .quadrant.monitor { background: #D1FAE5; border-left: 4px solid #10B981; }
  
  .quadrant h4 {
    margin: 0 0 12px 0;
    font-size: 14px;
  }
  
  .matrix-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    font-size: 13px;
  }
  
  .empty {
    color: #9ca3af;
    font-style: italic;
    font-size: 12px;
    padding: 8px 0;
  }
  
  .categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
  }
  
  .category-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
  }
  
  .category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
  
  .category-card.high { border-top: 4px solid #EF4444; }
  .category-card.mediumhigh { border-top: 4px solid #F97316; }
  .category-card.medium { border-top: 4px solid #FBBF24; }
  .category-card.mediumlow { border-top: 4px solid #34D399; }
  .category-card.low { border-top: 4px solid #10B981; }
  
  .category-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
  }
  
  .category-header .icon {
    font-size: 24px;
  }
  
  .category-header h4 {
    flex: 1;
    margin: 0;
    font-size: 16px;
  }
  
  .score-badge {
    padding: 4px 12px;
    border-radius: 20px;
    color: white;
    font-weight: bold;
    font-size: 14px;
  }
  
  .interpretation-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 16px;
  }
  
  .interpretation-badge.outstanding { background: #D1FAE5; color: #065F46; }
  .interpretation-badge.very-satisfactory { background: #D1FAE5; color: #065F46; }
  .interpretation-badge.satisfactory { background: #FEF3C7; color: #92400E; }
  .interpretation-badge.poor { background: #FEE2E2; color: #991B1B; }
  .interpretation-badge.very-poor { background: #FEE2E2; color: #991B1B; }
  
  .progress-section {
    margin: 16px 0;
  }
  
  .progress-labels {
    display: flex;
    justify-content: space-between;
    font-size: 9px;
    color: #9ca3af;
    margin-top: 4px;
  }
  
  .metrics-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin: 16px 0;
    padding: 12px;
    background: #f9fafb;
    border-radius: 12px;
  }
  
  .metric {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
  }
  
  .metric-label {
    color: #6b7280;
  }
  
  .gap-info {
    font-size: 12px;
    color: #f59e0b;
    margin: 12px 0;
    padding: 8px;
    background: #FEF3C7;
    border-radius: 8px;
  }
  
  .quick-wins-btn {
    width: 100%;
    padding: 10px;
    background: #6366f1;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    transition: background 0.2s;
  }
  
  .quick-wins-btn:hover {
    background: #4f46e5;
  }
  
  .improvement-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 24px;
    color: white;
  }
  
  .improvement-card h3 {
    margin: 0 0 20px 0;
  }
  
  .potential-gauge {
    display: flex;
    align-items: center;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 24px;
  }
  
  .current, .potential {
    text-align: center;
  }
  
  .current .label, .potential .label {
    font-size: 12px;
    opacity: 0.8;
    display: block;
  }
  
  .current .value, .potential .value {
    font-size: 36px;
    font-weight: bold;
    display: block;
  }
  
  .arrow {
    font-size: 32px;
    opacity: 0.8;
  }
  
  .gain {
    text-align: center;
  }
  
  .gain-value {
    font-size: 28px;
    font-weight: bold;
    display: block;
    color: #fbbf24;
  }
  
  .gain-label {
    font-size: 12px;
    opacity: 0.8;
  }
  
  .improvement-breakdown {
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 16px;
  }
  
  .improvement-breakdown h4 {
    margin: 0 0 12px 0;
    font-size: 14px;
  }
  
  .breakdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    font-size: 13px;
  }
  
  .breakdown-item .category {
    width: 120px;
  }
  
  .gap-bar {
    flex: 1;
    height: 8px;
    background: rgba(255,255,255,0.2);
    border-radius: 4px;
    overflow: hidden;
  }
  
  .gap-fill {
    height: 100%;
    background: #fbbf24;
    border-radius: 4px;
    transition: width 0.3s;
  }
  
  .gap-text {
    font-size: 11px;
    opacity: 0.8;
    min-width: 80px;
  }
  
  .recommendations-section {
    background: white;
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 24px;
  }
  
  .recommendations-section h3 {
    margin: 0 0 20px 0;
  }
  
  .recommendation-card {
    background: #f9fafb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    border-left: 4px solid;
  }
  
  .recommendation-card.critical { border-left-color: #EF4444; background: #FEF2F2; }
  .recommendation-card.quick_win { border-left-color: #10B981; background: #F0FDF4; }
  .recommendation-card.strategic { border-left-color: #3B82F6; background: #EFF6FF; }
  
  .rec-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
  }
  
  .rec-title {
    font-weight: bold;
    font-size: 16px;
  }
  
  .roi-badge {
    background: #f59e0b;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
  }
  
  .rec-description {
    color: #4b5563;
    margin-bottom: 12px;
    line-height: 1.5;
  }
  
  .rec-meta {
    display: flex;
    gap: 16px;
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 12px;
  }
  
  .action-items {
    margin-top: 12px;
  }
  
  .action-items strong {
    font-size: 13px;
    display: block;
    margin-bottom: 8px;
  }
  
  .action-items ul {
    margin: 0;
    padding-left: 20px;
  }
  
  .action-items li {
    font-size: 13px;
    color: #4b5563;
    margin: 6px 0;
  }
  
  .rec-footer {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
  }
  
  .impact-badge {
    font-size: 12px;
    color: #059669;
    font-weight: 500;
  }
  
  .progress-tracking {
    background: white;
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 24px;
  }
  
  .progress-tracking h3 {
    margin: 0 0 20px 0;
  }
  
  .trend-chart {
    position: relative;
    height: 300px;
    margin-bottom: 24px;
    background: linear-gradient(to top, #f3f4f6 0%, white 100%);
    border-radius: 12px;
    padding: 20px;
  }
  
  .trend-line {
    position: relative;
    height: 100%;
  }
  
  .trend-point {
    position: absolute;
    width: 12px;
    height: 12px;
    background: #6366f1;
    border-radius: 50%;
    cursor: pointer;
    transition: transform 0.2s;
    transform: translate(-50%, 50%);
  }
  
  .trend-point:hover {
    transform: translate(-50%, 50%) scale(1.5);
  }
  
  .point-tooltip {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #1f2937;
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 11px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s;
    z-index: 10;
  }
  
  .trend-point:hover .point-tooltip {
    opacity: 1;
  }
  
  .trend-stats {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 16px;
    padding: 16px;
    background: #f9fafb;
    border-radius: 12px;
  }
  
  .stat {
    text-align: center;
  }
  
  .stat span {
    font-size: 12px;
    color: #6b7280;
    display: block;
    margin-bottom: 4px;
  }
  
  .stat strong {
    font-size: 18px;
    color: #1f2937;
  }
  
  .confidence {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: 8px;
  }
  
  .confidence.high { background: #D1FAE5; color: #065F46; }
  .confidence.medium { background: #FEF3C7; color: #92400E; }
  .confidence.low { background: #FEE2E2; color: #991B1B; }
  
  .risk-assessment {
    background: white;
    border-radius: 20px;
    padding: 24px;
  }
  
  .risk-assessment h3 {
    margin: 0 0 20px 0;
  }
  
  .risk-card {
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 12px;
  }
  
  .risk-card.critical { background: #FEF2F2; border-left: 4px solid #EF4444; }
  .risk-card.high { background: #FEF3C7; border-left: 4px solid #F59E0B; }
  .risk-card.medium { background: #FEF9C3; border-left: 4px solid #EAB308; }
  
  .risk-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
  }
  
  .risk-level {
    font-weight: bold;
    font-size: 13px;
  }
  
  .risk-card.critical .risk-level { color: #EF4444; }
  .risk-card.high .risk-level { color: #F59E0B; }
  
  .risk-area {
    font-weight: 500;
  }
  
  .risk-impact, .risk-mitigation {
    font-size: 13px;
    margin: 8px 0;
  }
  
  .risk-mitigation {
    color: #059669;
  }
  
  .text-green { color: #10B981; }
  .text-light-green { color: #34D399; }
  .text-yellow { color: #FBBF24; }
  .text-orange { color: #F97316; }
  .text-red { color: #EF4444; }
  
  /* Modal Styles */
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
  }
  
  .modal-content {
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 500px;
    max-height: 80vh;
    overflow: auto;
  }
  
  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
  }
  
  .modal-header h3 {
    margin: 0;
  }
  
  .close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #9ca3af;
  }
  
  .modal-body {
    padding: 20px;
  }
  
  .modal-body ul {
    margin: 0;
    padding-left: 20px;
  }
  
  .modal-body li {
    margin: 12px 0;
    line-height: 1.5;
  }
  
  .modal-footer {
    padding: 16px 20px;
    border-top: 1px solid #e5e7eb;
    text-align: right;
  }
  
  .btn-secondary {
    padding: 8px 20px;
    background: #e5e7eb;
    border: none;
    border-radius: 8px;
    cursor: pointer;
  }
  
  @media (max-width: 768px) {
    .dss-dashboard {
      padding: 12px;
    }
    
    .kpi-grid {
      grid-template-columns: 1fr;
    }
    
    .matrix-grid {
      grid-template-columns: 1fr;
    }
    
    .categories-grid {
      grid-template-columns: 1fr;
    }
    
    .potential-gauge {
      flex-direction: column;
    }
    
    .arrow {
      transform: rotate(90deg);
    }
  }
  </style>