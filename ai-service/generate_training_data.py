# ============================================
# CELL 2: Generate NOISY Training Data
# FOCUS: Fixing Code-Switching (40% → 85%+)
# ============================================

import csv
import random
import re

print("=" * 70)
print("  GENERATING TRAINING DATA")
print("  FOCUS: FIXING CODE-SWITCHING (40% to 85%+)")
print("=" * 70)

data_set = set()

# ============================================================
# WORD BANKS
# ============================================================

pos_words = [
    "nindot", "lingaw", "maayo", "hapshay", "lami", "limpyo", "organized",
    "smooth", "engaging", "informative", "fun", "amazing", "galing", "ganda",
    "solid", "excellent", "wonderful", "fantastic", "superb", "perfect",
    "great", "awesome", "incredible", "fabulous", "brilliant", "outstanding",
    "top-notch", "world-class", "exceptional", "remarkable"
]

neg_words = [
    "bati", "gubot", "init", "saba", "hinay", "boring", "disorganized",
    "confusing", "terrible", "awful", "mess", "disappointing", "frustrating",
    "horrible", "waste", "useless", "chaotic", "unorganized", "poor", "bad",
    "sayang", "nagmahay", "kulang", "pangit", "nakakainis", "nakakabored"
]

neu_words = [
    "okay", "sakto", "normal", "average", "decent", "acceptable", "standard",
    "fair", "moderate", "ordinary", "typical", "common", "fine", "mediocre"
]

nouns = [
    "event", "program", "organizers", "facilitators", "speaker", "host",
    "venue", "games", "activities", "snacks", "food", "registration",
    "flow", "discussion", "workshop", "seminar", "management", "execution",
    "planning", "logistics", "sound system", "lighting", "seating", "aircon",
    "giveaways", "prizes", "certificates", "breakout sessions"
]

pos_reasons = [
    "naay daghang nakat-onan", "lingaw kaayo ang games", "maayo ang pagka-manage",
    "limpyo ang lugar", "lami ang pagkaon", "organized ang tanan",
    "the host was funny", "the speaker was clear", "everything ran on time",
    "naay daghang freebies", "malamig ang aircon", "ang babait ng staff",
    "may natutunan ako", "nakapag-network ako", "worth it ang oras ko"
]

neg_reasons = [
    "dugay nag-start", "gubot ang registration", "init kaayo", "saba ang palibot",
    "hinay ang mic", "wala koy nasabtan", "the speaker was boring",
    "started late", "no clear instructions", "disorganized flow",
    "nag-overtime", "walay makan-an", "nawala ang kuryente", "ang haba ng pila",
    "masikip ang venue", "walang tubig", "pangit ang sound system"
]

emojis = [" 👍", " 😊", " ❤️", " 🔥", " 👏", " ✨", " 💯", " 😅", " 🙌", " 🎉", " 🤔", " 😕", " 💪", " 🎯"]

def random_emoji():
    return random.choice(emojis) if random.random() < 0.3 else ""

# ============================================================
# NOISE FUNCTIONS
# ============================================================

def add_typos(text):
    """Add realistic typos"""
    typo_map = {
        'nindot': ['nidot', 'nindut', 'nndot'],
        'lingaw': ['lingao', 'lingw', 'lingaww'],
        'maayo': ['maau', 'mayo', 'maayu'],
        'lami': ['lmi', 'lamii'],
        'bati': ['bte', 'bt'],
        'gubot': ['gbut', 'goboot'],
        'saba': ['sabaa', 'sba'],
        'init': ['nit', 'inet'],
        'hinay': ['hnay', 'hinayy'],
        'smooth': ['smoth', 'smoot'],
        'great': ['gret', 'grate'],
        'perfect': ['perpekt', 'perpek'],
        'salamat': ['slmat', 'slamat'],
        'sobrang': ['subrang', 'sobrng'],
        'maganda': ['magnda', 'mganda'],
        'kasi': ['kse', 'kase'],
        'wala': ['wlang', 'wla'],
    }

    for word, typo_list in typo_map.items():
        if word in text.lower() and random.random() < 0.35:
            text = re.sub(re.escape(word), random.choice(typo_list), text, flags=re.IGNORECASE)

    if random.random() < 0.15:
        words = text.split()
        if words:
            idx = random.randint(0, len(words)-1)
            if len(words[idx]) > 3:
                words[idx] = words[idx][:-1]
            text = ' '.join(words)

    return text

def add_spacing_issues(text):
    if random.random() < 0.12:
        text = text.replace(' ', '  ')
    if random.random() < 0.08:
        text = text.replace(' ', '')
    return text

def add_case_variation(text):
    if random.random() < 0.25:
        words = text.split()
        for i in range(len(words)):
            if random.random() < 0.3:
                words[i] = words[i].upper()
            elif random.random() < 0.2:
                words[i] = words[i].lower()
        text = ' '.join(words)
    return text

def add_extra_punctuation(text):
    if random.random() < 0.2:
        text = text.replace('!', '!!')
        text = text.replace('?', '??')
    if random.random() < 0.15:
        text = text + '...'
    return text

def add_noise(comment):
    """Apply noise to regular comments"""
    comment = add_typos(comment)
    comment = add_spacing_issues(comment)
    comment = add_case_variation(comment)
    comment = add_extra_punctuation(comment)
    return comment

# ============================================================
# CODE-SWITCHING GENERATION (FIXED - ALL LABELS)
# ============================================================

def generate_code_switching_positive():
    """Code-switching that should be POSITIVE"""
    templates = [
        "Sobrang {pw} ng {n} kahit may {nr} man, worth it pa rin!",
        "Ang galing ng {n}! Kahit may {nr}, {pw} pa rin ang experience.",
        "{pw} naman ang {n} overall kahit hindi perfect.",
        "Naenjoy ko pa rin yung {n} kahit {nr}. Babalik ako!",
        "The {n} was {pw} despite the {nr}. Thank you!",
        "Maayo ang {n} bisan pa sa {nr}. Will recommend!",
        "Sulit na sulit ang {n} kahit may {nr}. {pw} ang mga speakers!",
        "Ang saya ng {n} kahit na {nr}. Next year ulit!",
        "Great event! Medyo {nr} lang pero sobrang {pw} pa rin.",
        "Worth it yung {n} kahit {nr}. Galing ng organizers!",
    ]
    template = random.choice(templates)
    try:
        comment = template.format(
            pw=random.choice(pos_words),
            nr=random.choice(neg_reasons),
            n=random.choice(nouns)
        )
    except:
        comment = "Sobrang galing ng event kahit may konting issues, worth it pa rin!"

    if random.random() < 0.2:
        comment = add_noise(comment)
    return comment + random_emoji(), "Positive"

def generate_code_switching_negative():
    """Code-switching that should be NEGATIVE"""
    templates = [
        "Ang ganda ng venue kaso ang layo.",
        "Maayo ang speakers pero yung sound system hindi maganda.",
        "Salamat sa free food pero kulang ang serving.",
        "The topic was informative pero ang haba.",
        "Lingaw ang games but the prizes were mejo disappointing.",
        "Ang babait ng staff pero yung registration sobrang bagal.",
        "Okay naman ang event pero I expected more from the organizers.",
        "The host was funny kaso ang daming technical difficulties.",
        "Maganda ang giveaways pero walang laman.",
        "Maayos ang food pero konti ang serving.",
        "Ang galing ng speaker, kung nakinig lang ako.",
        "Wonderful aircon, kung gumagana lang talaga.",
        "The best event ever! Kung may naenjoy lang ako kahit konti.",
        "Sobrang organized ng event, naligaw lahat ng participants.",
        "Ang efficient ng registration, pumila kami ng 2 oras.",
        "Perfect ang sound system, walang narinig ang nasa likod.",
        "Great job sa organizers, sobrang late ng start.",
        "{pw} unta ang {n} pero {nr}.",
        "Maayo ang {n} kaso {nr}.",
        "The {n} was nice but {nr}.",
    ]
    template = random.choice(templates)
    try:
        comment = template.format(
            pw=random.choice(pos_words),
            nr=random.choice(neg_reasons),
            n=random.choice(nouns)
        )
    except:
        comment = "Ang ganda ng venue kaso ang layo masyado."

    if random.random() < 0.2:
        comment = add_noise(comment)
    return comment + random_emoji(), "Negative"

def generate_code_switching_neutral():
    """Code-switching that should be NEUTRAL"""
    templates = [
        "Okay naman yung {n}, may maganda pero may pangit din.",
        "Pwede na yung {n}, hindi ganun kaganda pero hindi rin pangit.",
        "Sakto lang yung {n}. Hindi sobrang saya, hindi sobrang lungkot.",
        "Average lang yung experience. May natutunan pero may kulang.",
        "The {n} was decent. Could be better, could be worse.",
        "Wala lang. Di ko alam kung masaya ba ko o hindi.",
        "Ewan ko ba sa {n} na yan. Keri naman.",
        "Bahala na. Okay na rin yung {n}.",
        "Medyo okay naman ang {n} pero may room for improvement.",
        "The {n} met expectations. Nothing more, nothing less.",
        "Hindi siya yung best pero hindi rin yung worst.",
        "Sige na, pwede na yung {n}.",
        "Keri lang yung {n}. Di ko alam kung irerecommend ko.",
    ]
    template = random.choice(templates)
    try:
        comment = template.format(n=random.choice(nouns))
    except:
        comment = "Okay naman yung event, may maganda pero may pangit din."

    if random.random() < 0.2:
        comment = add_noise(comment)
    return comment + random_emoji(), "Neutral"

# ============================================================
# REGULAR GENERATION FUNCTIONS
# ============================================================

def generate_pos():
    templates = [
        "{w} kaayo {n}!",
        "Lingaw kaayo ang {n} kay {r}.",
        "Maayo kaayo ang {n}.",
        "Salamat sa {n} kay {r}.",
        "Ang {w} ng {n}!",
        "Sobrang {w} ng {n}!",
        "The {n} was very {w}!",
        "I really enjoyed the {n} because {r}.",
        "Great {n}! {r}",
        "Nalingaw jud ko sa {n}. {r}",
        "Balik ko puhon kay {w} ang {n}.",
    ]
    t = random.choice(templates)
    try:
        comment = t.format(w=random.choice(pos_words), n=random.choice(nouns), r=random.choice(pos_reasons))
    except:
        comment = f"Nindot kaayo ang {random.choice(nouns)}!"
    if random.random() < 0.5:
        comment = add_noise(comment)
    return comment + random_emoji(), "Positive"

def generate_neg():
    templates = [
        "Bati kaayo ang {n} kay {r}.",
        "Gubot ang {n}.",
        "Nagmahay ko kay {r}.",
        "Wala koy nakat-onan kay {r}.",
        "Init kaayo sa {n}.",
        "Ang {w} ng {n}! {r}",
        "Nakakabored ang {n} kasi {r}.",
        "Hindi maayos ang {n}. {r}",
        "The {n} was {w} because {r}.",
        "Complete waste of time. The {n} was {w}.",
        "Sayang ang oras ko sa {n}. {r}",
        "Maypag wala nalang ko niadto kay {r}.",
    ]
    t = random.choice(templates)
    try:
        comment = t.format(w=random.choice(neg_words), n=random.choice(nouns), r=random.choice(neg_reasons))
    except:
        comment = f"Bati kaayo ang {random.choice(nouns)}!"
    if random.random() < 0.5:
        comment = add_noise(comment)
    return comment + random_emoji(), "Negative"

def generate_neu():
    templates = [
        "Okay ra ang {n}.",
        "Sakto lang ang {n}.",
        "Wala koy reklamo pero wala sad koy ikapuri.",
        "Normal ra ang {n}.",
        "Ang {n} kay {w} pero okay ra.",
        "Medyo {w} ang {n} pero okay lang.",
        "The {n} was okay. Nothing special.",
        "Average {n}. Met basic expectations.",
        "Could be better but acceptable.",
        "It was fine. Not great, not terrible.",
    ]
    t = random.choice(templates)
    try:
        comment = t.format(n=random.choice(nouns), w=random.choice(neu_words))
    except:
        comment = f"Okay ra ang {random.choice(nouns)}."
    if random.random() < 0.4:
        comment = add_noise(comment)
    return comment + random_emoji(), "Neutral"

def generate_mixed():
    """Mixed sentiment = NEGATIVE"""
    templates = [
        "{pw} unta ang {n} pero {nr}.",
        "Maayo ang {n} kaso {nr}.",
        "Salamat sa {n} pero {nr}.",
        "The {n} was nice but {nr}.",
    ]
    t = random.choice(templates)
    try:
        comment = t.format(pw=random.choice(pos_words), nr=random.choice(neg_reasons), n=random.choice(nouns))
    except:
        comment = f"Nindot unta pero {random.choice(neg_reasons)}."
    if random.random() < 0.4:
        comment = add_noise(comment)
    return comment + random_emoji(), "Negative"

def generate_sarcastic():
    """Sarcastic = NEGATIVE - NO NOISE"""
    templates = [
        "Wow galing ng {n}, {nr}!",
        "Perfect ang {n}, {nr}!",
        "Sobrang galing ng {n}, {nr}!",
        "Great job sa {n}, {nr}!",
        "Ang galing ng {n}, {nr}!",
        "The best ang {n}, {nr}!",
    ]
    t = random.choice(templates)
    try:
        comment = t.format(n=random.choice(nouns), nr=random.choice(neg_reasons))
    except:
        comment = f"Wow galing, {random.choice(neg_reasons)}!"
    return comment + random_emoji(), "Negative"

def generate_subtle_positive():
    phrases = [
        "Buti na lang may libreng tubig.",
        "Nakauwi ako ng maaga.",
        "At least may natutunan ako.",
        "Okay na rin kesa wala.",
        "Swerte at malamig sa venue.",
        "Maganda yung mga binigay na examples.",
        "Nakapagtanong ako sa speaker.",
        "Maaga natapos ang event.",
        "May nakausap akong bago.",
        "Nakatulong yung topic.",
    ]
    comment = random.choice(phrases)
    if random.random() < 0.3:
        comment = add_noise(comment)
    return comment + random_emoji(), "Positive"

def generate_subtle_negative():
    phrases = [
        "Sana next time mas maaga.",
        "Pwede bang mas mabilis.",
        "Medyo mainit lang.",
        "Ang tagal ng pila.",
        "Medyo masikip yung venue.",
        "Sana may mas maraming pagkain.",
        "Pwede bang mas malinaw ang instructions.",
        "Medyo maingay ang background music.",
        "Ang haba ng programa.",
        "Sana may water station.",
    ]
    comment = random.choice(phrases)
    if random.random() < 0.3:
        comment = add_noise(comment)
    return comment + random_emoji(), "Negative"

def generate_short():
    pos_shorts = ["Nindot!", "Lingaw!", "Salamat!", "Maayo!", "Perfect!", "Great!", "Awesome!", "Superb!"]
    neg_shorts = ["Bati!", "Gubot!", "Sayang!", "Init!", "Terrible!", "Worst!", "Horrible!", "Awful!"]
    neu_shorts = ["Okay.", "Sakto.", "Pwede na.", "Wala lang.", "Meh.", "Fine.", "So-so."]

    choice = random.random()
    if choice < 0.34:
        comment = random.choice(pos_shorts)
        label = "Positive"
    elif choice < 0.67:
        comment = random.choice(neg_shorts)
        label = "Negative"
    else:
        comment = random.choice(neu_shorts)
        label = "Neutral"

    if random.random() < 0.2:
        comment = add_noise(comment)
    return comment + random_emoji(), label

# ============================================================
# GENERATE DATA
# ============================================================

print("\n📝 Generating 15,000 comments...")
print("   ✓ Code-switching: NOW with ALL labels (Pos/Neu/Neg)")
print("   ✓ Regular comments: WITH noise")
print("   ✓ Sarcastic: CLEAN (no noise)")
print("   ✓ Mixed sentiment: Negative\n")

data_list = []
target_total = 15000

print("   Generating Positive comments...")
pos_target = 5000
pos_generated = 0
while pos_generated < pos_target:
    choice = random.random()
    if choice < 0.50:  # Regular positive
        c, label = generate_pos()
    elif choice < 0.65:  # Subtle positive
        c, label = generate_subtle_positive()
    elif choice < 0.80:  # Short positive
        c, label = generate_short()
        if label != "Positive":
            continue
    else:  # POSITIVE CODE-SWITCHING (NEW!)
        c, label = generate_code_switching_positive()

    if c not in data_set:
        data_set.add(c)
        data_list.append((c, label))
        pos_generated += 1
    if pos_generated % 500 == 0:
        print(f"      Positive: {pos_generated}/{pos_target}")

print("   Generating Negative comments...")
neg_target = 5000
neg_generated = 0
while neg_generated < neg_target:
    choice = random.random()
    if choice < 0.30:  # Regular negative
        c, label = generate_neg()
    elif choice < 0.45:  # Mixed sentiment
        c, label = generate_mixed()
    elif choice < 0.60:  # Sarcastic
        c, label = generate_sarcastic()
    elif choice < 0.75:  # Subtle negative
        c, label = generate_subtle_negative()
    elif choice < 0.85:  # Short negative
        c, label = generate_short()
        if label != "Negative":
            continue
    else:  # NEGATIVE CODE-SWITCHING
        c, label = generate_code_switching_negative()

    if c not in data_set:
        data_set.add(c)
        data_list.append((c, label))
        neg_generated += 1
    if neg_generated % 500 == 0:
        print(f"      Negative: {neg_generated}/{neg_target}")

print("   Generating Neutral comments...")
neu_target = 5000
neu_generated = 0
while neu_generated < neu_target:
    choice = random.random()
    if choice < 0.50:  # Regular neutral
        c, label = generate_neu()
    elif choice < 0.70:  # Short neutral
        c, label = generate_short()
        if label != "Neutral":
            continue
    else:  # NEUTRAL CODE-SWITCHING (NEW!)
        c, label = generate_code_switching_neutral()

    if c not in data_set:
        data_set.add(c)
        data_list.append((c, label))
        neu_generated += 1
    if neu_generated % 500 == 0:
        print(f"      Neutral: {neu_generated}/{neu_target}")

random.shuffle(data_list)

# ============================================================
# STATISTICS
# ============================================================

pos_count = sum(1 for _, label in data_list if label == "Positive")
neg_count = sum(1 for _, label in data_list if label == "Negative")
neu_count = sum(1 for _, label in data_list if label == "Neutral")

print(f"\n📊 FINAL STATISTICS:")
print(f"   Total: {len(data_list)}")
print(f"   Positive: {pos_count} ({pos_count/len(data_list)*100:.1f}%)")
print(f"   Negative: {neg_count} ({neg_count/len(data_list)*100:.1f}%)")
print(f"   Neutral: {neu_count} ({neu_count/len(data_list)*100:.1f}%)")

print("\n📝 SAMPLE CODE-SWITCHING - POSITIVE (NEW!):")
pos_cs = []
for c, l in data_list:
    if l == "Positive" and any(word in c.lower() for word in ["kahit", "pero", "kaso"]):
        if len(pos_cs) < 3:
            pos_cs.append(c)
for c in pos_cs:
    print(f"   [POSITIVE] {c[:80]}")

print("\n📝 SAMPLE CODE-SWITCHING - NEGATIVE:")
neg_cs = []
for c, l in data_list:
    if l == "Negative" and any(word in c.lower() for word in ["kaso", "pero", "but"]):
        if len(neg_cs) < 3:
            neg_cs.append(c)
for c in neg_cs:
    print(f"   [NEGATIVE] {c[:80]}")

print("\n📝 SAMPLE CODE-SWITCHING - NEUTRAL (NEW!):")
neu_cs = []
for c, l in data_list:
    if l == "Neutral" and any(word in c.lower() for word in ["pero", "kaso", "eh"]):
        if len(neu_cs) < 3:
            neu_cs.append(c)
for c in neu_cs:
    print(f"   [NEUTRAL] {c[:80]}")

# ============================================================
# SAVE
# ============================================================

csv_file = "event_data_.csv"
with open(csv_file, "w", newline="", encoding="utf-8") as f:
    writer = csv.writer(f)
    writer.writerow(["vcomment", "Label"])
    for comment, label in data_list:
        writer.writerow([comment, label])

print(f"\n✅ Saved to {csv_file}")
print("\n📌 KEY FIX FOR CODE-SWITCHING:")
print("   ✓ Now has 3 separate functions for Positive, Negative, AND Neutral")
print("   ✓ Code-switching appears in ALL sentiment categories")
print("   ✓ 20% of Positive comments are now code-switched")
print("   ✓ 15% of Negative comments are now code-switched")
print("   ✓ 30% of Neutral comments are now code-switched")
print("\n📌 NEXT: Update CELL 3 to use this file:")
print("   Change DATA_FILE = 'event_data_latest.csv'")
print("=" * 70)