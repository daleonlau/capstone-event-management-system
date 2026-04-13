"""
500 Challenging Comments Test - For Fine-tuned Model
Tests sarcasm, mixed sentiment, code-switching, subtle expressions
"""

import requests
import random
import time

print("=" * 80)
print("  500 CHALLENGING COMMENTS TEST")
print("  Fine-tuned XLM-RoBERTa Model (15,000 training data)")
print("=" * 80)

url = "http://127.0.0.1:8001/analyze"

# ============================================================
# 500 CHALLENGING TEST COMMENTS
# ============================================================

test_comments = []

# 1. MIXED SENTIMENT (80 comments)
mixed_sentiment = [
    "Nindot unta ang venue pero bati kay dugay nag-start",
    "Maayo ang pagkain kaso walay lami",
    "Lingaw ang games pero saba kaayo ang venue",
    "Salamat sa effort pero sayang lang ang oras",
    "Ang galing ng host pero ang haba ng program kaya nakatulog ako",
    "The speaker was good but I could barely hear him",
    "Maganda ang venue pero ang init sa loob",
    "Maayos naman ang lahat pero may kulang pa rin",
    "The food was delicious but the serving was too small",
    "Okay naman ang event pero sana next time mas organized",
    "Nindot ang topic pero boring ang speaker",
    "Maganda ang giveaways pero hindi organized ang event",
    "The venue was nice but the sound system was terrible",
    "Maayos ang registration pero magulo ang flow",
    "Ang saya ng games pero sobrang init sa venue",
    "Maganda ang program pero ang haba",
    "Maayo ang speakers pero walang time para sa Q&A",
    "Salamat sa free food pero pangit ang lasa",
    "The games were fun but the prizes were disappointing",
    "Ang galing ng host pero paulit-ulit ang jokes",
]
for _ in range(4):  # 20 x 4 = 80
    for c in mixed_sentiment[:20]:
        test_comments.append((c, "Negative"))

# 2. SARCASTIC (100 comments)
sarcastic = [
    "Wow galing ng time management, natapos ng 9pm ang 5pm event",
    "Perfect ang sound system, walang narinig ang nasa likod",
    "Ang efficient ng registration, pumila kami ng 2 oras",
    "Great job sa organizers, sobrang late ng start",
    "Sobrang organized ng event, naligaw lahat ng participants",
    "Best event ever! Kung may naenjoy lang ako kahit konti",
    "Ang linaw ng instructions kaya lahat naguluhan",
    "Super comfortable ng chairs, kung hindi lang masakit sa likod",
    "Ang sarap ng food, kung may natira lang sa amin",
    "Wonderful aircon, kung gumagana lang talaga",
    "Ang galing ng speaker, kung nakinig lang ako",
    "Great venue, kung hindi lang umuulan sa loob",
    "Excellent food, kung hindi lang ako nagka-food poisoning",
    "Perfect timing, kung hindi lang kami naghintay ng 3 oras",
    "Amazing wifi, kung gumagana lang",
    "World-class service, kung may service lang",
    "Very professional staff, kung may staff lang",
    "Top-notch facilities, kung may facilities lang",
    "State-of-the-art equipment, kung gumagana lang",
    "Sobrang saya ng event, kung hindi lang ako nakatulog",
]
for _ in range(5):  # 20 x 5 = 100
    for c in sarcastic:
        test_comments.append((c, "Negative"))

# 3. SUBTLE COMPLAINTS (75 comments)
subtle_complaints = [
    "Sana next time mas maaga ang start",
    "Pwede bang mas mabilis ang processing",
    "Medyo mainit lang sa venue",
    "Ang tagal ng pila",
    "Medyo masikip yung venue",
    "Sana may mas maraming pagkain",
    "Pwede bang mas malinaw ang instructions",
    "Medyo maingay ang background music",
    "Ang haba ng program",
    "Sana may water station",
    "Medyo madilim ang ilaw",
    "Ang bagal ng wifi",
    "Sana may mas mahabang break",
    "Medyo magulo ang flow",
    "Sana mas organized ang registration",
]
for _ in range(5):  # 15 x 5 = 75
    for c in subtle_complaints:
        test_comments.append((c, "Negative"))

# 4. SUBTLE POSITIVES (60 comments)
subtle_positives = [
    "Buti na lang may libreng tubig",
    "Nakauwi ako ng maaga",
    "At least may natutunan ako",
    "Okay na rin kesa wala",
    "Swerte at malamig sa venue",
    "Maganda yung mga binigay na examples",
    "Nakapagtanong ako sa speaker",
    "Maaga natapos ang event",
    "May nakausap akong bago",
    "Nakatulong yung topic",
    "Buti may nag-assist sa amin",
    "At least hindi masyadong mainit",
    "Buti na lang may backup plan",
    "At least may freebies",
    "Buti may CR sa malapit",
]
for _ in range(4):  # 15 x 4 = 60
    for c in subtle_positives:
        test_comments.append((c, "Positive"))

# 5. CODE-SWITCHING (80 comments)
code_switching = [
    "Nagenjoy naman ako pero sana next time mas organized ang flow ng program",
    "Ang ganda ng venue kaso ang layo masyado sa aming school",
    "Maayo ang speakers pero yung sound system hindi maganda ang quality",
    "Salamat sa free food pero kulang ang serving para sa lahat ng tao",
    "The topic was very informative pero ang haba ng discussion nakatulog ako",
    "Lingaw ang games but the prizes were mejo disappointing",
    "Ang babait ng staff pero yung registration sobrang bagal",
    "Okay naman ang event pero I expected more from the organizers",
    "Maayos ang venue pero the ventilation was very poor",
    "The host was funny kaso ang daming technical difficulties",
    "Nag-enjoy naman ako kaso mainit",
    "The speaker was great pero mahina ang mic",
    "Maayos ang food pero konti ang serving",
    "Ang ganda ng giveaways pero walang laman",
    "The program was well-planned pero nag-overtime",
]
for _ in range(6):  # 15 x 6 = 90 (take 80)
    for c in code_switching[:14]:
        test_comments.append((c, random.choice(["Positive", "Negative", "Neutral"])))

# 6. AMBIGUOUS/NEUTRAL (50 comments)
ambiguous = [
    "Wala naman akong masabi, normal lang",
    "Same lang sa mga nakaraang event",
    "Pwede na, hindi na rin ako magreklamo",
    "Sakto lang, hindi pangit hindi maganda",
    "Okay na rin para sa libre",
    "Standard na program, walang bago",
    "Hindi ako satisfied pero hindi rin disappointed",
    "Medyo okay, may room for improvement",
    "Wala namang masyadong issue pero wala ring wow factor",
    "Parehas lang sa mga na-attend-an ko dati",
    "It is what it is",
    "Not bad, not great either",
    "Could be better",
    "So-so",
    "Meh",
]
for _ in range(4):  # 15 x 4 = 60 (take 50)
    for c in ambiguous[:13]:
        test_comments.append((c, "Neutral"))

# 7. VERY SHORT (50 comments)
short = [
    ("Ok lang", "Neutral"), ("Pwede na", "Neutral"), ("Sakto lang", "Neutral"),
    ("Wala lang", "Neutral"), ("Meh", "Neutral"), ("Fine", "Neutral"),
    ("Not bad", "Neutral"), ("So-so", "Neutral"), ("Could be better", "Neutral"),
    ("It is what it is", "Neutral"), ("Nindot!", "Positive"), ("Lingaw!", "Positive"),
    ("Salamat!", "Positive"), ("Maayo!", "Positive"), ("Perfect!", "Positive"),
    ("Great!", "Positive"), ("Bati!", "Negative"), ("Gubot!", "Negative"),
    ("Sayang!", "Negative"), ("Init!", "Negative"),
]
for _ in range(3):  # 20 x 3 = 60 (take 50)
    for c, l in short[:17]:
        test_comments.append((c, l))

# 8. TYPOS / INFORMAL (50 comments)
typos = [
    "nidot kau event! lingaw kau ko!",
    "bati kau! gubot kau ang registration!",
    "slamat sa mga orgazers! hapsay kau!",
    "ang init! walang aircon! sobrang init!",
    "sayang oras! nagmahay ko!",
    "the speaker was so galing! pero haba!",
    "gnda ng venue! kaso ang layo!",
    "worst event ever! d nako uulit!",
    "galing ng sound system? wala kaming narinig!",
    "okay naman pero sana next time mas maganda",
    "ang saya saya! thank you po!",
    "grabe ang galing! sulit na sulit!",
    "the best event ever! walang tatalo!",
    "sobrang worth it! babalik ako!",
    "ang galing ng organizers! solid!",
]
for _ in range(4):  # 15 x 4 = 60 (take 50)
    for c in typos:
        test_comments.append((c, random.choice(["Positive", "Negative", "Neutral"])))

# Shuffle
random.shuffle(test_comments)

print(f"\n📊 Test Dataset: {len(test_comments)} comments")
print(f"   Positive: {sum(1 for _, l in test_comments if l == 'Positive')}")
print(f"   Negative: {sum(1 for _, l in test_comments if l == 'Negative')}")
print(f"   Neutral: {sum(1 for _, l in test_comments if l == 'Neutral')}")

# ============================================================
# RUN TEST
# ============================================================

payload = {
    "positive_comments": [],
    "suggestion_comments": [text for text, _ in test_comments]
}

print("\n" + "=" * 80)
print("  RUNNING 500 COMMENT TEST")
print("=" * 80)

try:
    print("\n📤 Sending comments to fine-tuned model...")
    start_time = time.time()
    response = requests.post(url, json=payload, timeout=600)
    elapsed = time.time() - start_time
    print(f"   Completed in {elapsed:.1f} seconds")
    
    result = response.json()
    
    # Build prediction map
    pred_map = {}
    for comment in result.get("positive_comments", []):
        pred_map[comment] = "Positive"
    for comment in result.get("negative_comments", []):
        pred_map[comment] = "Negative"
    for comment in result.get("neutral_comments", []):
        pred_map[comment] = "Neutral"
    
    # Calculate accuracy
    y_true = []
    y_pred = []
    for text, true_label in test_comments:
        pred_label = pred_map.get(text, "Unknown")
        y_true.append(true_label)
        y_pred.append(pred_label)
    
    correct = sum(1 for t, p in zip(y_true, y_pred) if t == p)
    accuracy = correct / len(test_comments) * 100
    
    print("\n" + "=" * 80)
    print("📊 OVERALL RESULTS")
    print("=" * 80)
    print(f"   Total comments: {len(test_comments)}")
    print(f"   Correct predictions: {correct}")
    print(f"   Overall Accuracy: {accuracy:.2f}%")
    
    # Per-category breakdown
    print("\n" + "=" * 80)
    print("📊 PER-CATEGORY BREAKDOWN")
    print("=" * 80)
    
    categories = {
        "Mixed Sentiment": mixed_sentiment[:15],
        "Sarcastic": sarcastic[:15],
        "Subtle Complaints": subtle_complaints[:10],
        "Subtle Positives": subtle_positives[:10],
        "Code-Switching": code_switching[:10],
        "Ambiguous/Neutral": ambiguous[:10],
        "Short Comments": [c for c, _ in short[:8]],
        "Typos/Informal": typos[:8],
    }
    
    for cat_name, cat_comments in categories.items():
        cat_correct = 0
        cat_total = 0
        for c in cat_comments:
            pred = pred_map.get(c, "Unknown")
            for text, label in test_comments:
                if text == c:
                    if pred == label:
                        cat_correct += 1
                    cat_total += 1
                    break
        if cat_total > 0:
            cat_acc = cat_correct / cat_total * 100
            print(f"   {cat_name:25} : {cat_correct}/{cat_total} ({cat_acc:.1f}%)")
    
    # Show misclassifications
    print("\n" + "=" * 80)
    print("❌ MISCLASSIFIED EXAMPLES")
    print("=" * 80)
    
    misclassified = []
    for text, true_label in test_comments:
        pred_label = pred_map.get(text, "Unknown")
        if pred_label != true_label:
            misclassified.append((text, true_label, pred_label))
    
    print(f"\nTotal misclassifications: {len(misclassified)} / {len(test_comments)} ({len(misclassified)/len(test_comments)*100:.1f}%)")
    print("\nSample misclassifications (first 15):")
    for text, true_label, pred_label in misclassified[:15]:
        print(f"   [{true_label} → {pred_label}] {text[:70]}...")
    
    print("\n" + "=" * 80)
    print("🎯 FINAL INTERPRETATION")
    print("=" * 80)
    
    if accuracy >= 88:
        print("\n   ✅ EXCELLENT - Model is highly accurate on challenging comments!")
        print("   🎉 Ready for production deployment")
    elif accuracy >= 82:
        print("\n   👍 GOOD - Model handles challenging comments well")
        print("   📈 Acceptable for capstone deployment")
    elif accuracy >= 75:
        print("\n   ⚠️ FAIR - Model needs more training on challenging cases")
    else:
        print("\n   ❌ POOR - Model struggling with challenging comments")
    
    print(f"\n📁 Model info:")
    print(f"   Type: {result.get('method_used', 'Unknown')}")
    print(f"   Fine-tuned: {result.get('fine_tuned', False)}")
    print(f"   Training data: 15,000 balanced comments")
    
except Exception as e:
    print(f"\n❌ Error: {e}")
    print("Make sure the service is running: python main.py")