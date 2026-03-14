"""
app/main.py - FastAPI service for event evaluation analysis
Run with: uvicorn app.main:app --reload --port 8001
"""

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import numpy as np
import pandas as pd
import json
import os
from typing import List, Dict, Any, Optional
from datetime import datetime
import logging

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Initialize FastAPI app
app = FastAPI(
    title="Event Evaluation AI Service",
    description="Analysis for event evaluations with 6 categories",
    version="2.0.0"
)

# Enable CORS for Laravel
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

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
    
    # Metadata
    total_respondents: Optional[int] = 0
    response_rate: Optional[float] = 0

    class Config:
        json_schema_extra = {
            "example": {
                "info_timeliness": 4.5,
                "info_adequacy": 4.2,
                "design_program": 4.3,
                "design_relevance": 4.4,
                "design_pacing": 3.9,
                "outcomes_attendance": 4.1,
                "outcomes_participation": 4.2,
                "outcomes_interaction": 4.0,
                "outcomes_teamwork": 4.3,
                "secretariat_sensitivity": 3.8,
                "secretariat_management": 3.9,
                "secretariat_communication": 3.7,
                "facilities_appearance": 4.2,
                "facilities_cleanliness": 4.1,
                "facilities_equipment": 3.8,
                "food_quality": 4.0,
                "food_presentation": 4.1,
                "food_timeliness": 3.7,
                "food_service": 3.9,
                "food_sufficiency": 4.0,
                "food_quantity": 3.8,
                "year_level": 2,
                "respondent_type": 0,
                "total_respondents": 85,
                "response_rate": 0.85
            }
        }

class InsightResponse(BaseModel):
    summary: str
    strengths: List[str]
    weaknesses: List[str]
    recommendations: List[str]
    predicted_satisfaction: float
    success_probability: float
    category_breakdown: Dict[str, float]
    response_rate: float
    total_respondents: int
    analyzed_at: str

# ==================== Helper Functions ====================

def calculate_category_scores(evaluation: EvaluationData) -> Dict[str, float]:
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
        category_scores[category] = round(np.mean(scores) if scores else 0, 1)
    
    return category_scores

def generate_insights(evaluation: EvaluationData) -> InsightResponse:
    """
    Generate insights based on actual evaluation data
    """
    data = evaluation.dict()
    category_scores = calculate_category_scores(evaluation)
    
    # Calculate overall satisfaction
    all_ratings = []
    for key, value in data.items():
        if key not in ['year_level', 'respondent_type', 'total_respondents', 'response_rate'] and isinstance(value, (int, float)) and value > 0:
            all_ratings.append(value)
    
    overall_satisfaction = round(np.mean(all_ratings), 2) if all_ratings else 2.5
    success_prob = round(overall_satisfaction / 5, 2)
    
    # Identify strengths (categories with scores >= 3.5)
    strengths = []
    for category, score in category_scores.items():
        if score >= 3.5:
            strengths.append(f"✓ {category}: {score}/5.0")
    
    # Identify weaknesses (categories with scores < 3.5 and > 0)
    weaknesses = []
    for category, score in category_scores.items():
        if 0 < score < 3.5:
            weaknesses.append(f"⚠️ {category}: {score}/5.0 - Needs improvement")
        elif score == 0:
            weaknesses.append(f"📊 {category}: No data available")
    
    # Generate specific recommendations
    recommendations = []
    
    # Information Dissemination
    if category_scores.get("Information Dissemination", 0) < 3.5 and category_scores.get("Information Dissemination", 0) > 0:
        recommendations.append("📢 **Improve Information Dissemination** - Send invites earlier and ensure adequate information reaches all participants")
    
    # Design of Activity
    if category_scores.get("Design of Activity", 0) < 3.5 and category_scores.get("Design of Activity", 0) > 0:
        recommendations.append("📋 **Enhance Activity Design** - Review program flow, relevance of activities, and time allocation")
    
    # Outcomes
    if category_scores.get("Outcomes", 0) < 3.5 and category_scores.get("Outcomes", 0) > 0:
        recommendations.append("👥 **Boost Participant Outcomes** - Focus on improving attendance, participation, interaction, and teamwork")
    
    # Secretariat
    if category_scores.get("Secretariat", 0) < 3.5 and category_scores.get("Secretariat", 0) > 0:
        recommendations.append("👤 **Staff Training Needed** - Secretariat should improve sensitivity, management, and communication")
    
    # Facilities
    if category_scores.get("Facilities", 0) < 3.5 and category_scores.get("Facilities", 0) > 0:
        recommendations.append("🏛️ **Upgrade Facilities** - Enhance venue appearance, cleanliness, and equipment functionality")
    
    # Food
    if category_scores.get("Food", 0) < 3.5 and category_scores.get("Food", 0) > 0:
        recommendations.append("🍽️ **Improve Food Service** - Focus on quality, presentation, timeliness, and sufficiency")
    
    # Overall recommendations based on satisfaction
    if overall_satisfaction >= 4.0:
        recommendations.append(f"🎉 **Excellent Event!** Overall satisfaction is {overall_satisfaction}/5.0. Maintain these high standards.")
    elif overall_satisfaction >= 3.0:
        recommendations.append(f"📊 **Good Event with Room for Improvement** - Overall satisfaction is {overall_satisfaction}/5.0. Address the weaknesses above.")
    else:
        recommendations.append(f"⚠️ **Needs Significant Improvement** - Overall satisfaction is {overall_satisfaction}/5.0. Focus on all categories.")
    
    # Response rate note
    if evaluation.response_rate < 0.75:
        recommendations.append(f"📝 **Note:** Only {evaluation.response_rate*100:.0f}% response rate. Consider reopening to get more feedback.")
    
    # Generate summary
    summary = f"Overall satisfaction: {overall_satisfaction}/5.0 based on {evaluation.total_respondents} respondents ({evaluation.response_rate*100:.0f}% response rate). "
    
    if overall_satisfaction >= 4.0:
        summary += "Attendees are highly satisfied."
    elif overall_satisfaction >= 3.0:
        summary += "Attendees are moderately satisfied."
    else:
        summary += "Attendees are dissatisfied - improvements needed."
    
    return InsightResponse(
        summary=summary,
        strengths=strengths if strengths else ["No strengths identified yet"],
        weaknesses=weaknesses if weaknesses else ["Waiting for more data"],
        recommendations=recommendations if recommendations else ["Collect more responses for better insights"],
        predicted_satisfaction=overall_satisfaction,
        success_probability=success_prob,
        category_breakdown=category_scores,
        response_rate=evaluation.response_rate,
        total_respondents=evaluation.total_respondents,
        analyzed_at=datetime.now().isoformat()
    )

# ==================== API Endpoints ====================

@app.get("/")
async def root():
    return {
        "service": "Event Evaluation AI Service v2.0",
        "status": "healthy",
        "categories": ["Information Dissemination", "Design of Activity", "Outcomes", 
                      "Secretariat", "Facilities", "Food"]
    }

@app.post("/analyze", response_model=InsightResponse)
async def analyze_evaluation(evaluation: EvaluationData):
    """
    Analyze evaluation data and generate insights
    """
    logger.info(f"📥 Received evaluation data: {evaluation.dict()}")
    
    try:
        insights = generate_insights(evaluation)
        logger.info(f"✅ Analysis complete: satisfaction={insights.predicted_satisfaction}")
        return insights
        
    except Exception as e:
        logger.error(f"❌ Analysis failed: {e}")
        return InsightResponse(
            summary="Analysis completed with errors",
            strengths=["Data received"],
            weaknesses=["Unable to perform detailed analysis"],
            recommendations=["Please try again later"],
            predicted_satisfaction=2.5,
            success_probability=0.5,
            category_breakdown={},
            response_rate=0,
            total_respondents=0,
            analyzed_at=datetime.now().isoformat()
        )

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001)