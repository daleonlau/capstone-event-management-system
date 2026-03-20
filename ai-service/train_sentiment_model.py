"""
train_sentiment_model.py - Train supervised NLP model for sentiment analysis
Use this script with your labeled dataset of comments
"""

import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.naive_bayes import MultinomialNB
from sklearn.svm import SVC
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix
from sklearn.preprocessing import LabelEncoder
import joblib
import os
import json
import re
import string
from typing import Tuple, List

class SentimentTrainer:
    def __init__(self, data_path: str = None):
        """
        Initialize trainer with path to your labeled dataset
        Your CSV should have columns: 'comment' and 'sentiment'
        sentiment values: 'positive', 'negative', 'neutral'
        """
        self.data_path = data_path
        self.models = {}
        self.results = {}
        self.best_model = None
        self.vectorizer = None
        self.label_encoder = LabelEncoder()
        self.X_train_vec = None
        self.X_test_vec = None
        self.y_train = None
        self.y_test = None
        
    def load_data(self) -> pd.DataFrame:
        """Load your labeled dataset from CSV or Excel"""
        print("📂 Loading dataset...")
        
        if self.data_path.endswith('.csv'):
            df = pd.read_csv(self.data_path)
        elif self.data_path.endswith(('.xlsx', '.xls')):
            df = pd.read_excel(self.data_path)
        else:
            raise ValueError("Unsupported file format. Use CSV or Excel.")
        
        # Check required columns
        required_cols = ['comment', 'sentiment']
        for col in required_cols:
            if col not in df.columns:
                raise ValueError(f"Missing required column: {col}")
        
        print(f"✅ Loaded {len(df)} labeled comments")
        print(f"📊 Sentiment distribution:")
        print(df['sentiment'].value_counts())
        
        return df
    
    def preprocess_text(self, text: str) -> str:
        """Clean and preprocess text for training"""
        if pd.isna(text) or not isinstance(text, str):
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
    
    def prepare_data(self, df: pd.DataFrame) -> Tuple:
        """Prepare data for training"""
        print("\n🔧 Preparing data for training...")
        
        # Preprocess comments
        df['processed_comment'] = df['comment'].apply(self.preprocess_text)
        
        # Remove empty comments
        df = df[df['processed_comment'] != ""]
        
        # Encode labels
        y = self.label_encoder.fit_transform(df['sentiment'])
        
        print(f"Classes: {self.label_encoder.classes_.tolist()}")
        
        return df['processed_comment'].values, y
    
    def train_models(self, X, y):
        """Train multiple models and select the best one"""
        print("\n🚀 Training multiple models...")
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.2, random_state=42, stratify=y
        )
        
        # Store for later use
        self.y_train = y_train
        self.y_test = y_test
        
        # Create TF-IDF vectorizer
        print("📊 Creating TF-IDF features...")
        self.vectorizer = TfidfVectorizer(
            max_features=2000,
            min_df=2,
            max_df=0.8,
            ngram_range=(1, 2),
            stop_words='english'
        )
        
        self.X_train_vec = self.vectorizer.fit_transform(X_train)
        self.X_test_vec = self.vectorizer.transform(X_test)
        
        print(f"Feature matrix shape: {self.X_train_vec.shape}")
        
        # Define models to try
        models = {
            'Logistic Regression': LogisticRegression(
                max_iter=1000,
                C=1.0,
                class_weight='balanced',
                random_state=42
            ),
            'Naive Bayes': MultinomialNB(),
            'SVM (Linear)': SVC(
                kernel='linear',
                probability=True,
                class_weight='balanced',
                random_state=42
            )
        }
        
        # Train and evaluate each model
        for name, model in models.items():
            print(f"\n📈 Training {name}...")
            
            # Train
            model.fit(self.X_train_vec, y_train)
            
            # Predict
            y_pred = model.predict(self.X_test_vec)
            accuracy = accuracy_score(y_test, y_pred)
            
            # Store
            self.models[name] = model
            self.results[name] = {
                'accuracy': accuracy,
                'model': model
            }
            
            print(f"✅ {name} Accuracy: {accuracy:.4f}")
            print(f"Classification Report:")
            print(classification_report(
                y_test, 
                y_pred, 
                target_names=self.label_encoder.classes_
            ))
        
        # Select best model
        best_model_name = max(self.results, key=lambda x: self.results[x]['accuracy'])
        self.best_model = self.results[best_model_name]['model']
        best_accuracy = self.results[best_model_name]['accuracy']
        
        print("\n" + "=" * 50)
        print(f"🏆 BEST MODEL: {best_model_name} with accuracy {best_accuracy:.4f}")
        print("=" * 50)
        
        return self.best_model
    
    def save_models(self):
        """Save trained models and metadata"""
        print("\n💾 Saving models...")
        
        os.makedirs('models', exist_ok=True)
        
        # Save best sentiment model
        joblib.dump(self.best_model, 'models/sentiment_model.pkl')
        joblib.dump(self.vectorizer, 'models/vectorizer.pkl')
        joblib.dump(self.label_encoder, 'models/label_encoder.pkl')
        
        # Train and save a logistic regression model for feature importance
        # This is separate from the best model in case another algorithm performed better
        print("\n📊 Training dedicated Logistic Regression model for feature importance...")
        logistic_model = LogisticRegression(max_iter=1000, class_weight='balanced', random_state=42)
        logistic_model.fit(self.X_train_vec, self.y_train)
        joblib.dump(logistic_model, 'models/logistic_model.pkl')
        
        # Evaluate logistic regression
        y_pred_logistic = logistic_model.predict(self.X_test_vec)
        logistic_accuracy = accuracy_score(self.y_test, y_pred_logistic)
        print(f"✅ Logistic Regression accuracy: {logistic_accuracy:.4f}")
        
        # Save model comparison results
        results_summary = {
            name: {
                'accuracy': self.results[name]['accuracy'],
                'model_type': type(self.results[name]['model']).__name__
            }
            for name in self.results
        }
        
        metadata = {
            'best_model': type(self.best_model).__name__,
            'best_accuracy': self.results[max(self.results, key=lambda x: self.results[x]['accuracy'])]['accuracy'],
            'logistic_regression_accuracy': logistic_accuracy,
            'classes': self.label_encoder.classes_.tolist(),
            'vectorizer_params': {
                'max_features': self.vectorizer.max_features,
                'ngram_range': self.vectorizer.ngram_range
            },
            'model_comparison': results_summary,
            'training_date': pd.Timestamp.now().isoformat()
        }
        
        with open('models/metadata.json', 'w') as f:
            json.dump(metadata, f, indent=2)
        
        print(f"\n✅ Sentiment model saved to: models/sentiment_model.pkl")
        print(f"✅ Logistic model saved to: models/logistic_model.pkl")
        print(f"✅ Vectorizer saved to: models/vectorizer.pkl")
        print(f"✅ Label encoder saved to: models/label_encoder.pkl")
        print(f"✅ Metadata saved to: models/metadata.json")
        
        return metadata

def main():
    print("=" * 60)
    print("🧠 SUPERVISED NLP SENTIMENT ANALYSIS TRAINER")
    print("=" * 60)
    
    # ============================================
    # UPDATE THIS PATH TO YOUR DATASET
    # ============================================
    DATASET_PATH = "your_labeled_comments.csv"  # Change this to your file path
    
    if not os.path.exists(DATASET_PATH):
        print(f"\n❌ Dataset not found at: {DATASET_PATH}")
        print("\nPlease update the DATASET_PATH variable with the correct path to your labeled comments file.")
        print("\nYour CSV should have at least these columns:")
        print("  - 'comment': The comment text")
        print("  - 'sentiment': Label (positive, negative, neutral)")
        
        # Create sample dataset for demonstration
        print("\n🔄 Creating sample dataset for demonstration...")
        sample_data = {
            'comment': [
                "The event was amazing and well organized!",
                "Great food and excellent venue",
                "The speakers were informative and engaging",
                "Very disappointed with the lack of organization",
                "The food was terrible and the venue was too small",
                "I didn't like how long the program was",
                "It was okay, nothing special",
                "Average event, could be better",
                "Not bad but not great either",
                "The secretariat staff were very helpful",
                "The registration process was smooth",
                "Waiting time was too long",
                "Poor acoustics made it hard to hear",
                "The event was informative but a bit boring"
            ],
            'sentiment': [
                'positive', 'positive', 'positive', 'negative', 'negative',
                'negative', 'neutral', 'neutral', 'neutral', 'positive',
                'positive', 'negative', 'negative', 'neutral'
            ]
        }
        df = pd.DataFrame(sample_data)
        df.to_csv('sample_dataset.csv', index=False)
        DATASET_PATH = 'sample_dataset.csv'
        print(f"✅ Sample dataset created at: sample_dataset.csv")
    
    # Initialize trainer
    trainer = SentimentTrainer(DATASET_PATH)
    
    try:
        # Load data
        df = trainer.load_data()
        
        # Prepare data
        X, y = trainer.prepare_data(df)
        
        # Train models (using the new method name)
        best_model = trainer.train_models(X, y)
        
        # Save models
        metadata = trainer.save_models()
        
        print("\n" + "=" * 60)
        print("✅ TRAINING COMPLETE! Models are ready for deployment.")
        print("=" * 60)
        print("\nNext steps:")
        print("1. Start the AI service with: uvicorn app.main:app --reload --port 8001")
        print("2. The service will automatically load your trained models")
        print("3. Sentiment analysis will now use your supervised ML model")
        
    except Exception as e:
        print(f"\n❌ Error during training: {e}")
        import traceback
        traceback.print_exc()

if __name__ == "__main__":
    main()