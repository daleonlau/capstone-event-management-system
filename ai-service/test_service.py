"""
Quick test script for the sentiment analysis service
Run this after starting the service to verify it works
"""

import requests

def test_service():
    url = "http://127.0.0.1:8001/analyze"
    
    # Test comments in Bisaya, Tagalog, English
    test_data = {
        "positive_comments": [
            "Nindot kaayo ang event! Nalingaw jud ko sa tanan activities.",
            "Salamat sa libreng pagkain busog kami",
            "Ang galing ng host sobrang nakakatawa",
            "Maayo kaayo ang mga organizers, salamat kaayo!",
            "The speaker was very informative and engaging.",
        ],
        "suggestion_comments": [
            "Bati kaayo kay dugay nag-start ang program",
            "Ang init sa venue walang aircon, hindi ako nagenjoy",
            "Sana next time mas maaga ang announcement ng schedule",
            "The sound system was terrible, couldn't hear anything",
            "Gubot kaayo ang registration, pila mi og 1 hour",
        ]
    }
    
    print("=" * 70)
    print("EventFlow Sentiment Analysis - Quick Test")
    print("=" * 70)
    print(f"\n📤 Sending {len(test_data['positive_comments'])} positive comments")
    print(f"📤 Sending {len(test_data['suggestion_comments'])} suggestion comments")
    
    try:
        response = requests.post(url, json=test_data, timeout=30)
        result = response.json()
        
        print(f"\n✅ Response received!")
        print(f"   Method: {result['method_used']}")
        print(f"   Sentiment Score: {result['sentiment_score']}")
        
        print(f"\n📊 RESULTS:")
        print(f"   Positive comments: {len(result['positive_comments'])}")
        for c in result['positive_comments']:
            print(f"     [+] {c[:60]}...")
        
        print(f"\n   Negative comments: {len(result['negative_comments'])}")
        for c in result['negative_comments']:
            print(f"     [-] {c[:60]}...")
        
        print(f"\n   Neutral comments: {len(result['neutral_comments'])}")
        for c in result['neutral_comments']:
            print(f"     [~] {c[:60]}...")
        
        print("\n" + "=" * 70)
        print("✅ Test completed successfully!")
        
    except Exception as e:
        print(f"\n❌ ERROR: {e}")
        print("Make sure the service is running: python main.py")

if __name__ == "__main__":
    test_service()