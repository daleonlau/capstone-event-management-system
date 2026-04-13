import csv
import random
import re

print("=" * 70)
print("  GENERATING NOISY, REALISTIC TRAINING DATA")
print("  COMPLETE FIXED VERSION - ALL WEAKNESSES ADDRESSED")
print("=" * 70)

data_set = set()

# ============================================================
# WORD BANKS (EXPANDED)
# ============================================================

pos_words = [
    "nindot", "lingaw", "maayo", "hapshay", "lami", "limpyo", "organized",
    "smooth", "engaging", "informative", "fun", "amazing", "galing", "ganda",
    "solid", "excellent", "wonderful", "fantastic", "superb", "perfect",
    "great", "awesome", "incredible", "fabulous", "brilliant", "outstanding",
    "top-notch", "world-class", "exceptional", "remarkable", "perfect"
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

# Ambiguous/Neutral phrases (for 20% accuracy fix)
ambiguous_phrases = [
    "Pwede na.",
    "It is what it is.",
    "Ganoon talaga.",
    "Wala namang magagawa.",
    "Sana all na lang.",
    "Bahala na.",
    "Okay na rin kesa wala.",
    "At least may natutunan kahit papaano.",
    "Hindi ko alam kung ano mararamdaman ko.",
    "Sakto lang, hindi masaya hindi malungkot.",
    "Ewan ko ba.",
    "Bahala na si Batman.",
    "Que sera sera.",
    "Whatever floats your boat.",
    "Kanya-kanyang trip lang.",
    "Ganon daw talaga.",
    "Expected ko na rin.",
    "Wala naman bago.",
    "Parang ganun pa rin.",
    "Same old, same old.",
    "Medyo meh.",
    "E di wow.",
    "Ikaw na.",
    "Sige na nga.",
    "Oo na, ikaw na magaling.",
]

# Negation patterns (FIX for "Wala koy reklamo" issues)
negation_patterns = [
    "Wala koy ika-suggest nga usbon {adj} na.",
    "Wala koy reklamo, {adj} ang tanan.",
    "Walay problema, {adj} kaayo.",
    "Wala koy ma negative nga masulti, {adj} ang event.",
    "Hindi ako maka-reklamo kasi {adj} naman.",
    "Wala namang masamang masabi, {adj} ang lahat.",
    "Walang problema sa {n}, {adj} na siya.",
    "Wala koy ma comment nga negative kay {adj} na.",
    "Walay mali, {adj} ang execution.",
    "Wala koy ika reklamo, {adj} ang program.",
]

positive_adj = ["perfect", "nindot", "maayo", "smooth", "organized", "lingaw", "lami", "excellent", "great", "wonderful"]

# Double negative patterns
double_negative_patterns = [
    "Hindi naman pangit ang {n}.",
    "Wala namang masama sa {n}.",
    "Hindi ako disappointed sa {n}.",
    "Walang boring sa event.",
    "Hindi masama ang experience.",
    "Hindi pangit ang pagkaka-organize.",
    "Wala namang problema sa venue.",
    "Hindi nakakainis ang speaker.",
    "Walang reklamo sa pagkain.",
]

emojis = [" 👍", " 😊", " ❤️", " 🔥", " 👏", " ✨", " 💯", " 😅", " 🙌", " 🎉", " 🤔", " 😕", " 💪", " 🎯", " 😒", " 😐", " 🤷"]

def random_emoji():
    return random.choice(emojis) if random.random() < 0.3 else ""

# ============================================================
# IMPROVED NOISE FUNCTIONS
# ============================================================

def add_realistic_typos(text):
    """Enhanced typo generation for 25% accuracy fix"""
    
    # Common Filipino typing errors
    typo_map = {
        'nindot': ['nidot', 'nindut', 'nndot', 'nidot', 'nindotz'],
        'lingaw': ['lingao', 'lingw', 'lingaww', 'lingao', 'lingaw'],
        'maayo': ['maau', 'mayo', 'maayo', 'maau', 'maayu', 'mau'],
        'lami': ['lmi', 'lami', 'lamii', 'lami', 'lame'],
        'bati': ['bati', 'bte', 'bati', 'bati', 'bt'],
        'gubot': ['gubot', 'gbut', 'gubot', 'gubot', 'goboot'],
        'saba': ['sabaa', 'sba', 'saba', 'saba', 'sab'],
        'init': ['init', 'init', 'nit', 'init', 'inet'],
        'hinay': ['hinay', 'hnay', 'hinay', 'hinay', 'hinayy'],
        'smooth': ['smoth', 'smoothh', 'smooth', 'smoot'],
        'great': ['gret', 'greatt', 'great', 'grate'],
        'perfect': ['perpekt', 'perfect', 'perfect', 'perpek'],
        'salamat': ['slmat', 'salamat', 'salamt', 'salamat', 'slamat'],
        'sobrang': ['subrang', 'sobra', 'subra', 'sobrng'],
        'maganda': ['magnda', 'maganda', 'mganda', 'magand'],
        'kasi': ['kse', 'ksi', 'kase', 'kasi'],
        'meron': ['merun', 'meron', 'mern', 'meronh'],
        'wala': ['wlang', 'wala', 'wal', 'wla'],
        'reklamo': ['reklamo', 'reklamu', 'reklm', 'reklamo'],
        'problema': ['problema', 'prob', 'probs', 'blema'],
    }
    
    for word, typo_list in typo_map.items():
        if word in text.lower() and random.random() < 0.35:
            text = re.sub(re.escape(word), random.choice(typo_list), text, flags=re.IGNORECASE)
    
    # Missing spaces
    if random.random() < 0.1:
        text = re.sub(r'(\w+)\s+(\w+)', r'\1\2', text, count=1)
    
    # Repeated letters (excitement/frustration)
    if random.random() < 0.12:
        vowels = 'aeiou'
        words = text.split()
        if words:
            idx = random.randint(0, len(words)-1)
            for i, char in enumerate(words[idx]):
                if char.lower() in vowels and random.random() < 0.5:
                    words[idx] = words[idx][:i] + char*2 + words[idx][i+1:]
                    break
            text = ' '.join(words)
    
    # Random character deletions
    if random.random() < 0.08:
        words = text.split()
        if words:
            idx = random.randint(0, len(words)-1)
            if len(words[idx]) > 3:
                del_pos = random.randint(0, len(words[idx])-1)
                words[idx] = words[idx][:del_pos] + words[idx][del_pos+1:]
            text = ' '.join(words)
    
    return text

def add_spacing_issues(text):
    if random.random() < 0.12:
        text = text.replace(' ', '  ')
    if random.random() < 0.08:
        text = text.replace(' ', '')
    if random.random() < 0.1:
        text = re.sub(r'([.!?])', r' \1', text)
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
    if random.random() < 0.05:
        text = text.upper()
    return text

def add_extra_punctuation(text):
    if random.random() < 0.2:
        text = text.replace('!', '!!')
        text = text.replace('?', '??')
    if random.random() < 0.15:
        text = text + '...'
    if random.random() < 0.08:
        text = '❗ ' + text
    return text

def add_noise(comment):
    """Apply noise to regular comments (NOT sarcasm or code-switching)"""
    comment = add_realistic_typos(comment)
    comment = add_spacing_issues(comment)
    comment = add_case_variation(comment)
    comment = add_extra_punctuation(comment)
    return comment

# ============================================================
# GENERATION FUNCTIONS
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
        "10/10 ang {n}!",
        "Highly recommended ang {n}!",
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
        "0/10 hindi na uulit.",
        "Never again sa {n}!",
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
        "Pwede na yung {n}.",
        "Hindi pangit, hindi rin maganda.",
    ]
    t = random.choice(templates)
    try:
        comment = t.format(n=random.choice(nouns), w=random.choice(neu_words))
    except:
        comment = f"Okay ra ang {random.choice(nouns)}."
    if random.random() < 0.4:
        comment = add_noise(comment)
    return comment + random_emoji(), "Neutral"

def generate_ambiguous():
    """Generate truly ambiguous comments (fix for 20% accuracy)"""
    comment = random.choice(ambiguous_phrases)
    
    roll = random.random()
    if roll < 0.35:
        label = "Negative"
    elif roll < 0.65:
        label = "Neutral"
    else:
        label = "Positive"
    
    if random.random() < 0.25:
        comment = add_noise(comment)
    return comment + random_emoji(), label

def generate_mixed():
    """Mixed sentiment = NEGATIVE (negative outweighs positive)"""
    templates = [
        "{pw} unta ang {n} pero {nr}.",
        "Maayo ang {n} kaso {nr}.",
        "Salamat sa {n} pero {nr}.",
        "The {n} was nice but {nr}.",
        "Maganda sana kaso {nr}.",
        "Okay naman pero {nr}.",
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
    """Sarcastic = ALWAYS Negative - NO NOISE"""
    templates = [
        "Wow galing ng {n}, {nr}!",
        "Perfect ang {n}, {nr}!",
        "Sobrang galing ng {n}, {nr}!",
        "Great job sa {n}, {nr}!",
        "Ang galing ng {n}, {nr}!",
        "The best ang {n}, {nr}!",
        "Sobrang saya ng {n}, {nr}!",
        "Ang efficient ng {n}, {nr}!",
        "Ang linaw ng {n}, {nr}!",
        "Sobrang organized ng {n}, {nr}!",
        "World-class ang {n}, {nr}!",
        "Top-notch ang {n}, {nr}!",
        "Amazing ang {n}, {nr}!",
        "Incredible ang {n}, {nr}!",
        "Fantastic ang {n}, {nr}!",
        "⭐️⭐️⭐️⭐️⭐️ ang {n}, {nr}!",
        "Perfect 10/10! {nr}",
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
        "Mababait ang staff kahit papano.",
        "Masarap yung snacks kahit konti.",
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
        "Sana mas malamig ang aircon.",
        "Sana mas organized next time.",
    ]
    comment = random.choice(phrases)
    if random.random() < 0.3:
        comment = add_noise(comment)
    return comment + random_emoji(), "Negative"

def generate_short():
    pos_shorts = ["Nindot!", "Lingaw!", "Salamat!", "Maayo!", "Perfect!", "Great!", "Awesome!", "Superb!", "Galing!", "Solid!"]
    neg_shorts = ["Bati!", "Gubot!", "Sayang!", "Init!", "Terrible!", "Worst!", "Horrible!", "Awful!", "Pangit!", "Never again!"]
    neu_shorts = ["Okay.", "Sakto.", "Pwede na.", "Wala lang.", "Meh.", "Fine.", "So-so.", "Ewan.", "Bahala na."]
    
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

def generate_negation_pattern():
    """FIX: Generate comments with negation that should be POSITIVE"""
    template = random.choice(negation_patterns)
    try:
        comment = template.format(
            adj=random.choice(positive_adj),
            n=random.choice(nouns)
        )
    except:
        comment = "Wala koy reklamo, perfect ang event."
    # Light noise only
    if random.random() < 0.2:
        comment = add_noise(comment)
    return comment + random_emoji(), "Positive"

def generate_double_negative():
    """FIX: Generate double negative comments = POSITIVE"""
    template = random.choice(double_negative_patterns)
    try:
        comment = template.format(n=random.choice(nouns))
    except:
        comment = "Hindi naman pangit ang event."
    if random.random() < 0.2:
        comment = add_noise(comment)
    return comment + random_emoji(), "Positive"

def generate_code_switch_improved():
    """IMPROVED: Code-switching with ALL labels"""
    
    positive_templates = [
        "Sobrang {pw} ng {n} kahit may {nr} pa! Worth it!",
        "Ang galing ng {n}! Kahit {nr}, okay lang kasi {pw} talaga.",
        "{pw} naman ang {n} overall kahit may {nr}.",
        "Naenjoy ko pa rin yung {n} kahit {nr}. {pw} pa rin!",
    ]
    
    negative_templates = [
        "Ang ganda ng venue kaso ang layo.",
        "Maayo ang speakers pero yung sound system hindi maganda.",
        "Salamat sa free food pero kulang ang serving.",
        "The topic was informative pero ang haba.",
        "Lingaw ang games but the prizes were mejo disappointing.",
        "Ang babait ng staff pero yung registration sobrang bagal.",
        "Okay naman ang event pero I expected more.",
        "The host was funny kaso ang haba ng program.",
        "Maganda ang giveaways pero walang laman.",
        "Maayos ang food pero konti ang serving.",
        "Ang galing ng speaker, kung nakinig lang ako.",
        "Wonderful aircon, kung gumagana lang talaga.",
        "The best event ever! Kung may naenjoy lang ako kahit konti.",
        "Sobrang organized ng event, naligaw lahat ng participants.",
        "Ang efficient ng registration, pumila kami ng 2 oras.",
        "Perfect ang sound system, walang narinig ang nasa likod.",
        "Great job sa organizers, sobrang late ng start.",
    ]
    
    neutral_templates = [
        "Okay naman yung {n}, may {pw} pero may {nr} din.",
        "Pwede na yung {n}, kahit {nr} at least may {pw}.",
        "Sakto lang yung {n}. Hindi {pw}, hindi rin {w}.",
        "Average lang yung experience. May maganda pero may pangit.",
        "The {n} was decent. Could be better, could be worse.",
        "Wala lang. Di ko alam kung masaya ba ko o hindi.",
        "Ewan ko ba sa {n} na yan. Keri naman.",
        "Bahala na. Okay na rin yung {n}.",
    ]
    
    roll = random.random()
    if roll < 0.50:
        templates = negative_templates
        label = "Negative"
    elif roll < 0.85:
        templates = neutral_templates
        label = "Neutral"
    else:
        templates = positive_templates
        label = "Positive"
    
    template = random.choice(templates)
    
    try:
        comment = template.format(
            n=random.choice(nouns),
            pw=random.choice(pos_words),
            w=random.choice(neg_words),
            nr=random.choice(neg_reasons)
        )
    except:
        comment = random.choice(negative_templates)
    
    return comment + random_emoji(), label

# ============================================================
# GENERATE DATA
# ============================================================

print("\n📝 Generating 15,000 comments...")
print("   ✓ Regular comments: WITH noise")
print("   ✓ Sarcastic: CLEAN (no noise)")
print("   ✓ Code-switching: CLEAN with ALL labels")
print("   ✓ Ambiguous: NEW category")
print("   ✓ Negation patterns: NEW (Wala koy reklamo → Positive)")
print("   ✓ Double negatives: NEW (Hindi naman pangit → Positive)")
print("   ✓ Mixed sentiment: Negative\n")

data_list = []
target_total = 15000

print("   Generating Positive comments...")
pos_target = 5000
pos_generated = 0
while pos_generated < pos_target:
    choice = random.random()
    if choice < 0.40:  # Regular positive
        c, label = generate_pos()
    elif choice < 0.55:  # Subtle positive
        c, label = generate_subtle_positive()
    elif choice < 0.65:  # Short positive
        c, label = generate_short()
        if label != "Positive":
            continue
    elif choice < 0.80:  # Negation patterns (NEW!)
        c, label = generate_negation_pattern()
    elif choice < 0.90:  # Double negatives (NEW!)
        c, label = generate_double_negative()
    else:  # Positive code-switching
        c, label = generate_code_switch_improved()
        if label != "Positive":
            continue
    
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
    if choice < 0.25:  # Regular negative
        c, label = generate_neg()
    elif choice < 0.40:  # Mixed sentiment (Negative)
        c, label = generate_mixed()
    elif choice < 0.55:  # Sarcastic
        c, label = generate_sarcastic()
    elif choice < 0.70:  # Subtle negative
        c, label = generate_subtle_negative()
    elif choice < 0.85:  # Short negative
        c, label = generate_short()
        if label != "Negative":
            continue
    else:  # Negative code-switching
        c, label = generate_code_switch_improved()
        if label != "Negative":
            continue
    
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
    if choice < 0.30:  # Regular neutral
        c, label = generate_neu()
    elif choice < 0.55:  # Ambiguous
        c, label = generate_ambiguous()
    elif choice < 0.75:  # Neutral code-switching
        c, label = generate_code_switch_improved()
        if label != "Neutral":
            continue
    else:  # Short neutral
        c, label = generate_short()
        if label != "Neutral":
            continue
    
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

print("\n📝 SAMPLE NEGATION PATTERNS (FIXED - should be POSITIVE):")
negation_samples = []
for c, l in data_list:
    if any(word in c.lower() for word in ["wala koy reklamo", "wala koy ika-suggest", "walay problema"]):
        if len(negation_samples) < 5:
            negation_samples.append((c, l))
for c, l in negation_samples[:5]:
    print(f"   [{l}] {c[:80]}")

print("\n📝 SAMPLE DOUBLE NEGATIVES (FIXED - should be POSITIVE):")
double_neg_samples = []
for c, l in data_list:
    if any(word in c.lower() for word in ["hindi naman", "wala namang"]):
        if len(double_neg_samples) < 5:
            double_neg_samples.append((c, l))
for c, l in double_neg_samples[:5]:
    print(f"   [{l}] {c[:80]}")

print("\n📝 SAMPLE CODE-SWITCHING (ALL labels):")
cs_samples = []
for c, l in data_list:
    if any(word in c.lower() for word in ["kaso", "pero", "kahit", "yung", "kung"]):
        if len(cs_samples) < 5 and c not in [s[0] for s in cs_samples]:
            cs_samples.append((c, l))
for c, l in cs_samples[:5]:
    print(f"   [{l}] {c[:80]}")

print("\n📝 SAMPLE AMBIGUOUS COMMENTS:")
amb_samples = [c for c, l in data_list if c in ambiguous_phrases][:5]
for c in amb_samples[:5]:
    print(f"   {c[:80]}")

# ============================================================
# SAVE
# ============================================================

csv_file = "event_data_NOISY_IMPROVED.csv"
with open(csv_file, "w", newline="", encoding="utf-8") as f:
    writer = csv.writer(f)
    writer.writerow(["vcomment", "Label"])
    for comment, label in data_list:
        writer.writerow([comment, label])

print(f"\n✅ Saved to {csv_file}")
print("\n📌 ALL FIXES INCLUDED IN THIS VERSION:")
print("   ✓ Negation patterns: 'Wala koy reklamo' → POSITIVE")
print("   ✓ Double negatives: 'Hindi naman pangit' → POSITIVE")
print("   ✓ Code-switching: Now has Positive, Negative, AND Neutral labels")
print("   ✓ Ambiguous: NEW category (25+ phrases)")
print("   ✓ Typos: Enhanced with missing spaces, repeated letters")
print("   ✓ Mixed sentiment: Negative (correct)")
print("   ✓ Sarcastic: Clean (correct)")
print("\n📌 NEXT: Update CELL 3 to use this improved dataset:")
print("   Change DATA_FILE = 'event_data_NOISY_IMPROVED.csv'")
print("=" * 70)