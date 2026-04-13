import requests
import json

url = 'http://127.0.0.1:8001/analyze'

# 100 CHALLENGING TEST COMMENTS
test_comments = [
    # Mixed Sentiment (1-15)
    'Nindot unta ang venue pero bati kay dugay nag-start',
    'Maayo ang pagkain kaso walay lami',
    'Lingaw ang games pero saba kaayo ang venue',
    'Salamat sa effort pero sayang lang ang oras',
    'Ang galing ng host pero ang haba ng program kaya nakatulog ako',
    'The speaker was good but I could barely hear him',
    'Maganda ang venue pero ang init sa loob',
    'Maayos naman ang lahat pero may kulang pa rin',
    'The food was delicious but the serving was too small',
    'Okay naman ang event pero sana next time mas organized',
    'Nindot ang topic pero boring ang speaker',
    'Maganda ang giveaways pero hindi organized ang event',
    'The venue was nice but the sound system was terrible',
    'Maayos ang registration pero magulo ang flow',
    'Ang saya ng games pero sobrang init sa venue',
    # Sarcastic (16-25)
    'Wow galing ng time management, natapos ng 9pm ang 5pm event',
    'Perfect ang sound system, walang narinig ang nasa likod',
    'Ang efficient ng registration, pumila kami ng 2 oras',
    'Great job sa organizers, sobrang late ng start',
    'Sobrang organized ng event, naligaw lahat ng participants',
    'Best event ever! Kung may naenjoy lang ako kahit konti',
    'Ang linaw ng instructions kaya lahat naguluhan',
    'Super comfortable ng chairs, kung hindi lang masakit sa likod',
    'Ang sarap ng food, kung may natira lang sa amin',
    'Wonderful aircon, kung gumagana lang talaga',
    # Subtle Complaints (26-35)
    'Sana next time may mas maraming pagkain para sa lahat',
    'Pwede bang mas maaga ang announcement ng schedule',
    'Medyo mainit lang sa venue pagdating ng hapon',
    'Sana may water station na malapit sa seating area',
    'Ang tagal ng processing ng certificates sa dulo',
    'Medyo masikip yung aisle sa venue',
    'Sana may mas mahabang break time',
    'Pwede bang mas malinaw ang instructions',
    'Medyo matagal ang transition between speakers',
    'Sana nasunod yung schedule na nasa program',
    # Subtle Positives (36-45)
    'Buti na lang may libreng tubig kasi sobrang init',
    'Nakauwi ako ng maaga kahit mahaba ang programa',
    'At least may natutunan ako kahit hindi gaano kaganda',
    'Okay na rin ito kesa walang event na ganap',
    'Swerte at malamig sa venue kahit maraming tao',
    'Maganda yung mga binigay na examples sa discussion',
    'Nakapagtanong ako sa speaker tungkol sa topic',
    'Maaga natapos ang event kaya naka-abot pa sa ibang lakad',
    'May nakausap akong bago at naging kaibigan ko',
    'Nakatulong yung topic sa mga assignments ko',
    # Code-Switching (46-55)
    'Nagenjoy naman ako pero sana next time mas organized ang flow ng program',
    'Ang ganda ng venue kaso ang layo masyado sa aming school',
    'Maayo ang speakers pero yung sound system hindi maganda ang quality',
    'Salamat sa free food pero kulang ang serving para sa lahat ng tao',
    'The topic was very informative pero ang haba ng discussion nakatulog ako',
    'Lingaw ang games but the prizes were mejo disappointing',
    'Ang babait ng staff pero yung registration sobrang bagal',
    'Okay naman ang event pero I expected more from the organizers',
    'Maayos ang venue pero the ventilation was very poor',
    'The host was funny kaso ang daming technical difficulties',
    # Ambiguous/Neutral (56-65)
    'Wala naman akong masabi, normal lang',
    'Same lang sa mga nakaraang event na pinuntahan ko',
    'Pwede na, hindi na rin ako magreklamo',
    'Sakto lang, hindi pangit hindi maganda',
    'Okay na rin para sa libre',
    'Standard na program, walang bago',
    'Hindi ako satisfied pero hindi rin disappointed',
    'Medyo okay, may room for improvement',
    'Wala namang masyadong issue pero wala ring wow factor',
    'Parehas lang sa mga na-attend-an ko dati',
    # Very Short / One Word (66-75)
    'Ok lang',
    'Pwede na',
    'Sakto lang',
    'Wala lang',
    'Meh',
    'Fine',
    'Not bad',
    'So-so',
    'Could be better',
    'It is what it is',
    # Typos / Informal (76-85)
    'nidot kau event! lingaw kau ko!',
    'bati kau! gubot kau ang registration!',
    'slamat sa mga orgazers! hapsay kau!',
    'ang init! walang aircon! sobrang init!',
    'sayang oras! nagmahay ko!',
    'the speaker was so galing! pero haba!',
    'gnda ng venue! kaso ang layo!',
    'worst event ever! d nako uulit!',
    'galing ng sound system? wala kaming narinig!',
    'okay naman pero sana next time mas maganda',
    # Extreme/Emotional (86-95)
    'This was the most disappointing event I have ever attended in my entire life',
    'I absolutely loved everything about this event, it was perfect in every way',
    'The organizers should be ashamed of themselves for this disaster',
    'This event changed my life, I am so grateful I came',
    'I have never been so bored in my entire existence',
    'The speaker was a genius, every word was pure gold',
    'Complete and utter waste of time and money, I want a refund',
    'This was magical, I will remember this day forever',
    'I would rather watch paint dry than attend this again',
    'The best event of the year, hands down no competition',
    # Realistic Student Comments (96-100)
    'Sana lang talaga next time mas maaga yung start kasi sayang oras',
    'Ang daming tao pero kulang yung upuan, nakatayo lang kami sa gilid',
    'Yung topic maganda naman kaso yung speaker parang nagmamadali',
    'Free foods lang nagustuhan ko, yung program boring',
    'Hindi ko alam kung maganda o pangit, basta may natutunan naman ako'
]

payload = {
    'positive_comments': [],
    'suggestion_comments': test_comments
}

print('='*70)
print('CHALLENGING SENTIMENT TEST (100 Comments)')
print('='*70)
print(f'\n📤 Sending {len(test_comments)} comments to fine-tuned model...\n')

response = requests.post(url, json=payload)
result = response.json()

print('='*70)
print('📊 RESULTS SUMMARY')
print('='*70)
print(f'Total comments: {len(test_comments)}')
print(f'Positive comments: {len(result["positive_comments"])}')
print(f'Negative comments: {len(result["negative_comments"])}')
print(f'Neutral comments: {len(result["neutral_comments"])}')

print('\n' + '='*70)
print('✅ POSITIVE COMMENTS')
print('='*70)
if result['positive_comments']:
    for i, c in enumerate(result['positive_comments'], 1):
        print(f'{i}. {c}')
else:
    print('No positive comments found')

print('\n' + '='*70)
print('❌ NEGATIVE COMMENTS')
print('='*70)
if result['negative_comments']:
    for i, c in enumerate(result['negative_comments'], 1):
        print(f'{i}. {c}')
else:
    print('No negative comments found')

print('\n' + '='*70)
print('😐 NEUTRAL COMMENTS')
print('='*70)
if result['neutral_comments']:
    for i, c in enumerate(result['neutral_comments'], 1):
        print(f'{i}. {c}')
else:
    print('No neutral comments found')

print('\n' + '='*70)
print('🎯 MODEL INFO')
print('='*70)
print(f'Model type: {result.get("method_used", "Unknown")}')
print(f'Fine-tuned: {result.get("fine_tuned", False)}')
print(f'Sentiment score: {result.get("sentiment_score", 0)}')