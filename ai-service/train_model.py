"""
train_model.py - Train Random Forest model on evaluation data
Run this script to create and train the machine learning model
"""

import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestRegressor, RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import mean_squared_error, r2_score, accuracy_score
import joblib
import os
import json
from datetime import datetime

def prepare_training_data():
    """
    Generate synthetic training data that matches your evaluation structure
    In production, you would load real data from your database
    """
    print("🔄 Preparing training data...")
    
    # Set random seed for reproducibility
    np.random.seed(42)
    n_samples = 1000
    
    # Generate synthetic data matching your evaluation structure
    data = {
        # Information Dissemination
        'information_timeliness': np.random.randint(1, 6, n_samples),
        'information_adequacy': np.random.randint(1, 6, n_samples),
        
        # Design of Activity
        'program_order': np.random.randint(1, 6, n_samples),
        'relevance': np.random.randint(1, 6, n_samples),
        'time_allotment': np.random.randint(1, 6, n_samples),
        
        # Outcomes
        'attendance': np.random.randint(1, 6, n_samples),
        'participation': np.random.randint(1, 6, n_samples),
        'interaction': np.random.randint(1, 6, n_samples),
        'teamwork': np.random.randint(1, 6, n_samples),
        
        # Secretariat
        'secretariat_sensitivity': np.random.randint(1, 6, n_samples),
        'secretariat_management': np.random.randint(1, 6, n_samples),
        'secretariat_communication': np.random.randint(1, 6, n_samples),
        
        # Facilities
        'venue_appearance': np.random.randint(1, 6, n_samples),
        'cleanliness': np.random.randint(1, 6, n_samples),
        'equipment_functionality': np.random.randint(1, 6, n_samples),
        
        # Food
        'food_quality': np.random.randint(1, 6, n_samples),
        'food_presentation': np.random.randint(1, 6, n_samples),
        'food_timeliness': np.random.randint(1, 6, n_samples),
        'food_service': np.random.randint(1, 6, n_samples),
        'food_sufficiency': np.random.randint(1, 6, n_samples),
        'food_quantity': np.random.randint(1, 6, n_samples),
        
        # Demographics
        'year_level': np.random.choice([1, 2, 3, 4], n_samples),
        'respondent_type': np.random.choice([0, 1, 2], n_samples),
        
        # Target variables
        'overall_satisfaction': np.random.uniform(1, 5, n_samples),
        'event_success': np.random.choice([0, 1], n_samples, p=[0.3, 0.7]),
    }
    
    df = pd.DataFrame(data)
    
    # Add realistic patterns to make the model learn meaningful relationships
    # If food quality is high, overall satisfaction tends to be higher
    df.loc[df['food_quality'] >= 4, 'overall_satisfaction'] += 1
    df.loc[df['food_quality'] >= 4, 'overall_satisfaction'] = df.loc[df['food_quality'] >= 4, 'overall_satisfaction'].clip(1, 5)
    
    # If venue scores are low, event success is less likely
    df.loc[df['venue_appearance'] <= 2, 'event_success'] = 0
    
    # If secretariat communication is low, overall satisfaction drops
    df.loc[df['secretariat_communication'] <= 2, 'overall_satisfaction'] -= 0.5
    df.loc[df['secretariat_communication'] <= 2, 'overall_satisfaction'] = df.loc[df['secretariat_communication'] <= 2, 'overall_satisfaction'].clip(1, 5)
    
    print(f"✅ Generated {n_samples} training samples")
    return df

def train_random_forest_models(df):
    """
    Train Random Forest models for both regression and classification
    """
    print("\n🌲 Training Random Forest models...")
    
    # Separate features and targets
    feature_columns = [col for col in df.columns if col not in ['overall_satisfaction', 'event_success']]
    X = df[feature_columns]
    y_reg = df['overall_satisfaction']  # For regression (predict satisfaction score)
    y_cls = df['event_success']          # For classification (predict success/failure)
    
    # Split data into training and testing sets
    X_train, X_test, y_reg_train, y_reg_test, y_cls_train, y_cls_test = train_test_split(
        X, y_reg, y_cls, test_size=0.2, random_state=42
    )
    
    print(f"📊 Training samples: {len(X_train)}, Testing samples: {len(X_test)}")
    
    # 1. Random Forest Regressor - predicts overall satisfaction score
    print("   Training Regression Model (predicts satisfaction scores)...")
    rf_regressor = RandomForestRegressor(
        n_estimators=100,      # Number of trees in the forest
        max_depth=10,          # Maximum depth of trees (prevents overfitting)
        min_samples_split=5,   # Minimum samples required to split a node
        min_samples_leaf=2,    # Minimum samples required at a leaf node
        random_state=42,
        n_jobs=-1              # Use all CPU cores
    )
    rf_regressor.fit(X_train, y_reg_train)
    
    # Evaluate regression model
    y_reg_pred = rf_regressor.predict(X_test)
    mse = mean_squared_error(y_reg_test, y_reg_pred)
    r2 = r2_score(y_reg_test, y_reg_pred)
    print(f"   ✓ Regression Model - MSE: {mse:.4f}, R²: {r2:.4f}")
    
    # 2. Random Forest Classifier - predicts event success (Yes/No)
    print("   Training Classification Model (predicts success/failure)...")
    rf_classifier = RandomForestClassifier(
        n_estimators=100,
        max_depth=8,
        min_samples_split=5,
        min_samples_leaf=2,
        random_state=42,
        n_jobs=-1
    )
    rf_classifier.fit(X_train, y_cls_train)
    
    # Evaluate classification model
    y_cls_pred = rf_classifier.predict(X_test)
    accuracy = accuracy_score(y_cls_test, y_cls_pred)
    print(f"   ✓ Classification Model - Accuracy: {accuracy:.4f}")
    
    # Get feature importance (THIS IS WHAT TELLS YOU WHAT TO IMPROVE)
    feature_importance = pd.DataFrame({
        'feature': feature_columns,
        'importance_regressor': rf_regressor.feature_importances_,
        'importance_classifier': rf_classifier.feature_importances_
    }).sort_values('importance_regressor', ascending=False)
    
    print("\n📊 TOP 10 MOST IMPORTANT FACTORS (What affects satisfaction most):")
    print("=" * 60)
    for i, row in feature_importance.head(10).iterrows():
        print(f"   {i+1}. {row['feature'].replace('_', ' ').title()}: {row['importance_regressor']:.2%}")
    print("=" * 60)
    
    return rf_regressor, rf_classifier, feature_importance, feature_columns

def save_models(rf_regressor, rf_classifier, feature_columns, feature_importance):
    """
    Save trained models and metadata to files
    """
    print("\n💾 Saving models...")
    
    # Create models directory if it doesn't exist
    os.makedirs('models', exist_ok=True)
    
    # Save the trained models
    joblib.dump(rf_regressor, 'models/rf_regressor.pkl')
    joblib.dump(rf_classifier, 'models/rf_classifier.pkl')
    print("   ✓ Model files saved")
    
    # Save feature columns for reference
    with open('models/feature_columns.json', 'w') as f:
        json.dump(feature_columns, f)
    print("   ✓ Feature columns saved")
    
    # Save feature importance for insights
    feature_importance.to_csv('models/feature_importance.csv', index=False)
    print("   ✓ Feature importance saved")
    
    # Save metadata
    metadata = {
        'training_date': datetime.now().isoformat(),
        'n_features': len(feature_columns),
        'regressor_type': 'RandomForestRegressor',
        'classifier_type': 'RandomForestClassifier',
        'n_estimators': 100,
        'max_depth_regressor': 10,
        'max_depth_classifier': 8
    }
    with open('models/metadata.json', 'w') as f:
        json.dump(metadata, f, indent=2)
    print("   ✓ Metadata saved")
    
    print(f"\n✅ All models saved to: {os.path.abspath('models')}")

def main():
    print("=" * 60)
    print("🌳 RANDOM FOREST MODEL TRAINING")
    print("=" * 60)
    
    # Prepare training data
    df = prepare_training_data()
    print(f"\n📊 Dataset shape: {df.shape[0]} rows, {df.shape[1]} columns")
    
    # Train the models
    rf_regressor, rf_classifier, feature_importance, feature_columns = train_random_forest_models(df)
    
    # Save the trained models
    save_models(rf_regressor, rf_classifier, feature_columns, feature_importance)
    
    print("\n" + "=" * 60)
    print("✨ TRAINING COMPLETE! ✨")
    print("=" * 60)
    print("\nNext steps:")
    print("1. Start the FastAPI service with: uvicorn app.main:app --reload --port 8001")
    print("2. The AI service will use these models to analyze your evaluations")
    print("3. Models are saved in the 'models' folder")

if __name__ == "__main__":
    main()