"""
EventFlow AI Service - NLP Sentiment Analysis Only
"""

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
import joblib
import os
from typing import List, Dict, Any, Optional
from datetime import datetime
import logging
import re
import string
from collections import Counter

# ==================== Logging ====================
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

# ==================== FastAPI App ====================
app = FastAPI(
    title="EventFlow AI Service",
    description="NLP Sentiment Analysis for Event Evaluation Comments",
    version="3.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ==================== Keyword Lists ====================

POSITIVE_KEYWORDS = [
    'good', 'great', 'excellent', 'awesome', 'nice', 'love', 'perfect', 'fantastic',
    'wonderful', 'amazing', 'helpful', 'clear', 'organized', 'satisfied', 'happy',
    'enjoyed', 'informative', 'well', 'best', 'outstanding', 'impressive', 'valuable',
    'recommend', 'superb', 'exceptional', 'brilliant', 'pleased', 'fun', 'exciting',
    'well-organized', 'well-planned', 'smooth', 'efficient', 'professional',
    'knowledgeable', 'engaging', 'interactive', 'entertaining', 'memorable', 'maayo',
    'lami', 'nindot', 'ganahan', 'salamat', 'appreciated', 'impressed'
]

NEGATIVE_KEYWORDS = [
    'bad', 'poor', 'terrible', 'awful', 'worst', 'disappointed', 'waste', 'horrible',
    'slow', 'boring', 'confusing', 'unclear', 'disorganized', 'unhelpful', 'rude',
    'unsatisfied', 'lacking', 'insufficient', 'delay', 'problem', 'issue', 'complaint',
    'difficult', 'frustrating', 'annoying', 'disappointing', 'dissatisfied', 'pangit',
    'hindi maganda', 'mabagal', 'malamig', 'konti', 'kulang', 'late', 'rush'
]

# ==================== Model Manager ====================

class SentimentModelManager:
    def __init__(self):
        self.sentiment_model = None
        self.vectorizer = None
        self.label_encoder = None
        self.model_loaded = False
        self.load_models()

    def load_models(self):
        models_dir = os.path.join(os.path.dirname(__file__), 'models')
        os.makedirs(models_dir, exist_ok=True)

        try:
            s_path = os.path.join(models_dir, 'sentiment_model.pkl')
            v_path = os.path.join(models_dir, 'vectorizer.pkl')
            e_path = os.path.join(models_dir, 'label_encoder.pkl')

            if all(os.path.exists(p) for p in [s_path, v_path, e_path]):
                self.sentiment_model = joblib.load(s_path)
                self.vectorizer = joblib.load(v_path)
                self.label_encoder = joblib.load(e_path)
                self.model_loaded = True
                logger.info("NLP Sentiment model loaded successfully")
            else:
                logger.warning("Sentiment model files not found — using keyword fallback")
        except Exception as e:
            logger.error(f"Failed to load sentiment model: {e}")

    def preprocess_text(self, text: str) -> str:
        if not text or not isinstance(text, str):
            return ""
        text = text.lower()
        text = text.translate(str.maketrans('', '', string.punctuation))
        text = re.sub(r'\d+', '', text)
        return ' '.join(text.split())

    def classify_comment(self, comment: str) -> str:
        if self.model_loaded:
            try:
                processed = self.preprocess_text(comment)
                if not processed:
                    return 'neutral'
                features = self.vectorizer.transform([processed])
                prediction = self.sentiment_model.predict(features)
                return str(self.label_encoder.inverse_transform(prediction)[0]).lower()
            except Exception as e:
                logger.warning(f"LR sentiment failed, falling back to keywords: {e}")

        return self._keyword_classify(comment)

    def _keyword_classify(self, comment: str) -> str:
        comment_lower = comment.lower()
        pos_count = sum(1 for kw in POSITIVE_KEYWORDS if kw in comment_lower)
        neg_count = sum(1 for kw in NEGATIVE_KEYWORDS if kw in comment_lower)
        
        # Check for very positive indicators
        very_positive = ['excellent', 'amazing', 'perfect', 'fantastic', 'outstanding', 'exceptional']
        if any(w in comment_lower for w in very_positive):
            return 'positive'
        
        # Check for very negative indicators
        very_negative = ['disappointed', 'terrible', 'awful', 'poor', 'horrible']
        if any(w in comment_lower for w in very_negative):
            return 'negative'
        
        if pos_count > neg_count:
            return 'positive'
        elif neg_count > pos_count:
            return 'negative'
        
        return 'neutral'

    def analyze_sentiment(self, comments: List[str]) -> Dict[str, Any]:
        """Perform NLP sentiment analysis on ALL open-ended comments (no limits)"""
        method = "Logistic Regression NLP" if self.model_loaded else "keyword-based"
        logger.info(f"Sentiment analysis [{method}] — {len(comments)} comments")

        if not comments:
            return self._empty_sentiment_result()

        positive_comments = []
        negative_comments = []
        neutral_comments = []
        sentiments = []

        for comment in comments:
            if not comment or not str(comment).strip():
                continue
            comment_str = str(comment).strip()
            sentiment = self.classify_comment(comment_str)
            sentiments.append(sentiment)
            
            if sentiment == 'positive':
                positive_comments.append(comment_str)
            elif sentiment == 'negative':
                negative_comments.append(comment_str)
            else:
                neutral_comments.append(comment_str)

        total = len(sentiments)
        if total == 0:
            return self._empty_sentiment_result()

        pos_count = sentiments.count('positive')
        neg_count = sentiments.count('negative')
        neu_count = sentiments.count('neutral')
        
        sentiment_score = round((pos_count + (neu_count * 0.5)) / total, 2)

        # Extract common themes from ALL comments
        all_text = ' '.join([c.lower() for c in comments if c])
        all_words = all_text.split()
        stopwords = {
            'the', 'and', 'is', 'in', 'to', 'of', 'it', 'that', 'was', 'for', 'this',
            'but', 'with', 'as', 'are', 'be', 'at', 'from', 'by', 'an', 'on', 'have',
            'has', 'were', 'had', 'been', 'not', 'very', 'so', 'a', 'i', 'we', 'they',
            'he', 'she', 'you', 'also', 'event', 'program', 'activity', 'activities'
        }
        word_freq = Counter(all_words)
        common_themes = [
            word for word, count in word_freq.most_common(15) 
            if word not in stopwords and len(word) > 3
        ][:10]

        logger.info(
            f"Sentiment results [{method}]: "
            f"{pos_count} positive ({round(pos_count/total*100,1)}%), "
            f"{neg_count} negative ({round(neg_count/total*100,1)}%), "
            f"{neu_count} neutral ({round(neu_count/total*100,1)}%)"
        )

        # Return ALL comments (no limits)
        return {
            'positive_percentage': round((pos_count / total) * 100, 1),
            'negative_percentage': round((neg_count / total) * 100, 1),
            'neutral_percentage': round((neu_count / total) * 100, 1),
            'sentiment_score': sentiment_score,
            'total_comments': total,
            'common_themes': common_themes,
            'positive_comments': positive_comments,  # ALL positive comments
            'negative_comments': negative_comments,  # ALL negative comments
            'neutral_comments': neutral_comments,    # ALL neutral comments
            'method_used': method,
            'model_loaded': self.model_loaded,
        }

    def _empty_sentiment_result(self) -> Dict[str, Any]:
        return {
            'positive_percentage': 0,
            'negative_percentage': 0,
            'neutral_percentage': 0,
            'sentiment_score': 0.5,
            'total_comments': 0,
            'common_themes': [],
            'positive_comments': [],
            'negative_comments': [],
            'neutral_comments': [],
            'method_used': 'none',
            'model_loaded': self.model_loaded,
        }


sentiment_manager = SentimentModelManager()


# ==================== Pydantic Models ====================

class AnalysisRequest(BaseModel):
    data: Optional[Dict[str, Any]] = Field(default_factory=dict)
    year_level: Optional[int] = 1
    respondent_type: Optional[int] = 0
    positive_comments: Optional[List[str]] = []
    suggestion_comments: Optional[List[str]] = []
    total_respondents: Optional[int] = 0
    response_rate: Optional[float] = 0
    event_date: Optional[str] = None


# ==================== API Endpoints ====================

@app.get("/")
async def root():
    return {
        "service": "EventFlow AI Service",
        "version": "3.0.0",
        "status": "healthy",
        "purpose": "NLP Sentiment Analysis Only",
        "methods": {
            "sentiment_analysis": "Logistic Regression NLP (TF-IDF) with keyword fallback"
        },
        "models": {
            "sentiment_model_loaded": sentiment_manager.model_loaded,
        },
        "note": "Satisfaction scores are calculated in Laravel DSS"
    }


@app.get("/health")
async def health_check():
    return {
        "status": "healthy",
        "timestamp": datetime.now().isoformat(),
        "service": "sentiment-analysis-only",
        "sentiment_model_loaded": sentiment_manager.model_loaded,
        "version": "3.0.0"
    }


@app.post("/analyze")
async def analyze_sentiment(request: AnalysisRequest):
    """Sentiment analysis endpoint - analyzes ALL comments with no limits"""
    logger.info(
        f"Sentiment analysis request — "
        f"{len(request.positive_comments or [])} positive comments | "
        f"{len(request.suggestion_comments or [])} suggestion comments"
    )

    try:
        all_comments = list(request.positive_comments or []) + list(request.suggestion_comments or [])
        
        result = sentiment_manager.analyze_sentiment(all_comments)
        
        result['total_respondents'] = request.total_respondents
        result['response_rate'] = request.response_rate
        result['event_date'] = request.event_date
        result['analyzed_at'] = datetime.now().isoformat()
        result['note'] = "Satisfaction scores are calculated in Laravel DSS"
        
        logger.info(f"Sentiment analysis complete — {result['total_comments']} comments processed")
        
        return result

    except Exception as e:
        logger.error(f"Sentiment analysis failed: {e}")
        import traceback
        traceback.print_exc()
        raise HTTPException(status_code=500, detail=f"Sentiment analysis failed: {str(e)}")


@app.post("/test-sentiment")
async def test_sentiment(data: dict):
    comments = data.get('comments', [])
    result = sentiment_manager.analyze_sentiment(comments)
    result['analyzed_at'] = datetime.now().isoformat()
    return result


if __name__ == "__main__":
    import uvicorn
    port = int(os.getenv("PORT", 8001))
    uvicorn.run(app, host="0.0.0.0", port=port)