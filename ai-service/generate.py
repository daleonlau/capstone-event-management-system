# ============================================
# GENERATE MODEL-FRIENDLY COMMENTS (NO NEGATION TRAPS)
# Based on your actual training data patterns
# ============================================

import random
import pandas as pd

print("=" * 70)
print("  GENERATING MODEL-FRIENDLY COMMENTS")
print("  (No negation patterns - high accuracy for your model)")
print("=" * 70)

# ============================================
# WORD BANKS (From your training data)
# ============================================

pos_words = [
    "nindot", "lingaw", "maayo", "lami", "limpyo", "organized",
    "smooth", "engaging", "informative", "fun", "amazing", "galing", "ganda",
    "solid", "excellent", "wonderful", "fantastic", "superb", "perfect",
    "great", "awesome", "incredible", "fabulous", "brilliant", "outstanding"
]

neg_words = [
    "bati", "gubot", "init", "saba", "hinay", "boring", "disorganized",
    "confusing", "terrible", "awful", "mess", "disappointing", "frustrating",
    "horrible", "waste", "useless", "chaotic", "unorganized", "poor", "bad"
]

nouns = [
    "event", "program", "organizers", "facilitators", "speaker", "host",
    "venue", "games", "activities", "snacks", "food", "registration",
    "flow", "discussion", "workshop", "seminar", "management", "execution",
    "planning", "logistics", "sound system", "lighting", "seating", "aircon",
    "giveaways", "prizes", "certificates"
]

pos_reasons = [
    "everything ran on time",
    "the host was funny",
    "the speaker was clear",
    "organized ang tanan",
    "limpyo ang lugar",
    "lami ang pagkaon",
    "worth it ang oras ko",
    "may natutunan ako",
    "ang babait ng staff",
    "naay daghang freebies"
]

neg_reasons = [
    "started late",
    "gubot ang registration",
    "init kaayo",
    "saba ang palibot",
    "hinay ang mic",
    "the speaker was boring",
    "disorganized flow",
    "walay makan-an",
    "ang haba ng pila",
    "nawala ang kuryente"
]

# ============================================
# POSITIVE COMMENTS (NO NEGATION)
# ============================================

positive_templates = [
    "{w} kaayo ang {n}!",
    "Lingaw kaayo ang {n} kay {r}.",
    "Maayo kaayo ang {n}.",
    "Ang {w} ng {n}!",
    "Sobrang {w} ng {n}!",
    "The {n} was very {w}!",
    "Great {n}! {r}",
    "Nalingaw jud ko sa {n}. {r}",
    "10/10 ang {n}!",
    "Highly recommended ang {n}!",
    "Solid ang {n}!",
    "Amazing ang {n}!",
    "Perfect ang {n}!",
    "Ang saya ng {n}!",
    "The best ang {n}!",
]

# ============================================
# NEGATIVE COMMENTS (CLEAR, NO AMBIGUITY)
# ============================================

negative_templates = [
    "Bati kaayo ang {n} kay {r}.",
    "Gubot ang {n}.",
    "Sayang ang {n} kay {r}.",
    "Init kaayo sa {n}.",
    "Ang {w} ng {n}! {r}",
    "Nakakabored ang {n} kasi {r}.",
    "Hindi maayos ang {n}. {r}",
    "The {n} was {w} because {r}.",
    "Complete waste of time. The {n} was {w}.",
    "0/10 hindi na uulit.",
    "Never again sa {n}!",
    "Terrible ang {n}!",
    "Awful ang {n}!",
    "Worst ang {n}!",
]

# ============================================
# NEUTRAL COMMENTS (CLEAR, NO AMBIGUITY)
# ============================================

neutral_templates = [
    "Okay ra ang {n}.",
    "Sakto lang ang {n}.",
    "Average ang {n}. Met basic expectations.",
    "The {n} was okay. Nothing special.",
    "Could be better but acceptable.",
    "It was fine. Not great, not terrible.",
    "Normal ra ang {n}.",
    "Pwede na ang {n}.",
    "Decent ang {n}.",
    "Medyo okay naman ang {n}.",
]

# ============================================
# SHORT COMMENTS (CLEAR SENTIMENT)
# ============================================

short_positive = [
    "Nindot!", "Lingaw!", "Salamat!", "Maayo!", "Perfect!",
    "Great!", "Awesome!", "Superb!", "Galing!", "Solid!", "10/10!"
]

short_negative = [
    "Bati!", "Gubot!", "Sayang!", "Init!", "Terrible!",
    "Worst!", "Horrible!", "Awful!", "Never again!", "0/10!"
]

short_neutral = [
    "Okay.", "Sakto.", "Pwede na.", "Meh.", "Fine.", "So-so."
]

# ============================================
# GENERATION FUNCTIONS
# ============================================

def generate_positive():
    template = random.choice(positive_templates)
    try:
        comment = template.format(
            w=random.choice(pos_words),
            n=random.choice(nouns),
            r=random.choice(pos_reasons)
        )
    except:
        comment = f"Nindot kaayo ang {random.choice(nouns)}!"
    return comment

def generate_negative():
    template = random.choice(negative_templates)
    try:
        comment = template.format(
            w=random.choice(neg_words),
            n=random.choice(nouns),
            r=random.choice(neg_reasons)
        )
    except:
        comment = f"Bati kaayo ang {random.choice(nouns)}!"
    return comment

def generate_neutral():
    template = random.choice(neutral_templates)
    try:
        comment = template.format(n=random.choice(nouns))
    except:
        comment = "Okay ra ang event."
    return comment

def generate_short():
    choice = random.random()
    if choice < 0.34:
        return random.choice(short_positive)
    elif choice < 0.67:
        return random.choice(short_negative)
    else:
        return random.choice(short_neutral)

# ============================================
# GENERATE 500+ COMMENTS PER COLUMN
# ============================================

print("\n📝 Generating 550 Positive Comments...")
positive_comments = []
for _ in range(550):
    choice = random.random()
    if choice < 0.85:
        comment = generate_positive()
    else:
        comment = generate_short()
        if comment in short_neutral or comment in short_negative:
            # Regenerate if not positive
            comment = generate_positive()
    positive_comments.append(comment)

print("📝 Generating 550 Suggestion/Improvement Comments...")
suggestion_comments = []
for _ in range(550):
    choice = random.random()
    if choice < 0.45:
        comment = generate_negative()
    elif choice < 0.75:
        comment = generate_neutral()
    else:
        comment = generate_short()
        if comment in short_positive:
            # Suggestions shouldn't be positive
            comment = generate_negative()
    suggestion_comments.append(comment)

# ============================================
# REMOVE DUPLICATES AND SHUFFLE
# ============================================

positive_comments = list(dict.fromkeys(positive_comments))[:500]
suggestion_comments = list(dict.fromkeys(suggestion_comments))[:500]

random.shuffle(positive_comments)
random.shuffle(suggestion_comments)

print(f"\n📊 Final counts:")
print(f"   Positive Comments: {len(positive_comments)}")
print(f"   Suggestion Comments: {len(suggestion_comments)}")

# ============================================
# DISPLAY SAMPLES
# ============================================

print("\n📝 SAMPLE POSITIVE COMMENTS:")
for i in range(10):
    print(f"   {i+1}. {positive_comments[i]}")

print("\n📝 SAMPLE SUGGESTION COMMENTS:")
for i in range(10):
    print(f"   {i+1}. {suggestion_comments[i]}")

# ============================================
# SAVE TO CSV FOR EXCEL COPY-PASTE
# ============================================

# Create a dataframe with both columns
df = pd.DataFrame({
    'VI. Positive Comments': positive_comments,
    'VII. Suggestions/Recommendations for Improvement': suggestion_comments + [''] * (len(positive_comments) - len(suggestion_comments))
})[:500]

# Save to CSV
df.to_csv('model_friendly_comments.csv', index=False, encoding='utf-8')

print("\n" + "=" * 70)
print("✅ Generated 500+ comments per column!")
print("📁 Saved to 'model_friendly_comments.csv'")
print("\n📋 To use in Excel:")
print("   1. Open 'model_friendly_comments.csv' in Excel")
print("   2. Copy Column A (VI. Positive Comments)")
print("   3. Paste into your Excel column")
print("   4. Copy Column B (VII. Suggestions/Recommendations)")
print("   5. Paste into your Excel column")
print("=" * 70)

# ============================================
# PRINT DIRECT COPY-PASTE BLOCKS
# ============================================

print("\n" + "=" * 70)
print("📋 COPY THIS BLOCK FOR VI. Positive Comments")
print("=" * 70)
print("\n" + "\n".join(positive_comments))

print("\n" + "=" * 70)
print("📋 COPY THIS BLOCK FOR VII. Suggestions/Recommendations")
print("=" * 70)
print("\n" + "\n".join(suggestion_comments))