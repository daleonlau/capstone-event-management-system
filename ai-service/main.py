"""
app/main.py - EventFlow AI Service with Enhanced Sentiment Analysis and Debug Logs
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
import hashlib
from collections import defaultdict

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

# Initialize FastAPI app
app = FastAPI(
    title="EventFlow AI Service",
    description="Dynamic ML for Event Evaluation with Enhanced Sentiment Analysis",
    version="7.0.0"
)

# Enable CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ==================== Database Configuration ====================

DB_CONFIG = {
    'host': os.getenv('DB_HOST', '127.0.0.1'),
    'database': os.getenv('DB_DATABASE', 'Capstone_Project_EventFlow'),
    'user': os.getenv('DB_USERNAME', 'root'),
    'password': os.getenv('DB_PASSWORD', ''),
    'port': int(os.getenv('DB_PORT', 3306))
}

logger.info(f"Database config: {DB_CONFIG['host']}/{DB_CONFIG['database']}")

def get_db_connection():
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        return connection
    except Error as e:
        logger.error(f"Database connection error: {e}")
        return None

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
        """Load question-to-category mapping from database"""
        connection = get_db_connection()
        if not connection:
            logger.warning("⚠️ Could not connect to database for category mapping")
            return
        
        try:
            cursor = connection.cursor(dictionary=True)
            cursor.execute("""
                SELECT 
                    eq.id,
                    eq.question_text,
                    ec.category_name
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
                    inferred_category = self.infer_category(question_text)
                    if inferred_category:
                        self.question_to_category[q_id] = inferred_category
                        self.category_questions[inferred_category].append(q_id)
                    else:
                        self.question_to_category[q_id] = "Other"
                        self.category_questions["Other"].append(q_id)
            
            self.last_updated = datetime.now()
            logger.info(f"✅ Loaded {len(self.question_to_category)} questions into {len(self.category_questions)} categories")
            
        except Error as e:
            logger.error(f"Error loading category mapping: {e}")
        finally:
            if connection:
                connection.close()
    
    def infer_category(self, question_text: str) -> str:
        """Infer category from question text"""
        text_lower = question_text.lower()
        
        if any(word in text_lower for word in ['timeliness', 'adequacy', 'information dissemination']):
            return "I. Information Dissemination"
        elif any(word in text_lower for word in ['program', 'order', 'relevance', 'pacing', 'time allotment', 'design']):
            return "II. Design of the Event"
        elif any(word in text_lower for word in ['attendance', 'participation', 'interaction', 'teamwork', 'outcomes']):
            return "III. Outcomes of the Event"
        elif any(word in text_lower for word in ['sensitivity', 'management', 'provision', 'feedback', 'secretariat']):
            return "IV. Secretariat"
        elif any(word in text_lower for word in ['appearance', 'cleanliness', 'orderliness', 'equipment', 'availability', 'functionality', 'facilities', 'venue']):
            return "V. Facilities"
        elif any(word in text_lower for word in ['food', 'beverage', 'presentation', 'service', 'sufficiency', 'quantity']):
            return "VI. Food"
        elif 'speaker' in text_lower:
            return "VII. Resource Speaker"
        elif 'traffic' in text_lower:
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

# Initialize category manager
category_manager = CategoryManager()

# ==================== Model Manager ====================

class ModelManager:
    def __init__(self):
        self.sentiment_model = None
        self.vectorizer = None
        self.label_encoder = None
        self.models_loaded = False
        self.load_models()
    
    def load_models(self):
        models_dir = os.path.join(os.path.dirname(__file__), 'models')
        os.makedirs(models_dir, exist_ok=True)
        
        try:
            sentiment_path = os.path.join(models_dir, 'sentiment_model.pkl')
            vectorizer_path = os.path.join(models_dir, 'vectorizer.pkl')
            encoder_path = os.path.join(models_dir, 'label_encoder.pkl')
            
            if os.path.exists(sentiment_path):
                self.sentiment_model = joblib.load(sentiment_path)
                self.vectorizer = joblib.load(vectorizer_path)
                self.label_encoder = joblib.load(encoder_path)
                self.models_loaded = True
                logger.info("✅ Existing sentiment models loaded")
                return True
        except Exception as e:
            logger.error(f"Failed to load models: {e}")
        
        logger.info("⚠️ No existing sentiment models found")
        return False
    
    def preprocess_text(self, text: str) -> str:
        if not text or not isinstance(text, str):
            return ""
        text = text.lower()
        text = text.translate(str.maketrans('', '', string.punctuation))
        text = ' '.join(text.split())
        text = re.sub(r'\d+', '', text)
        return text
    
    def analyze_sentiment(self, comments: List[str]) -> Dict[str, Any]:
        """Enhanced sentiment analysis with comprehensive keyword lists"""
        logger.info(f"🔍 ANALYZING SENTIMENT - Received {len(comments)} comments")
        
        if not comments:
            logger.info("No comments to analyze")
            return {
                'positive_percentage': 0,
                'negative_percentage': 0,
                'neutral_percentage': 0,
                'sentiment_score': 0.5,
                'total_comments': 0,
                'common_themes': [],
                'positive_comments': [],
                'negative_comments': [],
                'neutral_comments': []
            }
        
        # Log sample comments for debugging
        logger.info(f"📝 Sample comments (first 3): {comments[:3]}")
        
        # Comprehensive positive keywords
        positive_keywords = [
            'good', 'great', 'excellent', 'awesome', 'nice', 'love', 'perfect', 'fantastic',
            'wonderful', 'amazing', 'helpful', 'clear', 'organized', 'satisfied', 'happy',
            'enjoyed', 'informative', 'well', 'best', 'outstanding', 'impressive', 'valuable',
            'recommend', 'superb', 'exceptional', 'brilliant', 'pleased', 'fun', 'exciting',
            'thrilling', 'epic', 'intense', 'well-organized', 'well-planned', 'smooth',
            'efficient', 'professional', 'knowledgeable', 'engaging', 'interactive',
            'entertaining', 'memorable', 'fantastic', 'incredible', 'spectacular'
        ]
        
        # Comprehensive negative keywords
        negative_keywords = [
            'bad', 'poor', 'terrible', 'awful', 'worst', 'disappointed', 'waste', 'horrible',
            'slow', 'boring', 'confusing', 'unclear', 'disorganized', 'unhelpful', 'rude',
            'unsatisfied', 'lacking', 'insufficient', 'delay', 'problem', 'issue', 'complaint',
            'difficult', 'frustrating', 'annoying', 'disappointing', 'dissatisfied', 'cold',
            'not enough', 'long lines', 'broken', 'damaged', 'old', 'worn out', 'torn',
            'dangerous', 'unsafe', 'poor quality', 'terrible'
        ]
        
        positive_comments = []
        negative_comments = []
        neutral_comments = []
        sentiments = []
        
        for comment in comments:
            if not comment:
                continue
            
            comment_lower = comment.lower()
            pos_count = sum(1 for kw in positive_keywords if kw in comment_lower)
            neg_count = sum(1 for kw in negative_keywords if kw in comment_lower)
            
            if pos_count > neg_count:
                sentiment = 'positive'
                positive_comments.append(comment)
            elif neg_count > pos_count:
                sentiment = 'negative'
                negative_comments.append(comment)
            else:
                if any(word in comment_lower for word in ['excellent', 'amazing', 'perfect', 'fantastic']):
                    sentiment = 'positive'
                    positive_comments.append(comment)
                elif any(word in comment_lower for word in ['disappointed', 'terrible', 'awful', 'poor']):
                    sentiment = 'negative'
                    negative_comments.append(comment)
                else:
                    sentiment = 'neutral'
                    neutral_comments.append(comment)
            
            sentiments.append(sentiment)
        
        positive_count = sentiments.count('positive')
        negative_count = sentiments.count('negative')
        neutral_count = sentiments.count('neutral')
        total = len(sentiments)
        
        positive_pct = (positive_count / total) * 100
        negative_pct = (negative_count / total) * 100
        neutral_pct = (neutral_count / total) * 100
        sentiment_score = (positive_count + (neutral_count * 0.5)) / total
        
        # Extract common themes
        all_words = ' '.join([c.lower() for c in comments if c]).split()
        from collections import Counter
        word_freq = Counter(all_words)
        stopwords = {'the', 'and', 'is', 'in', 'to', 'of', 'it', 'that', 'was', 'for',
                     'this', 'but', 'with', 'as', 'are', 'be', 'at', 'from', 'by', 'an',
                     'on', 'have', 'has', 'were', 'had', 'been', 'not', 'very', 'so'}
        common_themes = [word for word, _ in word_freq.most_common(10) if word not in stopwords][:5]
        
        logger.info(f"✅ SENTIMENT RESULTS: {len(positive_comments)} positive, {len(negative_comments)} negative, {len(neutral_comments)} neutral")
        
        return {
            'positive_percentage': round(positive_pct, 1),
            'negative_percentage': round(negative_pct, 1),
            'neutral_percentage': round(neutral_pct, 1),
            'sentiment_score': round(sentiment_score, 2),
            'total_comments': total,
            'common_themes': common_themes,
            'positive_comments': positive_comments,
            'negative_comments': negative_comments,
            'neutral_comments': neutral_comments
        }

# Initialize model manager
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

# ==================== Category Weights ====================

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

# ==================== Analysis Functions ====================

def extract_question_scores(data: Dict[str, Any]) -> Dict[int, float]:
    """Extract question scores from the data"""
    question_scores = {}
    
    for key, value in data.items():
        if not isinstance(value, (int, float)):
            continue
        
        if isinstance(key, str) and key.startswith('q_'):
            try:
                q_id = int(key.split('_')[1])
                question_scores[q_id] = value
            except:
                pass
    
    return question_scores


def calculate_raw_average(question_scores: Dict[int, float]) -> float:
    """Calculate the raw average of all question scores"""
    if not question_scores:
        return 2.5
    
    total_score = sum(question_scores.values())
    total_count = len(question_scores)
    
    return round(total_score / total_count, 2)


def calculate_category_scores(question_scores: Dict[int, float]) -> Dict[str, float]:
    """Calculate category scores using dynamic mapping"""
    category_scores = {}
    
    for category in category_manager.get_all_categories():
        score = category_manager.get_category_score(category, question_scores)
        if score > 0:
            category_scores[category] = score
    
    return category_scores


def identify_low_scoring_questions(question_scores: Dict[int, float]) -> List[Dict[str, Any]]:
    """Identify questions scoring below 3.5"""
    low_scoring = []
    
    for q_id, score in question_scores.items():
        if score < 3.5 and score > 0:
            question_text = category_manager.get_question_text(q_id)
            category = category_manager.get_category_for_question(q_id)
            
            status = "critical" if score < 3.0 else "needs_attention" if score < 3.3 else "borderline"
            
            low_scoring.append({
                'id': q_id,
                'text': question_text,
                'score': score,
                'category': category,
                'status': status
            })
    
    low_scoring.sort(key=lambda x: x['score'])
    return low_scoring


def calculate_overall_satisfaction(category_scores: Dict[str, float]) -> float:
    """Calculate overall satisfaction (average of category scores) - DEPRECATED, use raw average instead"""
    valid_scores = [s for s in category_scores.values() if s > 0]
    return round(np.mean(valid_scores), 2) if valid_scores else 2.5


def calculate_feature_importance(category_scores: Dict[str, float]) -> Dict[str, float]:
    importance = {}
    for category, score in category_scores.items():
        if score > 0:
            weight = CATEGORY_WEIGHTS.get(category, 0.1)
            importance[category] = round(weight * (score / 5) * 100, 1)
    return importance


def identify_strengths_weaknesses(category_scores: Dict[str, float], low_scoring_questions: List[Dict[str, Any]]) -> Tuple[List[str], List[str]]:
    strengths = []
    weaknesses = []
    
    for category, score in category_scores.items():
        desc = CATEGORY_DESCRIPTIONS.get(category, "")
        if score >= 4.0:
            strengths.append(f"✅ {category}: {score}/5.0 - {desc}")
        elif score >= 3.5:
            strengths.append(f"📊 {category}: {score}/5.0 - Good performance")
        elif score >= 3.0 and score < 3.5:
            weaknesses.append(f"⚠️ {category}: {score}/5.0 - Needs attention")
        elif score > 0 and score < 3.0:
            weaknesses.append(f"❌ {category}: {score}/5.0 - Critical area")
    
    for q in low_scoring_questions[:5]:
        status_icon = "❌" if q['status'] == 'critical' else "⚠️"
        weaknesses.append(f"{status_icon} {q['text']}: {q['score']}/5.0 - Needs improvement")
    
    return strengths, weaknesses


def generate_recommendations(category_scores: Dict[str, float], feature_importance: Dict[str, float], low_scoring_questions: List[Dict[str, Any]]) -> List[Dict[str, Any]]:
    recommendations = []
    
    for q in low_scoring_questions[:5]:
        priority = "high" if q['score'] < 3.0 else "medium" if q['score'] < 3.3 else "low"
        
        action_items = []
        if 'food' in q['text'].lower():
            action_items = [
                "Review portion sizes and increase quantity",
                "Conduct taste testing with student representatives",
                "Improve food serving speed and organization",
                "Gather specific feedback on menu preferences"
            ]
        elif 'equipment' in q['text'].lower() or 'facilities' in q['text'].lower():
            action_items = [
                "Conduct pre-event equipment inspection",
                "Replace damaged or worn equipment",
                "Test all equipment before the event",
                "Create maintenance schedule"
            ]
        elif 'teamwork' in q['text'].lower():
            action_items = [
                "Organize team-building activities",
                "Assign clear roles and responsibilities",
                "Encourage collaboration through group activities",
                "Recognize team contributions"
            ]
        else:
            action_items = [
                f"Review current processes for {q['text']}",
                "Gather specific feedback from students",
                "Implement targeted improvements",
                "Monitor results in next evaluation"
            ]
        
        recommendations.append({
            'priority': priority,
            'category': q['category'],
            'title': f"📌 Improve: {q['text']}",
            'problem_statement': f"This aspect scored {q['score']}/5.0, below the target of 3.5.",
            'action_items': action_items[:4],
            'expected_outcome': f"Improving this to 4.0 would increase overall satisfaction",
            'resources_needed': ["Feedback collection", "Implementation plan", "Follow-up monitoring"],
            'success_metrics': [f"Improve score from {q['score']} to 4.0+", "Positive feedback on this aspect"]
        })
    
    for category, score in category_scores.items():
        if score < 3.8 and score > 0:
            importance = feature_importance.get(category, 0)
            priority = "high" if score < 3.5 else "medium"
            category_low_questions = [q for q in low_scoring_questions if q['category'] == category]
            
            if category_low_questions:
                recommendations.append({
                    'priority': priority,
                    'category': category,
                    'title': f"📊 Improve {category}",
                    'problem_statement': f"{category} scored {score}/5.0. Issues: {', '.join([q['text'][:30] for q in category_low_questions[:2]])}",
                    'action_items': [
                        f"Address all {len(category_low_questions)} low-scoring areas in this category",
                        "Conduct focused improvement plan",
                        "Monitor individual question scores in next evaluation"
                    ],
                    'expected_outcome': f"Improving {category} to 4.0 would increase satisfaction by approximately {round((4.0 - score) * (importance/100), 2)} points",
                    'resources_needed': ["Category-specific review", "Stakeholder feedback", "Implementation timeline"],
                    'success_metrics': [f"Improve {category} score from {score} to 4.0+"]
                })
    
    return sorted(recommendations, key=lambda x: {'high': 0, 'medium': 1, 'low': 2}.get(x['priority'], 3))


def calculate_what_if_analysis(category_scores: Dict[str, float], feature_importance: Dict[str, float], low_scoring_questions: List[Dict[str, Any]]) -> Tuple[Dict, Dict]:
    current = calculate_overall_satisfaction(category_scores)
    
    targeted_gain = 0
    targeted_improvements = []
    for q in low_scoring_questions[:3]:
        target = min(4.0, q['score'] + 1.0)
        gain = round((target - q['score']) * 0.05, 2)
        targeted_gain += gain
        targeted_improvements.append({
            'question': q['text'][:40],
            'category': q['category'],
            'from': q['score'],
            'to': target,
            'gain': gain
        })
    
    targeted = {
        'scenario': 'Focus on low-scoring questions',
        'current_satisfaction': round(current, 2),
        'projected_satisfaction': round(min(5.0, current + targeted_gain), 2),
        'gain': round(targeted_gain, 2),
        'improvements': targeted_improvements
    }
    
    optimistic_gain = 0
    optimistic_improvements = []
    for category, score in category_scores.items():
        if score < 4.0 and score > 0:
            gain = round((4.0 - score) * (CATEGORY_WEIGHTS.get(category, 0.1)), 2)
            optimistic_gain += gain
            optimistic_improvements.append({'category': category, 'from': score, 'to': 4.0, 'gain': gain})
    
    optimistic = {
        'scenario': 'Improve all categories to 4.0',
        'current_satisfaction': round(current, 2),
        'projected_satisfaction': round(min(5.0, current + optimistic_gain), 2),
        'gain': round(optimistic_gain, 2),
        'improvements': optimistic_improvements
    }
    
    return optimistic, targeted

# ==================== API Endpoints ====================

@app.get("/")
async def root():
    return {
        "service": "EventFlow AI Service",
        "status": "healthy",
        "version": "7.0.0",
        "database_connected": get_db_connection() is not None,
        "models_loaded": model_manager.models_loaded,
        "categories_loaded": len(category_manager.get_all_categories()),
        "questions_loaded": len(category_manager.question_to_text)
    }

@app.post("/analyze", response_model=InsightResponse)
async def analyze_evaluation(evaluation: EvaluationData):
    """Analyze evaluation data with enhanced sentiment analysis"""
    logger.info(f"📥 Received evaluation with {evaluation.total_respondents} responses")
    logger.info(f"📝 Comments received - positive: {len(evaluation.positive_comments)}, suggestion: {len(evaluation.suggestion_comments)}")
    logger.info(f"📝 Sample positive comments: {evaluation.positive_comments[:2] if evaluation.positive_comments else 'none'}")
    logger.info(f"📝 Sample suggestion comments: {evaluation.suggestion_comments[:2] if evaluation.suggestion_comments else 'none'}")
    
    try:
        # Combine all comments for sentiment analysis
        all_comments = (evaluation.positive_comments or []) + (evaluation.suggestion_comments or [])
        logger.info(f"📝 Total comments for analysis: {len(all_comments)}")
        
        # Extract question scores
        question_scores = extract_question_scores(evaluation.data)
        logger.info(f"📊 Found {len(question_scores)} question scores")
        
        # ==================== CALCULATE RAW AVERAGE ====================
        # This is the actual overall satisfaction from raw responses
        raw_overall_satisfaction = calculate_raw_average(question_scores)
        logger.info(f"📊 RAW OVERALL SATISFACTION: {raw_overall_satisfaction} from {len(question_scores)} ratings")
        
        # Calculate category scores (for category breakdown and analysis)
        category_scores = calculate_category_scores(question_scores)
        logger.info(f"📊 Category scores: {category_scores}")
        
        # Identify low-scoring questions
        low_scoring_questions = identify_low_scoring_questions(question_scores)
        logger.info(f"⚠️ Found {len(low_scoring_questions)} low-scoring questions")
        
        # Calculate metrics using RAW average
        success_probability = round(raw_overall_satisfaction / 5, 2)
        feature_importance = calculate_feature_importance(category_scores)
        
        # Analyze sentiment with comments
        sentiment_results = model_manager.analyze_sentiment(all_comments)
        
        logger.info(f"📊 SENTIMENT RESULTS: {sentiment_results['positive_percentage']}% positive, "
                   f"{sentiment_results['negative_percentage']}% negative, "
                   f"{sentiment_results['neutral_percentage']}% neutral")
        logger.info(f"📊 COMMENT COUNTS: Positive: {len(sentiment_results['positive_comments'])}, "
                   f"Negative: {len(sentiment_results['negative_comments'])}, "
                   f"Neutral: {len(sentiment_results['neutral_comments'])}")
        
        # Identify strengths and weaknesses
        strengths, weaknesses = identify_strengths_weaknesses(category_scores, low_scoring_questions)
        
        # Generate recommendations
        recommendations = generate_recommendations(category_scores, feature_importance, low_scoring_questions)
        
        # What-if analysis
        optimistic, targeted = calculate_what_if_analysis(category_scores, feature_importance, low_scoring_questions)
        
        # Generate summary using RAW average
        if raw_overall_satisfaction >= 4.0:
            summary = f"🎉 Excellent event! Overall satisfaction is {raw_overall_satisfaction}/5.0. "
        elif raw_overall_satisfaction >= 3.0:
            summary = f"📊 Good event with room for improvement. Overall satisfaction is {raw_overall_satisfaction}/5.0. "
        else:
            summary = f"⚠️ Event needs significant improvement. Overall satisfaction is {raw_overall_satisfaction}/5.0. "
        
        summary += f"Based on {evaluation.total_respondents} responses ({evaluation.response_rate*100:.0f}% response rate). "
        
        if sentiment_results['total_comments'] > 0:
            summary += f"Comments show {sentiment_results['positive_percentage']}% positive, "
            summary += f"{sentiment_results['negative_percentage']}% negative feedback."
        
        if low_scoring_questions:
            summary += f" {len(low_scoring_questions)} specific areas need improvement."
        
        # Prepare low-scoring questions response
        low_scoring_response = []
        for q in low_scoring_questions[:10]:
            low_scoring_response.append({
                'id': q['id'],
                'text': q['text'],
                'score': q['score'],
                'category': q['category'],
                'status': q['status']
            })
        
        response_data = InsightResponse(
            summary=summary,
            analyzed_at=datetime.now().isoformat(),
            predicted_satisfaction=raw_overall_satisfaction,  # NOW USING RAW AVERAGE
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
            low_scoring_questions=low_scoring_response,
            year_level_analysis=[]
        )
        
        logger.info(f"📤 RETURNING RESPONSE - Predicted satisfaction: {raw_overall_satisfaction} (raw average)")
        
        return response_data
        
    except Exception as e:
        logger.error(f"❌ Analysis failed: {e}")
        import traceback
        traceback.print_exc()
        return InsightResponse(
            summary="Analysis completed with errors",
            analyzed_at=datetime.now().isoformat(),
            predicted_satisfaction=2.5,
            success_probability=0.5,
            response_rate=0,
            total_respondents=0,
            category_breakdown={},
            feature_importance={},
            strengths=[],
            weaknesses=[],
            recommendations=[],
            sentiment_score=0.5,
            positive_percentage=0,
            negative_percentage=0,
            neutral_percentage=0,
            total_comments=0,
            common_themes=[],
            positive_comments=[],
            negative_comments=[],
            neutral_comments=[],
            what_if_optimistic={},
            what_if_targeted={},
            low_scoring_questions=[],
            year_level_analysis=[]
        )

@app.get("/health")
async def health_check():
    return {
        "status": "healthy",
        "timestamp": datetime.now().isoformat(),
        "database_connected": get_db_connection() is not None,
        "models_loaded": model_manager.models_loaded,
        "categories_loaded": len(category_manager.get_all_categories()),
        "questions_loaded": len(category_manager.question_to_text)
    }

@app.post("/reload-categories")
async def reload_categories():
    category_manager.load_mapping()
    return {
        "status": "success",
        "categories_loaded": len(category_manager.get_all_categories()),
        "questions_loaded": len(category_manager.question_to_text)
    }

@app.post("/test-comments")
async def test_comments(data: dict):
    """Test endpoint to verify sentiment analysis with comments"""
    comments = data.get('comments', [])
    result = model_manager.analyze_sentiment(comments)
    return result

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001)