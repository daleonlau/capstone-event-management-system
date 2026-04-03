"""
app/main.py - EventFlow AI Service v8.0
Implements exactly as described in the capstone document:
  - Logistic Regression (scikit-learn) for satisfaction score prediction from category ratings
  - Logistic Regression + TF-IDF NLP for sentiment classification of comments
  - Keyword-based fallback when models are unavailable
  - What-if analysis, low-scoring question detection, prioritized recommendations
"""

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
import numpy as np
import pandas as pd
import joblib
import os
from typing import List, Dict, Any, Optional, Tuple
from datetime import datetime
import logging
import re
import string
import json
import mysql.connector
from mysql.connector import Error
from collections import defaultdict, Counter

# ==================== Logging ====================
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

# ==================== FastAPI App ====================
app = FastAPI(
    title="EventFlow AI Service",
    description="Logistic Regression + NLP Sentiment Analysis for Event Evaluation",
    version="8.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ==================== Database ====================
DB_CONFIG = {
    'host': os.getenv('DB_HOST', '127.0.0.1'),
    'database': os.getenv('DB_DATABASE', 'Capstone_Project_EventFlow'),
    'user': os.getenv('DB_USERNAME', 'root'),
    'password': os.getenv('DB_PASSWORD', ''),
    'port': int(os.getenv('DB_PORT', 3306))
}

def get_db_connection():
    try:
        return mysql.connector.connect(**DB_CONFIG)
    except Error as e:
        logger.error(f"DB connection error: {e}")
        return None

# ==================== Category Configuration ====================

# Ordered list of categories — order MUST match logistic model training features
CATEGORY_ORDER = [
    "I. Information Dissemination",
    "II. Design of the Event",
    "III. Outcomes of the Event",
    "IV. Secretariat",
    "V. Facilities",
    "VI. Food",
    "VII. Resource Speaker",
    "VIII. Traffic Management",
]

CATEGORY_WEIGHTS = {
    "I. Information Dissemination": 0.18,
    "II. Design of the Event": 0.15,
    "III. Outcomes of the Event": 0.12,
    "IV. Secretariat": 0.22,
    "V. Facilities": 0.16,
    "VI. Food": 0.12,
    "VII. Resource Speaker": 0.05,
    "VIII. Traffic Management": 0.05,
}

CATEGORY_DESCRIPTIONS = {
    "I. Information Dissemination": "How well information was shared before and during the event",
    "II. Design of the Event": "The quality of program design and time management",
    "III. Outcomes of the Event": "The results and impact of the event on attendees",
    "IV. Secretariat": "The performance of the organizing team",
    "V. Facilities": "The quality of venue and equipment",
    "VI. Food": "The quality and service of food provided",
    "VII. Resource Speaker": "The effectiveness and expertise of the resource speaker",
    "VIII. Traffic Management": "The organization of traffic flow and safety",
}

# Keyword lists for sentiment fallback (as described in the document)
POSITIVE_KEYWORDS = [
    'good', 'great', 'excellent', 'awesome', 'nice', 'love', 'perfect', 'fantastic',
    'wonderful', 'amazing', 'helpful', 'clear', 'organized', 'satisfied', 'happy',
    'enjoyed', 'informative', 'well', 'best', 'outstanding', 'impressive', 'valuable',
    'recommend', 'superb', 'exceptional', 'brilliant', 'pleased', 'fun', 'exciting',
    'thrilling', 'epic', 'intense', 'well-organized', 'well-planned', 'smooth',
    'efficient', 'professional', 'knowledgeable', 'engaging', 'interactive',
    'entertaining', 'memorable', 'incredible', 'spectacular',
]

NEGATIVE_KEYWORDS = [
    'bad', 'poor', 'terrible', 'awful', 'worst', 'disappointed', 'waste', 'horrible',
    'slow', 'boring', 'confusing', 'unclear', 'disorganized', 'unhelpful', 'rude',
    'unsatisfied', 'lacking', 'insufficient', 'delay', 'problem', 'issue', 'complaint',
    'difficult', 'frustrating', 'annoying', 'disappointing', 'dissatisfied', 'cold',
    'not enough', 'long lines', 'broken', 'damaged', 'old', 'worn out', 'torn',
    'dangerous', 'unsafe',
]

# ==================== Dynamic Category Manager ====================

class CategoryManager:
    """Dynamically loads question-to-category mapping from database"""

    def __init__(self):
        self.question_to_category = {}
        self.question_to_text = {}
        self.category_questions = defaultdict(list)
        self.last_updated = None
        self.load_mapping()

    def load_mapping(self):
        connection = get_db_connection()
        if not connection:
            logger.warning("⚠️ Cannot connect to DB for category mapping")
            return
        try:
            cursor = connection.cursor(dictionary=True)
            cursor.execute("""
                SELECT eq.id, eq.question_text, ec.category_name
                FROM evaluation_questions eq
                LEFT JOIN evaluation_categories ec ON eq.category_id = ec.id
                WHERE eq.question_type = 'likert'
                ORDER BY eq.id
            """)
            rows = cursor.fetchall()
            self.question_to_category.clear()
            self.question_to_text.clear()
            self.category_questions.clear()

            for row in rows:
                q_id = row['id']
                question_text = row['question_text']
                category = row['category_name']
                self.question_to_text[q_id] = question_text
                if category:
                    self.question_to_category[q_id] = category
                    self.category_questions[category].append(q_id)
                else:
                    inferred = self.infer_category(question_text) or "Other"
                    self.question_to_category[q_id] = inferred
                    self.category_questions[inferred].append(q_id)

            self.last_updated = datetime.now()
            logger.info(f"✅ Loaded {len(self.question_to_category)} questions into {len(self.category_questions)} categories")
        except Error as e:
            logger.error(f"Category mapping error: {e}")
        finally:
            if connection:
                connection.close()

    def infer_category(self, question_text: str) -> Optional[str]:
        t = question_text.lower()
        if any(w in t for w in ['timeliness', 'adequacy', 'information dissemination']):
            return "I. Information Dissemination"
        elif any(w in t for w in ['program', 'order', 'relevance', 'pacing', 'time allotment', 'design']):
            return "II. Design of the Event"
        elif any(w in t for w in ['attendance', 'participation', 'interaction', 'teamwork', 'outcomes']):
            return "III. Outcomes of the Event"
        elif any(w in t for w in ['sensitivity', 'management', 'provision', 'feedback', 'secretariat']):
            return "IV. Secretariat"
        elif any(w in t for w in ['appearance', 'cleanliness', 'orderliness', 'equipment', 'availability', 'functionality', 'facilities', 'venue']):
            return "V. Facilities"
        elif any(w in t for w in ['food', 'beverage', 'presentation', 'service', 'sufficiency', 'quantity']):
            return "VI. Food"
        elif 'speaker' in t:
            return "VII. Resource Speaker"
        elif 'traffic' in t:
            return "VIII. Traffic Management"
        return None

    def get_category_for_question(self, q_id: int) -> str:
        return self.question_to_category.get(q_id, "Other")

    def get_question_text(self, q_id: int) -> str:
        return self.question_to_text.get(q_id, f"Question {q_id}")

    def get_all_categories(self) -> List[str]:
        return list(self.category_questions.keys())

    def get_category_score(self, category: str, question_scores: Dict[int, float]) -> float:
        q_ids = self.category_questions.get(category, [])
        scores = [question_scores[q_id] for q_id in q_ids if q_id in question_scores and question_scores[q_id] > 0]
        return round(sum(scores) / len(scores), 2) if scores else 0


category_manager = CategoryManager()


# ==================== Model Manager ====================

class ModelManager:
    """
    Manages two Logistic Regression models as described in the capstone document:
    1. Sentiment model (LR + TF-IDF) — classifies comments as positive/negative/neutral
    2. Satisfaction model (LR) — predicts satisfaction score from category ratings
    """

    def __init__(self):
        # Sentiment LR model (NLP)
        self.sentiment_model = None
        self.vectorizer = None
        self.label_encoder = None
        self.sentiment_model_loaded = False

        # Satisfaction LR model
        self.satisfaction_model = None
        self.satisfaction_model_loaded = False

        self.load_models()

    def load_models(self):
        models_dir = os.path.join(os.path.dirname(__file__), 'models')
        os.makedirs(models_dir, exist_ok=True)

        # --- Load NLP Sentiment Model (Logistic Regression + TF-IDF) ---
        try:
            s_path = os.path.join(models_dir, 'sentiment_model.pkl')
            v_path = os.path.join(models_dir, 'vectorizer.pkl')
            e_path = os.path.join(models_dir, 'label_encoder.pkl')

            if all(os.path.exists(p) for p in [s_path, v_path, e_path]):
                self.sentiment_model = joblib.load(s_path)
                self.vectorizer = joblib.load(v_path)
                self.label_encoder = joblib.load(e_path)
                self.sentiment_model_loaded = True
                logger.info("✅ NLP Sentiment model (Logistic Regression) loaded successfully")
            else:
                logger.warning("⚠️ Sentiment model files not found — keyword fallback will be used")
        except Exception as e:
            logger.error(f"❌ Failed to load sentiment model: {e}")

        # --- Load Satisfaction Prediction Model (Logistic Regression) ---
        try:
            lm_path = os.path.join(models_dir, 'logistic_model.pkl')
            if os.path.exists(lm_path):
                self.satisfaction_model = joblib.load(lm_path)
                self.satisfaction_model_loaded = True
                logger.info("✅ Satisfaction model (Logistic Regression) loaded successfully")
            else:
                logger.warning("⚠️ Satisfaction model not found — weighted average fallback will be used")
        except Exception as e:
            logger.error(f"❌ Failed to load satisfaction model: {e}")

    @property
    def models_loaded(self) -> bool:
        return self.sentiment_model_loaded

    def preprocess_text(self, text: str) -> str:
        if not text or not isinstance(text, str):
            return ""
        text = text.lower()
        text = text.translate(str.maketrans('', '', string.punctuation))
        text = re.sub(r'\d+', '', text)
        return ' '.join(text.split())

    def classify_comment(self, comment: str) -> str:
        """
        Classify a single comment as 'positive', 'negative', or 'neutral'
        using the Logistic Regression NLP model.
        Falls back to keyword-based classification.
        """
        if self.sentiment_model_loaded:
            try:
                processed = self.preprocess_text(comment)
                if not processed:
                    return 'neutral'
                features = self.vectorizer.transform([processed])
                prediction = self.sentiment_model.predict(features)
                return str(self.label_encoder.inverse_transform(prediction)[0]).lower()
            except Exception as e:
                logger.warning(f"LR sentiment prediction failed, falling back to keywords: {e}")

        # Keyword-based fallback
        return self._keyword_classify(comment)

    def _keyword_classify(self, comment: str) -> str:
        comment_lower = comment.lower()
        pos = sum(1 for kw in POSITIVE_KEYWORDS if kw in comment_lower)
        neg = sum(1 for kw in NEGATIVE_KEYWORDS if kw in comment_lower)
        if pos > neg:
            return 'positive'
        elif neg > pos:
            return 'negative'
        # Tie-breaker
        if any(w in comment_lower for w in ['excellent', 'amazing', 'perfect', 'fantastic']):
            return 'positive'
        if any(w in comment_lower for w in ['disappointed', 'terrible', 'awful', 'poor']):
            return 'negative'
        return 'neutral'

    def predict_satisfaction(self, category_scores: Dict[str, float]) -> Tuple[float, float]:
        """
        Predict overall satisfaction score and success probability
        using Logistic Regression from category rating scores.
        Returns: (predicted_satisfaction: float 1–5, success_probability: float 0–1)
        """
        if self.satisfaction_model_loaded:
            try:
                # Build feature vector in the defined CATEGORY_ORDER
                feature_vector = np.array([
                    category_scores.get(cat, 0.0) for cat in CATEGORY_ORDER
                ]).reshape(1, -1)

                # Trim if model was trained with fewer features
                n_features = (
                    self.satisfaction_model.coef_.shape[1]
                    if hasattr(self.satisfaction_model, 'coef_')
                    else len(CATEGORY_ORDER)
                )
                feature_vector = feature_vector[:, :n_features]

                # Get prediction probabilities
                proba = self.satisfaction_model.predict_proba(feature_vector)[0]
                classes = list(self.satisfaction_model.classes_)

                # Identify positive/high-satisfaction class index
                pos_idx = -1
                for i, cls in enumerate(classes):
                    if str(cls).lower() in ['positive', 'satisfactory', '1', 'good', 'high', 'satisfied']:
                        pos_idx = i
                        break
                if pos_idx == -1:
                    pos_idx = len(classes) - 1  # assume last class is best

                success_probability = round(float(proba[pos_idx]), 2)
                # Convert probability to 1–5 scale
                predicted_satisfaction = round(1.0 + (success_probability * 4.0), 2)
                predicted_satisfaction = max(1.0, min(5.0, predicted_satisfaction))

                logger.info(
                    f"🤖 LR Satisfaction Prediction: {predicted_satisfaction}/5.0 "
                    f"| Success Probability: {success_probability}"
                )
                return predicted_satisfaction, success_probability

            except Exception as e:
                logger.warning(f"LR satisfaction prediction failed, using weighted average: {e}")

        # Weighted average fallback
        return self._weighted_average_fallback(category_scores)

    def _weighted_average_fallback(self, category_scores: Dict[str, float]) -> Tuple[float, float]:
        """Fallback: weighted average of category scores → satisfaction"""
        total_weight = 0.0
        weighted_sum = 0.0
        for cat, score in category_scores.items():
            if score > 0:
                w = CATEGORY_WEIGHTS.get(cat, 0.1)
                weighted_sum += score * w
                total_weight += w
        if total_weight == 0:
            return 2.5, 0.5
        satisfaction = round(min(5.0, max(1.0, weighted_sum / total_weight)), 2)
        success_prob = round((satisfaction - 1.0) / 4.0, 2)
        return satisfaction, success_prob

    def analyze_sentiment(self, comments: List[str]) -> Dict[str, Any]:
        """
        Perform NLP sentiment analysis on open-ended comments using
        the Logistic Regression model. Falls back to keyword classification.
        """
        method = "Logistic Regression NLP" if self.sentiment_model_loaded else "keyword-based"
        logger.info(f"🔍 Sentiment analysis [{method}] — {len(comments)} comments")

        if not comments:
            return {
                'positive_percentage': 0, 'negative_percentage': 0, 'neutral_percentage': 0,
                'sentiment_score': 0.5, 'total_comments': 0,
                'common_themes': [], 'positive_comments': [], 'negative_comments': [], 'neutral_comments': []
            }

        positive_comments, negative_comments, neutral_comments, sentiments = [], [], [], []

        for comment in comments:
            if not comment or not str(comment).strip():
                continue
            sentiment = self.classify_comment(str(comment))
            sentiments.append(sentiment)
            if sentiment == 'positive':
                positive_comments.append(comment)
            elif sentiment == 'negative':
                negative_comments.append(comment)
            else:
                neutral_comments.append(comment)

        total = len(sentiments)
        if total == 0:
            return {
                'positive_percentage': 0, 'negative_percentage': 0, 'neutral_percentage': 0,
                'sentiment_score': 0.5, 'total_comments': 0,
                'common_themes': [], 'positive_comments': [], 'negative_comments': [], 'neutral_comments': []
            }

        pos_count = sentiments.count('positive')
        neg_count = sentiments.count('negative')
        neu_count = sentiments.count('neutral')
        sentiment_score = round((pos_count + (neu_count * 0.5)) / total, 2)

        # Extract common themes
        all_words = ' '.join([c.lower() for c in comments if c]).split()
        stopwords = {
            'the','and','is','in','to','of','it','that','was','for','this','but','with',
            'as','are','be','at','from','by','an','on','have','has','were','had','been',
            'not','very','so','a','i','we','they','he','she','you','also',
        }
        word_freq = Counter(all_words)
        common_themes = [w for w, _ in word_freq.most_common(15) if w not in stopwords and len(w) > 2][:5]

        logger.info(
            f"✅ Sentiment results [{method}]: "
            f"{pos_count} positive, {neg_count} negative, {neu_count} neutral"
        )

        return {
            'positive_percentage': round((pos_count / total) * 100, 1),
            'negative_percentage': round((neg_count / total) * 100, 1),
            'neutral_percentage': round((neu_count / total) * 100, 1),
            'sentiment_score': sentiment_score,
            'total_comments': total,
            'common_themes': common_themes,
            'positive_comments': positive_comments,
            'negative_comments': negative_comments,
            'neutral_comments': neutral_comments,
        }


model_manager = ModelManager()


# ==================== Pydantic Models ====================

class EvaluationData(BaseModel):
    data: Dict[str, Any] = Field(default_factory=dict)
    year_level: Optional[int] = 1
    respondent_type: Optional[int] = 0
    positive_comments: Optional[List[str]] = []
    suggestion_comments: Optional[List[str]] = []
    total_respondents: Optional[int] = 0
    response_rate: Optional[float] = 0


class InsightResponse(BaseModel):
    summary: str
    analyzed_at: str
    predicted_satisfaction: float
    success_probability: float
    response_rate: float
    total_respondents: int
    category_breakdown: Dict[str, float]
    feature_importance: Dict[str, float]
    strengths: List[str]
    weaknesses: List[str]
    recommendations: List[Dict[str, Any]]
    sentiment_score: float
    positive_percentage: float
    negative_percentage: float
    neutral_percentage: float
    total_comments: int
    common_themes: List[str]
    positive_comments: List[str]
    negative_comments: List[str]
    neutral_comments: List[str]
    what_if_optimistic: Dict[str, Any]
    what_if_targeted: Dict[str, Any]
    low_scoring_questions: List[Dict[str, Any]]
    year_level_analysis: List[Dict[str, Any]]


# ==================== Analysis Helpers ====================

def extract_question_scores(data: Dict[str, Any]) -> Dict[int, float]:
    scores = {}
    for key, value in data.items():
        if not isinstance(value, (int, float)):
            continue
        if isinstance(key, str) and key.startswith('q_'):
            try:
                q_id = int(key.split('_')[1])
                scores[q_id] = float(value)
            except Exception:
                pass
    return scores


def calculate_category_scores(question_scores: Dict[int, float]) -> Dict[str, float]:
    category_scores = {}
    for category in category_manager.get_all_categories():
        score = category_manager.get_category_score(category, question_scores)
        if score > 0:
            category_scores[category] = score
    return category_scores


def identify_low_scoring_questions(question_scores: Dict[int, float]) -> List[Dict[str, Any]]:
    low = []
    for q_id, score in question_scores.items():
        if 0 < score < 3.5:
            status = "critical" if score < 3.0 else "needs_attention" if score < 3.3 else "borderline"
            low.append({
                'id': q_id,
                'text': category_manager.get_question_text(q_id),
                'score': score,
                'category': category_manager.get_category_for_question(q_id),
                'status': status,
            })
    low.sort(key=lambda x: x['score'])
    return low


def calculate_feature_importance(category_scores: Dict[str, float]) -> Dict[str, float]:
    return {
        cat: round(CATEGORY_WEIGHTS.get(cat, 0.1) * (score / 5) * 100, 1)
        for cat, score in category_scores.items() if score > 0
    }


def identify_strengths_weaknesses(
    category_scores: Dict[str, float],
    low_scoring_questions: List[Dict[str, Any]]
) -> Tuple[List[str], List[str]]:
    strengths, weaknesses = [], []
    for cat, score in category_scores.items():
        desc = CATEGORY_DESCRIPTIONS.get(cat, "")
        if score >= 4.0:
            strengths.append(f"✅ {cat}: {score}/5.0 - {desc}")
        elif score >= 3.5:
            strengths.append(f"📊 {cat}: {score}/5.0 - Good performance")
        elif score >= 3.0:
            weaknesses.append(f"⚠️ {cat}: {score}/5.0 - Needs attention")
        elif score > 0:
            weaknesses.append(f"❌ {cat}: {score}/5.0 - Critical area")
    for q in low_scoring_questions[:5]:
        icon = "❌" if q['status'] == 'critical' else "⚠️"
        weaknesses.append(f"{icon} {q['text']}: {q['score']}/5.0 - Needs improvement")
    return strengths, weaknesses


def generate_recommendations(
    category_scores: Dict[str, float],
    feature_importance: Dict[str, float],
    low_scoring_questions: List[Dict[str, Any]]
) -> List[Dict[str, Any]]:
    recs = []
    for q in low_scoring_questions[:5]:
        priority = "high" if q['score'] < 3.0 else "medium" if q['score'] < 3.3 else "low"
        tl = q['text'].lower()
        if 'food' in tl:
            action_items = ["Review portion sizes and increase quantity", "Conduct taste testing with students", "Improve food serving speed", "Gather specific menu feedback"]
        elif 'equipment' in tl or 'facilities' in tl or 'venue' in tl:
            action_items = ["Conduct pre-event equipment inspection", "Replace damaged or worn equipment", "Test all AV equipment before event", "Create regular maintenance schedule"]
        elif 'teamwork' in tl or 'secretariat' in tl:
            action_items = ["Organize team-building activities", "Assign clear roles and responsibilities", "Encourage collaboration through group activities", "Recognize and reward team contributions"]
        else:
            action_items = [
                f"Review current processes for: {q['text'][:50]}",
                "Gather specific feedback from participants",
                "Implement targeted improvements before next event",
                "Monitor and measure results in next evaluation",
            ]
        recs.append({
            'priority': priority,
            'category': q['category'],
            'title': f"📌 Improve: {q['text'][:60]}",
            'problem_statement': f"This aspect scored {q['score']}/5.0, below the target threshold of 3.5.",
            'action_items': action_items,
            'expected_outcome': f"Improving this area to 4.0+ will increase overall participant satisfaction.",
            'resources_needed': ["Participant feedback collection", "Implementation plan", "Follow-up monitoring"],
            'success_metrics': [f"Improve score from {q['score']} to 4.0+", "Positive feedback in next evaluation"],
        })
    return sorted(recs, key=lambda x: {'high': 0, 'medium': 1, 'low': 2}.get(x['priority'], 3))


def calculate_what_if_analysis(
    category_scores: Dict[str, float],
    feature_importance: Dict[str, float],
    low_scoring_questions: List[Dict[str, Any]]
) -> Tuple[Dict, Dict]:
    valid = [s for s in category_scores.values() if s > 0]
    current = round(np.mean(valid), 2) if valid else 2.5

    # Targeted: fix low-scoring questions
    targeted_gain = 0.0
    targeted_improvements = []
    for q in low_scoring_questions[:3]:
        target = min(4.0, q['score'] + 1.0)
        gain = round((target - q['score']) * 0.05, 2)
        targeted_gain += gain
        targeted_improvements.append({
            'question': q['text'][:50], 'category': q['category'],
            'from': q['score'], 'to': target, 'gain': gain
        })

    targeted = {
        'scenario': 'Focus on low-scoring questions',
        'current_satisfaction': current,
        'projected_satisfaction': round(min(5.0, current + targeted_gain), 2),
        'gain': round(targeted_gain, 2),
        'improvements': targeted_improvements,
    }

    # Optimistic: all categories to 4.0
    optimistic_gain = 0.0
    optimistic_improvements = []
    for cat, score in category_scores.items():
        if 0 < score < 4.0:
            gain = round((4.0 - score) * CATEGORY_WEIGHTS.get(cat, 0.1), 2)
            optimistic_gain += gain
            optimistic_improvements.append({'category': cat, 'from': score, 'to': 4.0, 'gain': gain})

    optimistic = {
        'scenario': 'Improve all categories to 4.0',
        'current_satisfaction': current,
        'projected_satisfaction': round(min(5.0, current + optimistic_gain), 2),
        'gain': round(optimistic_gain, 2),
        'improvements': optimistic_improvements,
    }

    return optimistic, targeted


# ==================== API Endpoints ====================

@app.get("/")
async def root():
    return {
        "service": "EventFlow AI Service",
        "version": "8.0.0",
        "status": "healthy",
        "ai_methods": {
            "satisfaction_prediction": "Logistic Regression (from category scores)" if model_manager.satisfaction_model_loaded else "Weighted average fallback",
            "sentiment_analysis": "Logistic Regression NLP (TF-IDF)" if model_manager.sentiment_model_loaded else "Keyword-based fallback",
        },
        "models": {
            "sentiment_model_loaded": model_manager.sentiment_model_loaded,
            "satisfaction_model_loaded": model_manager.satisfaction_model_loaded,
        },
        "database_connected": get_db_connection() is not None,
        "categories_loaded": len(category_manager.get_all_categories()),
        "questions_loaded": len(category_manager.question_to_text),
    }


@app.get("/health")
async def health_check():
    return {
        "status": "healthy",
        "timestamp": datetime.now().isoformat(),
        "sentiment_model_loaded": model_manager.sentiment_model_loaded,
        "satisfaction_model_loaded": model_manager.satisfaction_model_loaded,
        "database_connected": get_db_connection() is not None,
        "categories_loaded": len(category_manager.get_all_categories()),
    }


@app.post("/analyze", response_model=InsightResponse)
async def analyze_evaluation(evaluation: EvaluationData):
    """
    Main analysis endpoint.
    1. Extracts question scores and computes category averages
    2. Uses Logistic Regression to predict satisfaction score from category ratings
    3. Uses Logistic Regression NLP to classify open-ended comments sentiment
    4. Identifies low-scoring questions (below 3.5)
    5. Generates prioritized recommendations with action items
    6. Performs what-if analysis for projected improvement scenarios
    """
    logger.info(
        f"📥 Analysis request — {evaluation.total_respondents} respondents | "
        f"{len(evaluation.positive_comments or [])} positive comments | "
        f"{len(evaluation.suggestion_comments or [])} suggestion comments"
    )

    try:
        all_comments = list(evaluation.positive_comments or []) + list(evaluation.suggestion_comments or [])

        # Step 1: Extract and average question scores
        question_scores = extract_question_scores(evaluation.data)
        logger.info(f"📊 Extracted {len(question_scores)} question scores")

        # Step 2: Calculate category scores (averages per evaluation dimension)
        category_scores = calculate_category_scores(question_scores)
        logger.info(f"📊 Category scores: {category_scores}")

        # Step 3: Logistic Regression — predict satisfaction from category scores
        predicted_satisfaction, success_probability = model_manager.predict_satisfaction(category_scores)
        logger.info(f"🤖 Predicted satisfaction: {predicted_satisfaction}/5.0 | Success prob: {success_probability}")

        # Step 4: Identify low-scoring questions (threshold: 3.5)
        low_scoring_questions = identify_low_scoring_questions(question_scores)
        logger.info(f"⚠️ {len(low_scoring_questions)} low-scoring questions identified")

        # Step 5: Feature importance based on category weights
        feature_importance = calculate_feature_importance(category_scores)

        # Step 6: NLP Sentiment analysis on open-ended comments
        sentiment_results = model_manager.analyze_sentiment(all_comments)
        logger.info(
            f"💬 Sentiment: {sentiment_results['positive_percentage']}% positive | "
            f"{sentiment_results['negative_percentage']}% negative | "
            f"{sentiment_results['neutral_percentage']}% neutral"
        )

        # Step 7: Strengths and weaknesses
        strengths, weaknesses = identify_strengths_weaknesses(category_scores, low_scoring_questions)

        # Step 8: Prioritized recommendations
        recommendations = generate_recommendations(category_scores, feature_importance, low_scoring_questions)

        # Step 9: What-if analysis
        optimistic, targeted = calculate_what_if_analysis(category_scores, feature_importance, low_scoring_questions)

        # Step 10: Generate summary
        if predicted_satisfaction >= 4.0:
            summary = f"🎉 Excellent event! Overall satisfaction is {predicted_satisfaction}/5.0. "
        elif predicted_satisfaction >= 3.0:
            summary = f"📊 Good event with room for improvement. Overall satisfaction is {predicted_satisfaction}/5.0. "
        else:
            summary = f"⚠️ Event needs significant improvement. Overall satisfaction is {predicted_satisfaction}/5.0. "

        summary += f"Based on {evaluation.total_respondents} responses ({evaluation.response_rate * 100:.0f}% response rate). "
        if sentiment_results['total_comments'] > 0:
            summary += (
                f"Sentiment analysis shows {sentiment_results['positive_percentage']}% positive "
                f"and {sentiment_results['negative_percentage']}% negative feedback. "
            )
        if low_scoring_questions:
            summary += f"{len(low_scoring_questions)} area(s) require improvement."

        return InsightResponse(
            summary=summary,
            analyzed_at=datetime.now().isoformat(),
            predicted_satisfaction=predicted_satisfaction,
            success_probability=success_probability,
            response_rate=evaluation.response_rate,
            total_respondents=evaluation.total_respondents,
            category_breakdown=category_scores,
            feature_importance=feature_importance,
            strengths=strengths,
            weaknesses=weaknesses,
            recommendations=recommendations,
            sentiment_score=sentiment_results['sentiment_score'],
            positive_percentage=sentiment_results['positive_percentage'],
            negative_percentage=sentiment_results['negative_percentage'],
            neutral_percentage=sentiment_results['neutral_percentage'],
            total_comments=sentiment_results['total_comments'],
            common_themes=sentiment_results.get('common_themes', []),
            positive_comments=sentiment_results.get('positive_comments', []),
            negative_comments=sentiment_results.get('negative_comments', []),
            neutral_comments=sentiment_results.get('neutral_comments', []),
            what_if_optimistic=optimistic,
            what_if_targeted=targeted,
            low_scoring_questions=low_scoring_questions[:10],
            year_level_analysis=[],
        )

    except Exception as e:
        logger.error(f"❌ Analysis failed: {e}")
        import traceback
        traceback.print_exc()
        raise HTTPException(status_code=500, detail=f"Analysis failed: {str(e)}")


@app.post("/reload-categories")
async def reload_categories():
    category_manager.load_mapping()
    return {
        "status": "success",
        "categories_loaded": len(category_manager.get_all_categories()),
        "questions_loaded": len(category_manager.question_to_text),
    }


@app.post("/test-sentiment")
async def test_sentiment(data: dict):
    """Test endpoint to verify sentiment analysis with comments"""
    comments = data.get('comments', [])
    result = model_manager.analyze_sentiment(comments)
    result['method'] = "Logistic Regression NLP" if model_manager.sentiment_model_loaded else "Keyword-based"
    return result


@app.post("/test-satisfaction")
async def test_satisfaction(data: dict):
    """Test endpoint to verify satisfaction prediction from category scores"""
    category_scores = data.get('category_scores', {})
    satisfaction, success_prob = model_manager.predict_satisfaction(category_scores)
    return {
        'predicted_satisfaction': satisfaction,
        'success_probability': success_prob,
        'method': "Logistic Regression" if model_manager.satisfaction_model_loaded else "Weighted average fallback",
    }


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)