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

print("=" * 70)
print("EventFlow AI Service - Sentiment Analysis")
print("=" * 70)

device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
print(f"\n💻 Using device: {device}")

using_finetuned = False
sentiment_analyzer = None
tokenizer = None
model = None
label_map = {0: "Negative", 1: "Neutral", 2: "Positive"}

# Check if fine-tuned model exists
print(f"\n📁 Checking for fine-tuned model at: {FINETUNED_PATH}")

if os.path.exists(FINETUNED_PATH):
    print(f"   ✅ Folder exists")
    
    # Check for adapter file (either .bin or .safetensors)
    adapter_bin = os.path.join(FINETUNED_PATH, "adapter_model.bin")
    adapter_safetensors = os.path.join(FINETUNED_PATH, "adapter_model.safetensors")
    config_file = os.path.join(FINETUNED_PATH, "adapter_config.json")
    
    has_adapter = False
    if os.path.exists(adapter_bin):
        print(f"   ✅ Found adapter_model.bin")
        has_adapter = True
    elif os.path.exists(adapter_safetensors):
        print(f"   ✅ Found adapter_model.safetensors")
        has_adapter = True
    else:
        print(f"   ❌ No adapter file found")
    
    if os.path.exists(config_file):
        print(f"   ✅ Found adapter_config.json")
    else:
        print(f"   ❌ adapter_config.json NOT FOUND")
    
    if has_adapter and os.path.exists(config_file):
        print(f"\n📚 Loading FINE-TUNED model from {FINETUNED_PATH}")
        print(f"   Base model: {BASE_MODEL}")
        print(f"   This model combines pre-trained knowledge + your synthetic data")
        
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
            print("\n✅ Fine-tuned model loaded successfully!")
            print("   Model has been trained on event feedback data")
            
        except Exception as e:
            print(f"\n⚠️ Error loading fine-tuned model: {e}")
            print("   Falling back to pre-trained model...")
            using_finetuned = False
    else:
        print(f"\n⚠️ Fine-tuned model files incomplete")
        print("   Falling back to pre-trained model...")
else:
    print(f"   ❌ Folder does NOT exist")
    print(f"   Falling back to pre-trained model...")

if not using_finetuned:
    print(f"\n📚 Loading PRE-TRAINED model from HuggingFace")
    print(f"   Model: {FALLBACK_MODEL}")
    print("   This is the base model without fine-tuning")
    
    sentiment_analyzer = pipeline(
        "sentiment-analysis",
        model=FALLBACK_MODEL,
        device=0 if torch.cuda.is_available() else -1
    )
    print("\n✅ Pre-trained model loaded successfully!")

print("\n" + "=" * 70)
print()

# Test the model
if using_finetuned:
    test_comment = "Nindot kaayo ang event!"
    inputs = tokenizer(test_comment, return_tensors="pt", truncation=True, max_length=64)
    inputs = {k: v.to(device) for k, v in inputs.items()}
    with torch.no_grad():
        outputs = model(**inputs)
        pred = torch.argmax(outputs.logits, dim=1).item()
    print(f"🔍 Model test: '{test_comment}' -> {label_map[pred]}")
else:
    test_result = sentiment_analyzer("Nindot kaayo ang event!")[0]
    print(f"🔍 Model test: 'Nindot kaayo ang event!' -> {test_result['label']}")

print("=" * 70)
print()

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
        "model_type": "Fine-tuned XLM-RoBERTa + LoRA" if using_finetuned else "Pre-trained XLM-RoBERTa",
        "fine_tuned": using_finetuned,
        "capabilities": ["Positive", "Neutral", "Negative"],
        "languages_supported": ["Bisaya", "Tagalog", "English", "Code-switching"]
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
    
    # Combine all comments from both input arrays
    all_comments = []
    
    # Add positive_comments
    for comment in request.positive_comments:
        if comment and comment.strip():
            all_comments.append(comment.strip())
    
    # Add suggestion_comments
    for comment in request.suggestion_comments:
        if comment and comment.strip():
            all_comments.append(comment.strip())
    
    # Handle empty request
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
    
    # Classify each comment
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
    
    # Calculate percentages
    total = len(all_comments)
    pos_count = len(positive_results)
    neg_count = len(negative_results)
    neu_count = len(neutral_results)
    
    positive_percentage = round((pos_count / total) * 100, 1) if total > 0 else 0
    negative_percentage = round((neg_count / total) * 100, 1) if total > 0 else 0
    neutral_percentage = round((neu_count / total) * 100, 1) if total > 0 else 0
    
    # Calculate sentiment score
    sentiment_score = round((pos_count + (neu_count * 0.5)) / total, 2) if total > 0 else 0.5
    
    # Extract common themes
    common_themes = extract_common_themes(all_comments)
    
    logger.info(f"Sentiment analysis complete: {pos_count} positive, {neg_count} negative, {neu_count} neutral")
    
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
        "analyzed_at": datetime.now().isoformat(),
        "note": "Satisfaction scores are calculated in Laravel DSS"
    }

@app.post("/test-sentiment")
async def test_sentiment(data: dict):
    """Test endpoint for debugging"""
    comments = data.get('comments', [])
    
    results = []
    for comment in comments:
        sentiment = classify_sentiment(comment)
        results.append({
            "comment": comment,
            "sentiment": sentiment
        })
    
    return {
        "results": results,
        "method_used": method_name,
        "fine_tuned": using_finetuned,
        "analyzed_at": datetime.now().isoformat()
    }

# ============================================================
# RUN THE SERVICE
# ============================================================

if __name__ == "__main__":
    port = int(os.environ.get("PORT", 8001))
    print(f"\n🚀 Starting server on port {port}...")
    print(f"   Local: http://127.0.0.1:{port}")
    print(f"   Health check: http://127.0.0.1:{port}/health")
    print("=" * 70)
    
    uvicorn.run(
        app,
        host="0.0.0.0",
        port=port,
        log_level="info"
    )