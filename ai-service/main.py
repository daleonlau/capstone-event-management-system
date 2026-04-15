"""
EventFlow AI Service - XLM-RoBERTa Sentiment Analysis
Supports: Pre-trained model + Fine-tuned model (with synthetic data)
"""

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List, Dict, Any, Optional
from transformers import pipeline, XLMRobertaTokenizer, XLMRobertaForSequenceClassification
from peft import PeftModel
import torch
import uvicorn
import os
import logging
import re
import string
from datetime import datetime
from collections import Counter

# ============================================================
# LOGGING
# ============================================================

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

# ============================================================
# INITIALIZE FASTAPI APP
# ============================================================

app = FastAPI(
    title="EventFlow AI Service",
    description="XLM-RoBERTa Sentiment Analysis for Event Feedback",
    version="2.0.0"
)

# Enable CORS for Laravel integration
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ============================================================
# CONFIGURATION
# ============================================================

BASE_MODEL = "FacebookAI/xlm-roberta-base"
FINETUNED_PATH = "./lora_finetuned/"
FALLBACK_MODEL = "cardiffnlp/twitter-xlm-roberta-base-sentiment"

# ============================================================
# LOAD MODEL (Fine-tuned if exists, otherwise pre-trained)
# ============================================================

logger.info("Initializing EventFlow AI Service...")

device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
logger.info(f"Using device: {device}")

using_finetuned = False
sentiment_analyzer = None
tokenizer = None
model = None
label_map = {0: "Negative", 1: "Neutral", 2: "Positive"}

# Check if fine-tuned model exists
if os.path.exists(FINETUNED_PATH):
    # Check for adapter file (either .bin or .safetensors)
    adapter_bin = os.path.join(FINETUNED_PATH, "adapter_model.bin")
    adapter_safetensors = os.path.join(FINETUNED_PATH, "adapter_model.safetensors")
    config_file = os.path.join(FINETUNED_PATH, "adapter_config.json")
    
    has_adapter = os.path.exists(adapter_bin) or os.path.exists(adapter_safetensors)
    
    if has_adapter and os.path.exists(config_file):
        logger.info(f"Loading fine-tuned model from {FINETUNED_PATH}")
        
        try:
            tokenizer = XLMRobertaTokenizer.from_pretrained(BASE_MODEL)
            base_model = XLMRobertaForSequenceClassification.from_pretrained(
                BASE_MODEL,
                num_labels=3,
                ignore_mismatched_sizes=True
            )
            model = PeftModel.from_pretrained(base_model, FINETUNED_PATH)
            model.eval()
            model.to(device)
            
            using_finetuned = True
            logger.info("Fine-tuned model loaded successfully")
            
        except Exception as e:
            logger.error(f"Error loading fine-tuned model: {e}")
            using_finetuned = False
    else:
        logger.warning("Fine-tuned model files incomplete, falling back to pre-trained")
        using_finetuned = False
else:
    logger.info("Fine-tuned model not found, falling back to pre-trained")
    using_finetuned = False

if not using_finetuned:
    logger.info(f"Loading pre-trained model: {FALLBACK_MODEL}")
    
    sentiment_analyzer = pipeline(
        "sentiment-analysis",
        model=FALLBACK_MODEL,
        device=0 if torch.cuda.is_available() else -1
    )
    logger.info("Pre-trained model loaded successfully")

# ============================================================
# REQUEST AND RESPONSE MODELS
# ============================================================

class AnalyzeRequest(BaseModel):
    """Request format for sentiment analysis"""
    positive_comments: List[str] = []
    suggestion_comments: List[str] = []
    total_respondents: Optional[int] = 0
    response_rate: Optional[float] = 0
    event_date: Optional[str] = None

# ============================================================
# SENTIMENT ANALYSIS FUNCTIONS
# ============================================================

def classify_sentiment_finetuned(comment: str) -> str:
    """Classify using fine-tuned LoRA model"""
    try:
        if not comment or len(comment.strip()) < 2:
            return 'Neutral'
        
        inputs = tokenizer(comment, return_tensors="pt", truncation=True, max_length=64)
        inputs = {k: v.to(device) for k, v in inputs.items()}
        
        with torch.no_grad():
            outputs = model(**inputs)
            pred = torch.argmax(outputs.logits, dim=1).item()
        
        return label_map[pred]
        
    except Exception as e:
        logger.error(f"Error in fine-tuned classification: {e}")
        return 'Neutral'

def classify_sentiment_pretrained(comment: str) -> str:
    """Classify using pre-trained model"""
    try:
        if not comment or len(comment.strip()) < 2:
            return 'Neutral'
        
        result = sentiment_analyzer(comment)[0]
        label = result['label'].lower()
        
        if label == 'positive':
            return 'Positive'
        elif label == 'negative':
            return 'Negative'
        else:
            return 'Neutral'
            
    except Exception as e:
        logger.error(f"Error in pre-trained classification: {e}")
        return 'Neutral'

# Select the appropriate classification function
if using_finetuned:
    classify_sentiment = classify_sentiment_finetuned
    method_name = "XLM-RoBERTa (Fine-tuned on Event Data + LoRA)"
else:
    classify_sentiment = classify_sentiment_pretrained
    method_name = "XLM-RoBERTa (Pre-trained)"

# ============================================================
# HELPER FUNCTIONS
# ============================================================

def extract_common_themes(comments: List[str]) -> List[str]:
    """Extract common themes from comments"""
    if not comments:
        return []
    
    all_text = ' '.join([c.lower() for c in comments if c])
    words = re.findall(r'\b[a-z]{4,}\b', all_text)
    
    stopwords = {
        'the', 'and', 'is', 'in', 'to', 'of', 'it', 'that', 'was', 'for', 'this',
        'but', 'with', 'as', 'are', 'be', 'at', 'from', 'by', 'an', 'on', 'have',
        'has', 'were', 'had', 'been', 'not', 'very', 'so', 'a', 'i', 'we', 'they',
        'he', 'she', 'you', 'also', 'event', 'program', 'activity', 'activities',
        'can', 'will', 'would', 'could', 'should', 'because', 'then', 'than'
    }
    
    word_freq = Counter([w for w in words if w not in stopwords])
    common_themes = [word for word, count in word_freq.most_common(10)][:10]
    
    return common_themes

# ============================================================
# API ENDPOINTS
# ============================================================

@app.get("/")
async def root():
    """Root endpoint - service information"""
    return {
        "service": "EventFlow AI Service",
        "version": "2.0.0",
        "status": "running",
        "model_type": method_name,
        "fine_tuned": using_finetuned
    }

@app.get("/health")
async def health():
    """Health check endpoint"""
    return {
        "status": "healthy",
        "timestamp": datetime.now().isoformat(),
        "fine_tuned": using_finetuned,
        "model_loaded": True
    }

@app.post("/analyze")
async def analyze_comments(request: AnalyzeRequest):
    """
    Analyze comments and return sentiment classification.
    """
    all_comments = []
    
    for comment in request.positive_comments:
        if comment and comment.strip():
            all_comments.append(comment.strip())
    
    for comment in request.suggestion_comments:
        if comment and comment.strip():
            all_comments.append(comment.strip())
    
    if not all_comments:
        return {
            "method_used": method_name,
            "sentiment_score": 0.5,
            "positive_percentage": 0,
            "negative_percentage": 0,
            "neutral_percentage": 0,
            "total_comments": 0,
            "common_themes": [],
            "positive_comments": [],
            "negative_comments": [],
            "neutral_comments": [],
            "fine_tuned": using_finetuned,
            "total_respondents": request.total_respondents,
            "response_rate": request.response_rate,
            "event_date": request.event_date,
            "analyzed_at": datetime.now().isoformat()
        }
    
    positive_results = []
    negative_results = []
    neutral_results = []
    
    for comment in all_comments:
        sentiment = classify_sentiment(comment)
        
        if sentiment == "Positive":
            positive_results.append(comment)
        elif sentiment == "Negative":
            negative_results.append(comment)
        else:
            neutral_results.append(comment)
    
    total = len(all_comments)
    pos_count = len(positive_results)
    neg_count = len(negative_results)
    neu_count = len(neutral_results)
    
    positive_percentage = round((pos_count / total) * 100, 1) if total > 0 else 0
    negative_percentage = round((neg_count / total) * 100, 1) if total > 0 else 0
    neutral_percentage = round((neu_count / total) * 100, 1) if total > 0 else 0
    
    sentiment_score = round((pos_count + (neu_count * 0.5)) / total, 2) if total > 0 else 0.5
    
    common_themes = extract_common_themes(all_comments)
    
    logger.info(f"Sentiment analysis complete: {pos_count} pos, {neg_count} neg")
    
    return {
        "method_used": method_name,
        "sentiment_score": sentiment_score,
        "positive_percentage": positive_percentage,
        "negative_percentage": negative_percentage,
        "neutral_percentage": neutral_percentage,
        "total_comments": total,
        "common_themes": common_themes,
        "positive_comments": positive_results,
        "negative_comments": negative_results,
        "neutral_comments": neutral_results,
        "fine_tuned": using_finetuned,
        "total_respondents": request.total_respondents,
        "response_rate": request.response_rate,
        "event_date": request.event_date,
        "analyzed_at": datetime.now().isoformat()
    }

# ============================================================
# RUN THE SERVICE
# ============================================================

if __name__ == "__main__":
    port = int(os.environ.get("PORT", 8001))
    uvicorn.run(app, host="0.0.0.0", port=port, log_level="info")