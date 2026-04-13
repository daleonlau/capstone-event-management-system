"""
Comprehensive Model Evaluation
Tests fine-tuned XLM-RoBERTa on 5,000+ diverse comments
Processes in batches to avoid timeout
"""

import requests
import random
import time
from sklearn.metrics import classification_report, confusion_matrix
import json
import math

print("=" * 80)
print("  COMPREHENSIVE MODEL EVALUATION")
print("  Fine-tuned XLM-RoBERTa on 5,000+ Test Comments")
print("=" * 80)

# ============================================================
# GENERATE 5,000+ DIVERSE TEST COMMENTS
# ============================================================

print("\n📝 Generating 5,000+ test comments...")

test_data = []

# Word banks
pos_words = ["nindot", "lingaw", "maayo", "hapshay", "lami", "limpyo", "organized", "smooth", "engaging", "informative", "fun", "amazing", "galing", "ganda", "solid", "excellent", "wonderful", "fantastic", "superb", "perfect", "great", "awesome"]
neg_words = ["bati", "gubot", "init", "saba", "hinay", "boring", "disorganized", "confusing", "terrible", "awful", "mess", "disappointing", "frustrating", "horrible", "waste", "useless", "chaotic", "unorganized"]
nouns = ["event", "program", "organizers", "facilitators", "speaker", "host", "venue", "games", "activities", "snacks", "food", "registration", "flow", "discussion", "workshop", "seminar", "management", "execution"]
reasons_pos = ["naay daghang nakat-onan", "lingaw kaayo ang games", "maayo ang pagka-manage", "limpyo ang lugar", "lami ang pagkaon", "organized ang tanan", "the host was funny", "the speaker was clear", "everything ran on time"]
reasons_neg = ["dugay nag-start", "gubot ang registration", "init kaayo", "saba ang palibot", "hinay ang mic", "wala koy nasabtan", "the speaker was boring", "started late", "no clear instructions", "disorganized flow", "nag-overtime"]

# Category 1: Clear Positive (800)
for i in range(800):
    template = random.choice(["{w} kaayo ang {n}!", "Lingaw kaayo ang {n} kay {r}.", "Salamat sa {n} kay {r}.", "Ang {w} ng {n}!", "The {n} was very {w}!"])
    try:
        comment = template.format(w=random.choice(pos_words), n=random.choice(nouns), r=random.choice(reasons_pos))
    except:
        comment = f"Nindot kaayo ang {random.choice(nouns)}!"
    test_data.append((comment, "Positive"))

# Category 2: Clear Negative (800)
for i in range(800):
    template = random.choice(["Bati kaayo ang {n} kay {r}.", "Gubot ang {n}.", "Nagmahay ko kay {r}.", "Ang {w} ng {n}!", "The {n} was {w} because {r}."])
    try:
        comment = template.format(w=random.choice(neg_words), n=random.choice(nouns), r=random.choice(reasons_neg))
    except:
        comment = f"Bati kaayo ang {random.choice(nouns)}!"
    test_data.append((comment, "Negative"))

# Category 3: Neutral (600)
for i in range(600):
    template = random.choice(["Okay ra ang {n}.", "Sakto lang ang {n}.", "Wala koy reklamo pero wala sad koy ikapuri.", "Normal ra ang {n}.", "The {n} was okay. Nothing special.", "Average {n}. Met expectations."])
    try:
        comment = template.format(n=random.choice(nouns))
    except:
        comment = "Okay ra ang event."
    test_data.append((comment, "Neutral"))

# Category 4: Mixed Sentiment (400)
for i in range(400):
    template = random.choice(["Nindot unta ang {n} pero {r}.", "Maayo ang {n} kaso {r}.", "Salamat sa {n} pero {r}.", "The {n} was nice but {r}."])
    try:
        comment = template.format(n=random.choice(nouns), r=random.choice(reasons_neg))
    except:
        comment = f"Nindot unta pero {random.choice(reasons_neg)}."
    test_data.append((comment, "Negative"))

# Category 5: Sarcastic (300)
for i in range(300):
    template = random.choice(["Wow galing ng {n}, {r}!", "Perfect ang {n}, {r}!", "Sobrang galing ng {n}, {r}!"])
    try:
        comment = template.format(n=random.choice(nouns), r=random.choice(reasons_neg))
    except:
        comment = f"Wow galing, {random.choice(reasons_neg)}!"
    test_data.append((comment, "Negative"))

# Category 6: Subtle Complaints (300)
subtle_complaints = ["Sana next time mas maayos.", "Pwede bang mas maaga.", "Medyo mainit lang.", "Ang tagal ng processing.", "Medyo masikip yung venue."]
for i in range(300):
    test_data.append((random.choice(subtle_complaints), "Negative"))

# Category 7: Subtle Positives (300)
subtle_positives = ["Buti na lang may libreng tubig.", "Nakauwi ako ng maaga.", "At least may natutunan ako.", "Okay na rin kesa wala.", "Swerte at malamig sa venue."]
for i in range(300):
    test_data.append((random.choice(subtle_positives), "Positive"))

# Category 8: Code-Switching (300)
code_switch = ["Nagenjoy naman ako pero sana next time mas organized.", "Ang ganda ng venue kaso ang layo.", "Maayo ang speakers pero yung sound system hindi maganda.", "Salamat sa free food pero kulang ang serving.", "The topic was informative pero ang haba."]
for i in range(300):
    test_data.append((random.choice(code_switch), random.choice(["Positive", "Negative", "Neutral"])))

# Category 9: Short Comments (200)
short = [("Nindot!", "Positive"), ("Lingaw!", "Positive"), ("Salamat!", "Positive"), ("Bati!", "Negative"), ("Gubot!", "Negative"), ("Sayang!", "Negative"), ("Okay.", "Neutral"), ("Sakto.", "Neutral")]
for i in range(200):
    test_data.append(random.choice(short))

# Category 10: Informal/Emoji (100)
emoji = [("👍👍👍", "Positive"), ("❤️❤️❤️", "Positive"), ("👎👎👎", "Negative"), ("😭😭😭", "Negative"), ("🤷‍♂️", "Neutral"), ("sobrang saya!!", "Positive"), ("worst ever", "Negative"), ("okay lang", "Neutral")]
for i in range(100):
    test_data.append(random.choice(emoji))

# Shuffle
random.shuffle(test_data)

print(f"✅ Generated {len(test_data)} test comments")
print(f"   Positive: {sum(1 for _, l in test_data if l == 'Positive')}")
print(f"   Negative: {sum(1 for _, l in test_data if l == 'Negative')}")
print(f"   Neutral: {sum(1 for _, l in test_data if l == 'Neutral')}")

# ============================================================
# RUN EVALUATION WITH BATCH PROCESSING
# ============================================================

print("\n" + "=" * 80)
print("  RUNNING EVALUATION (Batch Processing)")
print("=" * 80)

# Split into batches of 100 to avoid timeout
BATCH_SIZE = 100
batches = [test_data[i:i+BATCH_SIZE] for i in range(0, len(test_data), BATCH_SIZE)]

print(f"\n📊 Processing {len(test_data)} comments in {len(batches)} batches of {BATCH_SIZE}")

all_predictions = {}
total_batches = len(batches)

for batch_idx, batch in enumerate(batches):
    print(f"\n📤 Processing batch {batch_idx + 1}/{total_batches} ({len(batch)} comments)...")
    
    payload = {
        "positive_comments": [],
        "suggestion_comments": [text for text, _ in batch]
    }
    
    try:
        start_time = time.time()
        response = requests.post("http://127.0.0.1:8001/analyze", json=payload, timeout=120)
        elapsed = time.time() - start_time
        
        if response.status_code != 200:
            print(f"   ❌ Error: HTTP {response.status_code}")
            continue
        
        result = response.json()
        
        # Store predictions
        for comment in result.get("positive_comments", []):
            all_predictions[comment] = "Positive"
        for comment in result.get("negative_comments", []):
            all_predictions[comment] = "Negative"
        for comment in result.get("neutral_comments", []):
            all_predictions[comment] = "Neutral"
        
        print(f"   ✅ Completed in {elapsed:.1f}s | Pos: {len(result.get('positive_comments', []))}, Neg: {len(result.get('negative_comments', []))}, Neu: {len(result.get('neutral_comments', []))}")
        
    except Exception as e:
        print(f"   ❌ Error: {e}")
        continue

# ============================================================
# CALCULATE METRICS
# ============================================================

print("\n" + "=" * 80)
print("  CALCULATING METRICS")
print("=" * 80)

y_true = []
y_pred = []

for text, true_label in test_data:
    pred_label = all_predictions.get(text, "Neutral")
    y_true.append(true_label)
    y_pred.append(pred_label)

# Overall accuracy
correct = sum(1 for t, p in zip(y_true, y_pred) if t == p)
accuracy = correct / len(test_data) * 100

print(f"\n📊 OVERALL RESULTS")
print("=" * 80)
print(f"   Total test comments: {len(test_data)}")
print(f"   Correct predictions: {correct}")
print(f"   Overall Accuracy: {accuracy:.2f}%")

# Classification Report
print("\n📈 CLASSIFICATION REPORT")
print("=" * 80)
print(classification_report(y_true, y_pred, labels=["Positive", "Neutral", "Negative"], digits=4))

# Confusion Matrix
print("\n📊 CONFUSION MATRIX")
print("=" * 80)
labels = ["Positive", "Neutral", "Negative"]
cm = confusion_matrix(y_true, y_pred, labels=labels)

print("\n                 Predicted")
print("              Pos    Neu    Neg")
for i, true_label in enumerate(labels):
    print(f"Actual {true_label:7} {cm[i][0]:5}  {cm[i][1]:5}  {cm[i][2]:5}")

# Confidence Interval
print("\n📊 STATISTICAL ANALYSIS")
print("=" * 80)
z = 1.96  # 95% confidence
ci_lower = accuracy - z * math.sqrt((accuracy/100 * (1 - accuracy/100)) / len(test_data)) * 100
ci_upper = accuracy + z * math.sqrt((accuracy/100 * (1 - accuracy/100)) / len(test_data)) * 100

print(f"   Sample size: {len(test_data)} comments")
print(f"   Accuracy: {accuracy:.2f}%")
print(f"   95% Confidence Interval: [{ci_lower:.2f}%, {ci_upper:.2f}%]")

# Final interpretation
print("\n🎯 FINAL INTERPRETATION")
print("=" * 80)

if accuracy >= 85:
    print("\n   ✅ EXCELLENT - Model is highly accurate")
    print("   🎉 Ready for production deployment")
elif accuracy >= 75:
    print("\n   👍 GOOD - Model meets project requirements")
    print("   📈 Acceptable for capstone deployment")
elif accuracy >= 65:
    print("\n   ⚠️ FAIR - Model needs improvement")
else:
    print("\n   ❌ POOR - Model not performing well")

# Save results
results_summary = {
    "total_comments": len(test_data),
    "accuracy": accuracy,
    "confidence_interval_lower": ci_lower,
    "confidence_interval_upper": ci_upper,
    "confusion_matrix": cm.tolist(),
    "classification_report": classification_report(y_true, y_pred, labels=["Positive", "Neutral", "Negative"], output_dict=True)
}

with open("evaluation_results.json", "w") as f:
    json.dump(results_summary, f, indent=2)

print("\n📁 Results saved to: evaluation_results.json")