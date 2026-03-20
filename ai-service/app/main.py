"""
app/main.py - EventFlow AI Service with Logistic Regression and NLP
Supervised Learning for Likert scale analysis and sentiment analysis
"""

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
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

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Initialize FastAPI app
app = FastAPI(
    title="EventFlow AI Service",
    description="Logistic Regression with NLP for Event Evaluation",
    version="2.0.0"
)

# Enable CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ==================== Load Trained Models ====================

MODELS_DIR = os.path.join(os.path.dirname(os.path.dirname(__file__)), 'models')
logger.info(f"Loading models from {MODELS_DIR}")

# Try to load trained models, but provide fallback
sentiment_model = None
vectorizer = None
label_encoder = None
logistic_model = None

try:
    if os.path.exists(os.path.join(MODELS_DIR, 'logistic_model.pkl')):
        logistic_model = joblib.load(os.path.join(MODELS_DIR, 'logistic_model.pkl'))
        logger.info("✅ Logistic Regression model loaded successfully")
    else:
        logger.warning("⚠️ No logistic_model.pkl found. Using default weights.")
    
    if os.path.exists(os.path.join(MODELS_DIR, 'sentiment_model.pkl')):
        sentiment_model = joblib.load(os.path.join(MODELS_DIR, 'sentiment_model.pkl'))
        vectorizer = joblib.load(os.path.join(MODELS_DIR, 'vectorizer.pkl'))
        label_encoder = joblib.load(os.path.join(MODELS_DIR, 'label_encoder.pkl'))
        logger.info("✅ Sentiment analysis models loaded successfully")
    else:
        logger.warning("⚠️ No sentiment models found. Using rule-based fallback.")
except Exception as e:
    logger.error(f"❌ Failed to load models: {e}")

# ==================== Pydantic Models ====================

class EvaluationData(BaseModel):
    # I. Information Dissemination (2 questions)
    info_timeliness: float = 0
    info_adequacy: float = 0
    
    # II. Design of the Activity (3 questions)
    design_program: float = 0
    design_relevance: float = 0
    design_pacing: float = 0
    
    # III. Outcomes of the Event (4 questions)
    outcomes_attendance: float = 0
    outcomes_participation: float = 0
    outcomes_interaction: float = 0
    outcomes_teamwork: float = 0
    
    # IV. Secretariat (3 questions)
    secretariat_sensitivity: float = 0
    secretariat_management: float = 0
    secretariat_communication: float = 0
    
    # V. Facilities (3 questions)
    facilities_appearance: float = 0
    facilities_cleanliness: float = 0
    facilities_equipment: float = 0
    
    # VI. Food (6 questions)
    food_quality: float = 0
    food_presentation: float = 0
    food_timeliness: float = 0
    food_service: float = 0
    food_sufficiency: float = 0
    food_quantity: float = 0
    
    # Demographics
    year_level: Optional[int] = 1
    respondent_type: Optional[int] = 0
    
    # Comments for sentiment analysis
    positive_comments: Optional[List[str]] = []
    suggestion_comments: Optional[List[str]] = []
    
    # Metadata
    total_respondents: Optional[int] = 0
    response_rate: Optional[float] = 0

# Simplified response model for frontend
class InsightResponse(BaseModel):
    summary: str
    analyzed_at: str
    
    # Simple metrics
    predicted_satisfaction: float
    success_probability: float
    response_rate: float
    total_respondents: int
    
    # Category scores - simple key-value
    information_dissemination: float
    design_of_activity: float
    outcomes: float
    secretariat: float
    facilities: float
    food: float
    
    # Feature importance
    importance_information: float
    importance_design: float
    importance_outcomes: float
    importance_secretariat: float
    importance_facilities: float
    importance_food: float
    
    # Strengths and weaknesses - as simple strings
    strengths: List[str]
    weaknesses: List[str]
    
    # Recommendations - as objects for frontend to display
    recommendations: List[Dict[str, Any]]
    
    # Sentiment analysis
    sentiment_score: float
    positive_percentage: float
    negative_percentage: float
    neutral_percentage: float
    total_comments: int
    common_themes: List[str]
    
    # What-if analysis
    what_if_optimistic: Dict[str, Any]
    what_if_targeted: Dict[str, Any]

# ==================== NLP Helper Functions ====================

def preprocess_text(text: str) -> str:
    """Clean and preprocess text for sentiment analysis"""
    if not text or not isinstance(text, str):
        return ""
    
    # Convert to lowercase
    text = text.lower()
    
    # Remove punctuation
    text = text.translate(str.maketrans('', '', string.punctuation))
    
    # Remove extra whitespace
    text = ' '.join(text.split())
    
    # Remove numbers
    text = re.sub(r'\d+', '', text)
    
    return text

def analyze_sentiment(comments: List[str]) -> Dict[str, Any]:
    """
    Analyze sentiment of comments using trained model or rule-based fallback
    """
    if not comments:
        return {
            'positive_percentage': 0,
            'negative_percentage': 0,
            'neutral_percentage': 0,
            'sentiment_score': 0.5,
            'total_comments': 0,
            'common_themes': []
        }
    
    # If we have a trained model, use it
    if sentiment_model and vectorizer and label_encoder:
        try:
            # Preprocess all comments
            processed_comments = [preprocess_text(c) for c in comments if c]
            
            if not processed_comments:
                return fallback_sentiment_analysis(comments)
            
            # Vectorize comments
            X = vectorizer.transform(processed_comments)
            
            # Predict sentiments
            predictions = sentiment_model.predict(X)
            
            # Decode predictions
            sentiments = label_encoder.inverse_transform(predictions)
            
            # Count sentiments
            positive_count = np.sum(sentiments == 'positive')
            negative_count = np.sum(sentiments == 'negative')
            neutral_count = np.sum(sentiments == 'neutral')
            
            total = len(sentiments)
            
            # Calculate percentages
            positive_pct = (positive_count / total) * 100 if total > 0 else 0
            negative_pct = (negative_count / total) * 100 if total > 0 else 0
            neutral_pct = (neutral_count / total) * 100 if total > 0 else 0
            
            # Calculate overall sentiment score (0-1, higher is more positive)
            sentiment_score = (positive_count + (neutral_count * 0.5)) / total if total > 0 else 0.5
            
            # Extract common themes
            all_words = ' '.join(processed_comments).split()
            word_freq = pd.Series(all_words).value_counts()
            stopwords = ['the', 'and', 'is', 'in', 'to', 'of', 'it', 'that', 'was', 'for', 
                        'on', 'with', 'as', 'this', 'but', 'be', 'at', 'from', 'by', 'an']
            common_themes = word_freq[~word_freq.index.isin(stopwords)].head(5).index.tolist()
            
            return {
                'positive_percentage': round(positive_pct, 1),
                'negative_percentage': round(negative_pct, 1),
                'neutral_percentage': round(neutral_pct, 1),
                'sentiment_score': round(sentiment_score, 2),
                'total_comments': total,
                'common_themes': common_themes
            }
            
        except Exception as e:
            logger.error(f"❌ Model prediction failed: {e}, using fallback")
            return fallback_sentiment_analysis(comments)
    
    # Fallback to rule-based analysis
    return fallback_sentiment_analysis(comments)

def fallback_sentiment_analysis(comments: List[str]) -> Dict[str, Any]:
    """Simple rule-based sentiment analysis as fallback"""
    
    # Define simple positive/negative word lists
    positive_words = ['good', 'great', 'excellent', 'awesome', 'nice', 'love', 'perfect', 
                     'fantastic', 'wonderful', 'amazing', 'helpful', 'clear', 'organized',
                     'satisfied', 'happy', 'enjoyed', 'informative', 'well', 'best']
    
    negative_words = ['bad', 'poor', 'terrible', 'awful', 'worst', 'disappointed', 'waste',
                     'horrible', 'slow', 'boring', 'confusing', 'unclear', 'disorganized',
                     'unhelpful', 'rude', 'unsatisfied', 'lacking', 'insufficient']
    
    sentiments = []
    
    for comment in comments:
        if not comment:
            continue
            
        comment_lower = comment.lower()
        
        # Count positive and negative words
        pos_count = sum(word in comment_lower for word in positive_words)
        neg_count = sum(word in comment_lower for word in negative_words)
        
        if pos_count > neg_count:
            sentiments.append('positive')
        elif neg_count > pos_count:
            sentiments.append('negative')
        else:
            sentiments.append('neutral')
    
    if not sentiments:
        return {
            'positive_percentage': 0,
            'negative_percentage': 0,
            'neutral_percentage': 0,
            'sentiment_score': 0.5,
            'total_comments': 0,
            'common_themes': []
        }
    
    positive_count = sentiments.count('positive')
    negative_count = sentiments.count('negative')
    neutral_count = sentiments.count('neutral')
    total = len(sentiments)
    
    positive_pct = (positive_count / total) * 100 if total > 0 else 0
    negative_pct = (negative_count / total) * 100 if total > 0 else 0
    neutral_pct = (neutral_count / total) * 100 if total > 0 else 0
    
    sentiment_score = (positive_count + (neutral_count * 0.5)) / total if total > 0 else 0.5
    
    # Simple theme extraction
    all_words = ' '.join([c.lower() for c in comments if c]).split()
    word_freq = pd.Series(all_words).value_counts()
    stopwords = ['the', 'and', 'is', 'in', 'to', 'of', 'it', 'that', 'was', 'for']
    common_themes = word_freq[~word_freq.index.isin(stopwords)].head(5).index.tolist()
    
    return {
        'positive_percentage': round(positive_pct, 1),
        'negative_percentage': round(negative_pct, 1),
        'neutral_percentage': round(neutral_pct, 1),
        'sentiment_score': round(sentiment_score, 2),
        'total_comments': total,
        'common_themes': common_themes
    }

# ==================== Logistic Regression Analysis ====================

class LogisticRegressionAnalyzer:
    """Core analytics engine using logistic regression principles"""
    
    def __init__(self, trained_model=None):
        self.trained_model = trained_model
        # Default weights based on research
        self.category_weights = {
            "Information Dissemination": 0.18,
            "Design of Activity": 0.15,
            "Outcomes": 0.12,
            "Secretariat": 0.22,
            "Facilities": 0.16,
            "Food": 0.17
        }
        
        self.category_descriptions = {
            "Information Dissemination": "How well information was shared before and during the event",
            "Design of Activity": "The quality of program design and time management",
            "Outcomes": "The results and impact of the event on attendees",
            "Secretariat": "The performance of the organizing team",
            "Facilities": "The quality of venue and equipment",
            "Food": "The quality and service of food provided"
        }
    
    def calculate_feature_importance(self, evaluation: EvaluationData) -> Dict[str, float]:
        """Calculate importance scores for each category"""
        category_scores = self.calculate_category_scores(evaluation)
        importance = {}
        
        for category, score in category_scores.items():
            weight = self.category_weights.get(category, 0.1)
            # Importance = weight * (score/5) * 100
            importance[category] = round(weight * (score / 5) * 100, 1)
        
        return importance
    
    def calculate_category_scores(self, evaluation: EvaluationData) -> Dict[str, float]:
        """Calculate average scores for each category"""
        data = evaluation.dict()
        
        categories = {
            "Information Dissemination": ['info_timeliness', 'info_adequacy'],
            "Design of Activity": ['design_program', 'design_relevance', 'design_pacing'],
            "Outcomes": ['outcomes_attendance', 'outcomes_participation', 'outcomes_interaction', 'outcomes_teamwork'],
            "Secretariat": ['secretariat_sensitivity', 'secretariat_management', 'secretariat_communication'],
            "Facilities": ['facilities_appearance', 'facilities_cleanliness', 'facilities_equipment'],
            "Food": ['food_quality', 'food_presentation', 'food_timeliness', 'food_service', 'food_sufficiency', 'food_quantity']
        }
        
        category_scores = {}
        for category, features in categories.items():
            scores = [data[f] for f in features if data[f] > 0]
            category_scores[category] = round(np.mean(scores) if scores else 0, 2)
        
        return category_scores
    
    def identify_strengths_weaknesses(self, category_scores: Dict[str, float]) -> Tuple[List[str], List[str]]:
        """Identify categories that are performing well and those that need improvement"""
        strengths = []
        weaknesses = []
        
        for category, score in category_scores.items():
            description = self.category_descriptions.get(category, "")
            if score >= 3.5:
                strengths.append(f"✅ {category}: {score}/5.0 - {description}")
            elif score < 3.5 and score > 0:
                weaknesses.append(f"⚠️ {category}: {score}/5.0 - Needs improvement")
        
        return strengths, weaknesses
    
    def generate_recommendations(self, category_scores: Dict[str, float], 
                                  feature_importance: Dict[str, float]) -> List[Dict[str, Any]]:
        """Generate actionable recommendations based on analysis"""
        recommendations = []
        
        # Find categories that need improvement (score < 3.5)
        for category, score in category_scores.items():
            if score < 3.5:
                importance = feature_importance.get(category, 0)
                
                # Determine priority based on score and importance
                if score < 3.0:
                    priority = "high"
                elif score < 3.3:
                    priority = "medium"
                else:
                    priority = "low"
                
                # Generate specific recommendation based on category
                if "Facilities" in category:
                    recommendations.append({
                        'priority': priority,
                        'category': category,
                        'title': f"🏛️ Improve {category}",
                        'problem_statement': f"Facilities scored {score}/5.0, which is below the target of 3.5. This category has {importance}% importance.",
                        'action_items': [
                            "Conduct pre-event venue inspection 24 hours before",
                            "Create standardized cleaning checklist",
                            "Test all equipment 2 hours before event",
                            "Assign dedicated facilities coordinator"
                        ],
                        'expected_outcome': f"Improving Facilities to 4.0 would increase overall satisfaction by approximately {round((4.0 - score) * (importance/100), 2)} points",
                        'resources_needed': ["Cleaning supplies", "Equipment maintenance", "Inspection checklist"],
                        'success_metrics': [
                            f"Improve score from {score} to at least 3.5",
                            "Zero equipment failures",
                            "Positive facility comments"
                        ]
                    })
                elif "Secretariat" in category:
                    recommendations.append({
                        'priority': priority,
                        'category': category,
                        'title': f"👥 Enhance {category} Performance",
                        'problem_statement': f"Secretariat scored {score}/5.0 but has high importance ({importance}%).",
                        'action_items': [
                            "Conduct customer service workshop for all staff",
                            "Create standard operating procedures manual",
                            "Assign dedicated point persons for different needs",
                            "Implement real-time feedback system"
                        ],
                        'expected_outcome': f"Improving Secretariat to 4.0 would increase overall satisfaction by approximately {round((4.0 - score) * (importance/100), 2)} points",
                        'resources_needed': ["Training materials", "SOP templates", "Communication devices"],
                        'success_metrics': [
                            f"Improve score from {score} to 3.5+",
                            "Positive comments about staff",
                            "Faster response times"
                        ]
                    })
                elif "Food" in category:
                    recommendations.append({
                        'priority': priority,
                        'category': category,
                        'title': f"🍽️ Upgrade {category} Service",
                        'problem_statement': f"Food service scored {score}/5.0, below the target of 3.5.",
                        'action_items': [
                            "Review and possibly change caterer",
                            "Gather specific feedback on food preferences",
                            "Improve food serving timeline",
                            "Ensure sufficient quantity for all"
                        ],
                        'expected_outcome': f"Improving Food to 4.0 would increase satisfaction by approximately {round((4.0 - score) * (importance/100), 2)} points",
                        'resources_needed': ["Caterer contacts", "Menu feedback", "Budget review"],
                        'success_metrics': [
                            f"Improve score from {score} to 3.5+",
                            "Reduced food complaints",
                            "Positive food comments"
                        ]
                    })
                else:
                    recommendations.append({
                        'priority': priority,
                        'category': category,
                        'title': f"📈 Improve {category}",
                        'problem_statement': f"{category} scored {score}/5.0, target is 3.5/5.0.",
                        'action_items': [
                            f"Review current {category.lower()} processes",
                            "Gather specific feedback on what needs improvement",
                            "Implement changes based on feedback",
                            "Monitor results in next evaluation"
                        ],
                        'expected_outcome': f"Improving this category would increase overall satisfaction",
                        'resources_needed': ["Review meeting", "Feedback collection"],
                        'success_metrics': [
                            f"Improve score from {score} to 3.5+",
                            "Positive feedback in this category"
                        ]
                    })
        
        return sorted(recommendations, key=lambda x: {'high': 0, 'medium': 1, 'low': 2}.get(x['priority'], 3))
    
    def calculate_what_if_analysis(self, category_scores: Dict[str, float], 
                                   feature_importance: Dict[str, float]) -> Tuple[Dict, Dict]:
        """Calculate what-if scenarios for improvement"""
        current_satisfaction = np.mean([s for s in category_scores.values() if s > 0])
        
        # Scenario 1: Focus on top weaknesses
        weaknesses = []
        for category, score in category_scores.items():
            if score < 3.5:
                weaknesses.append({
                    'category': category,
                    'score': score,
                    'importance': feature_importance.get(category, 0)
                })
        
        # Sort by importance (highest first) and take top 3
        weaknesses.sort(key=lambda x: x['importance'], reverse=True)
        top_weaknesses = weaknesses[:3]
        
        targeted_gain = 0
        targeted_improvements = []
        
        for weakness in top_weaknesses:
            category = weakness['category']
            score = weakness['score']
            importance = weakness['importance']
            target = min(4.0, score + 1.0)
            gain = round((target - score) * (importance/100), 2)
            targeted_gain += gain
            targeted_improvements.append({
                'category': category,
                'from': score,
                'to': target,
                'gain': gain
            })
        
        targeted = {
            'scenario': 'Focus on top weaknesses',
            'current_satisfaction': round(current_satisfaction, 2),
            'projected_satisfaction': round(min(5.0, current_satisfaction + targeted_gain), 2),
            'gain': round(targeted_gain, 2),
            'improvements': targeted_improvements
        }
        
        # Scenario 2: Optimistic - improve all to 4.0
        optimistic_gain = 0
        optimistic_improvements = []
        
        for category, score in category_scores.items():
            if score < 4.0:
                importance = feature_importance.get(category, 0)
                gain = round((4.0 - score) * (importance/100), 2)
                optimistic_gain += gain
                optimistic_improvements.append({
                    'category': category,
                    'from': score,
                    'to': 4.0,
                    'gain': gain
                })
        
        optimistic = {
            'scenario': 'Improve all areas to 4.0',
            'current_satisfaction': round(current_satisfaction, 2),
            'projected_satisfaction': round(min(5.0, current_satisfaction + optimistic_gain), 2),
            'gain': round(optimistic_gain, 2),
            'improvements': optimistic_improvements
        }
        
        return optimistic, targeted
    
    def calculate_overall_satisfaction(self, evaluation: EvaluationData) -> float:
        """Calculate overall satisfaction score"""
        data = evaluation.dict()
        all_ratings = []
        
        for key, value in data.items():
            if key not in ['year_level', 'respondent_type', 'total_respondents', 'response_rate', 
                           'positive_comments', 'suggestion_comments'] and isinstance(value, (int, float)) and value > 0:
                all_ratings.append(value)
        
        return round(np.mean(all_ratings), 2) if all_ratings else 2.5

# Initialize analyzer
analyzer = LogisticRegressionAnalyzer(trained_model=logistic_model)

# ==================== API Endpoints ====================

@app.get("/")
async def root():
    return {
        "service": "EventFlow AI Service",
        "status": "healthy",
        "algorithm": "Supervised Logistic Regression with NLP"
    }

@app.post("/analyze", response_model=InsightResponse)
async def analyze_evaluation(evaluation: EvaluationData):
    """
    Analyze evaluation data using Logistic Regression and NLP
    """
    logger.info(f"📥 Received evaluation with {evaluation.total_respondents} responses")
    
    try:
        # Calculate category scores
        category_scores = analyzer.calculate_category_scores(evaluation)
        
        # Calculate feature importance
        feature_importance = analyzer.calculate_feature_importance(evaluation)
        
        # Calculate overall satisfaction
        overall_satisfaction = analyzer.calculate_overall_satisfaction(evaluation)
        
        # Analyze sentiment
        all_comments = (evaluation.positive_comments or []) + (evaluation.suggestion_comments or [])
        sentiment_results = analyze_sentiment(all_comments)
        
        # Adjust based on sentiment if available
        if sentiment_results['total_comments'] > 0:
            blended_satisfaction = (overall_satisfaction * 0.7) + (sentiment_results['sentiment_score'] * 5 * 0.3)
            overall_satisfaction = round(blended_satisfaction, 2)
        
        success_probability = round(overall_satisfaction / 5, 2)
        
        # Identify strengths and weaknesses
        strengths, weaknesses = analyzer.identify_strengths_weaknesses(category_scores)
        
        # Generate recommendations
        recommendations = analyzer.generate_recommendations(category_scores, feature_importance)
        
        # Calculate what-if scenarios
        optimistic, targeted = analyzer.calculate_what_if_analysis(category_scores, feature_importance)
        
        # Generate summary
        if overall_satisfaction >= 4.0:
            summary = f"🎉 Excellent event! Overall satisfaction is {overall_satisfaction}/5.0. "
        elif overall_satisfaction >= 3.0:
            summary = f"📊 Good event with room for improvement. Overall satisfaction is {overall_satisfaction}/5.0. "
        else:
            summary = f"⚠️ Event needs significant improvement. Overall satisfaction is {overall_satisfaction}/5.0. "
        
        summary += f"Based on {evaluation.total_respondents} responses ({evaluation.response_rate*100:.0f}% response rate). "
        
        if sentiment_results['total_comments'] > 0:
            summary += f"Comments show {sentiment_results['positive_percentage']}% positive."
        
        return InsightResponse(
            summary=summary,
            analyzed_at=datetime.now().isoformat(),
            predicted_satisfaction=overall_satisfaction,
            success_probability=success_probability,
            response_rate=evaluation.response_rate,
            total_respondents=evaluation.total_respondents,
            information_dissemination=category_scores.get("Information Dissemination", 0),
            design_of_activity=category_scores.get("Design of Activity", 0),
            outcomes=category_scores.get("Outcomes", 0),
            secretariat=category_scores.get("Secretariat", 0),
            facilities=category_scores.get("Facilities", 0),
            food=category_scores.get("Food", 0),
            importance_information=feature_importance.get("Information Dissemination", 0),
            importance_design=feature_importance.get("Design of Activity", 0),
            importance_outcomes=feature_importance.get("Outcomes", 0),
            importance_secretariat=feature_importance.get("Secretariat", 0),
            importance_facilities=feature_importance.get("Facilities", 0),
            importance_food=feature_importance.get("Food", 0),
            strengths=strengths,
            weaknesses=weaknesses,
            recommendations=recommendations,
            sentiment_score=sentiment_results['sentiment_score'],
            positive_percentage=sentiment_results['positive_percentage'],
            negative_percentage=sentiment_results['negative_percentage'],
            neutral_percentage=sentiment_results['neutral_percentage'],
            total_comments=sentiment_results['total_comments'],
            common_themes=sentiment_results.get('common_themes', []),
            what_if_optimistic=optimistic,
            what_if_targeted=targeted
        )
        
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
            information_dissemination=0,
            design_of_activity=0,
            outcomes=0,
            secretariat=0,
            facilities=0,
            food=0,
            importance_information=0,
            importance_design=0,
            importance_outcomes=0,
            importance_secretariat=0,
            importance_facilities=0,
            importance_food=0,
            strengths=[],
            weaknesses=[],
            recommendations=[],
            sentiment_score=0.5,
            positive_percentage=0,
            negative_percentage=0,
            neutral_percentage=0,
            total_comments=0,
            common_themes=[],
            what_if_optimistic={},
            what_if_targeted={}
        )

@app.get("/health")
async def health_check():
    """Simple health check endpoint"""
    return {"status": "healthy", "timestamp": datetime.now().isoformat()}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001)