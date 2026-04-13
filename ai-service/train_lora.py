import os
import csv
import torch
import random
from sklearn.model_selection import train_test_split
from torch.utils.data import Dataset, DataLoader
from transformers import XLMRobertaTokenizer, XLMRobertaForSequenceClassification
from peft import LoraConfig, get_peft_model, TaskType

# ============================================================
# CONFIGURATION
# ============================================================

MODEL_NAME = "FacebookAI/xlm-roberta-base"
OUTPUT_PATH = "./lora_finetuned/"
DATA_FILE = "event_data_NOISY.csv"
NUM_EPOCHS = 3
BATCH_SIZE = 16
LEARNING_RATE = 3e-4
MAX_LENGTH = 64
LORA_R = 8
LORA_ALPHA = 32
LORA_DROPOUT = 0.3  # Increased dropout to prevent overfitting

LABEL2ID = {"Negative": 0, "Neutral": 1, "Positive": 2}
ID2LABEL = {0: "Negative", 1: "Neutral", 2: "Positive"}

class EventDataset(Dataset):
    def __init__(self, texts, labels, tokenizer):
        self.encodings = tokenizer(
            texts,
            truncation=True,
            padding="max_length",
            max_length=MAX_LENGTH,
            return_tensors="pt"
        )
        self.labels = torch.tensor(labels, dtype=torch.long)

    def __len__(self):
        return len(self.labels)

    def __getitem__(self, idx):
        return {
            "input_ids": self.encodings["input_ids"][idx],
            "attention_mask": self.encodings["attention_mask"][idx],
            "labels": self.labels[idx]
        }

def load_data(filepath):
    texts, labels = [], []
    with open(filepath, encoding="utf-8") as f:
        reader = csv.DictReader(f)
        for row in reader:
            label = row.get("Label", "").strip()
            text = row.get("vcomment", "").strip()
            if label in LABEL2ID and text:
                texts.append(text)
                labels.append(LABEL2ID[label])
    
    unique = {}
    for t, l in zip(texts, labels):
        if t not in unique:
            unique[t] = l
    
    unique_texts = list(unique.keys())
    unique_labels = list(unique.values())
    
    print(f"   Unique comments: {len(unique_texts)}")
    print(f"   Removed {len(texts) - len(unique_texts)} duplicates")
    
    train_texts, val_texts, train_labels, val_labels = train_test_split(
        unique_texts, unique_labels, test_size=0.1, random_state=42, stratify=unique_labels
    )
    
    overlap = set(train_texts).intersection(set(val_texts))
    if overlap:
        print(f"   ❌ ERROR: {len(overlap)} overlapping comments!")
        raise ValueError("Data leakage detected!")
    
    print(f"   ✅ No leakage! Train: {len(train_texts)}, Val: {len(val_texts)}")
    
    return train_texts, val_texts, train_labels, val_labels

def main():
    print("=" * 70)
    print("  LoRA FINE-TUNING - NOISY Training Data")
    print("=" * 70)

    device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
    print(f"\\n💻 Using device: {device}")

    print("\\n📚 Loading PRE-TRAINED model from HuggingFace...")
    print(f"   Model: {MODEL_NAME}")
    tokenizer = XLMRobertaTokenizer.from_pretrained(MODEL_NAME)
    model = XLMRobertaForSequenceClassification.from_pretrained(
        MODEL_NAME,
        num_labels=3,
        ignore_mismatched_sizes=True
    )
    model.to(device)

    lora_config = LoraConfig(
        task_type=TaskType.SEQ_CLS,
        r=LORA_R,
        lora_alpha=LORA_ALPHA,
        lora_dropout=LORA_DROPOUT,
        target_modules=["query", "value"],
        bias="none",
    )
    model = get_peft_model(model, lora_config)

    trainable = sum(p.numel() for p in model.parameters() if p.requires_grad)
    total = sum(p.numel() for p in model.parameters())
    print(f"\\n📊 Trainable: {trainable:,} / {total:,} ({trainable/total*100:.2f}%)")
    print(f"   Only training LoRA adapters - base model stays frozen")

    print(f"\\n📁 Loading NOISY training data from {DATA_FILE}...")
    train_texts, val_texts, train_labels, val_labels = load_data(DATA_FILE)

    train_dataset = EventDataset(train_texts, train_labels, tokenizer)
    val_dataset = EventDataset(val_texts, val_labels, tokenizer)

    train_loader = DataLoader(train_dataset, batch_size=BATCH_SIZE, shuffle=True)
    val_loader = DataLoader(val_dataset, batch_size=BATCH_SIZE)

    optimizer = torch.optim.AdamW(model.parameters(), lr=LEARNING_RATE, weight_decay=0.01)
    scheduler = torch.optim.lr_scheduler.LinearLR(optimizer, start_factor=1.0, end_factor=0.5, total_iters=NUM_EPOCHS)

    best_accuracy = 0.0

    for epoch in range(NUM_EPOCHS):
        print(f"\\n{'='*50}")
        print(f"🔄 Epoch {epoch+1}/{NUM_EPOCHS}")
        print(f"{'='*50}")

        model.train()
        total_loss = 0
        
        for step, batch in enumerate(train_loader):
            input_ids = batch["input_ids"].to(device)
            attention_mask = batch["attention_mask"].to(device)
            labels = batch["labels"].to(device)

            optimizer.zero_grad()
            outputs = model(
                input_ids=input_ids,
                attention_mask=attention_mask,
                labels=labels
            )
            loss = outputs.loss
            loss.backward()
            torch.nn.utils.clip_grad_norm_(model.parameters(), 1.0)
            optimizer.step()
            total_loss += loss.item()

            if (step + 1) % 100 == 0:
                print(f"  Step {step+1}/{len(train_loader)} | Loss: {loss.item():.4f}")

        avg_loss = total_loss / len(train_loader)
        print(f"\\n  📉 Avg Training Loss: {avg_loss:.4f}")

        model.eval()
        correct = 0
        total_count = 0
        
        with torch.no_grad():
            for batch in val_loader:
                input_ids = batch["input_ids"].to(device)
                attention_mask = batch["attention_mask"].to(device)
                labels = batch["labels"].to(device)
                
                outputs = model(
                    input_ids=input_ids,
                    attention_mask=attention_mask
                )
                preds = torch.argmax(outputs.logits, dim=1)
                correct += (preds == labels).sum().item()
                total_count += len(labels)

        accuracy = correct / total_count * 100
        print(f"  📈 Validation Accuracy: {accuracy:.1f}%")

        if accuracy > best_accuracy:
            best_accuracy = accuracy
            os.makedirs(OUTPUT_PATH, exist_ok=True)
            model.save_pretrained(OUTPUT_PATH)
            tokenizer.save_pretrained(OUTPUT_PATH)
            print(f"  ⭐ New best fine-tuned model! Saved to {OUTPUT_PATH}")

        scheduler.step()

    print(f"\\n{'='*70}")
    print(f"✅ FINE-TUNING COMPLETE!")
    print(f"   Training data: NOISY (typos, spacing issues, case variations)")
    print(f"   Best accuracy: {best_accuracy:.1f}%")
    print(f"   Fine-tuned model saved to: {OUTPUT_PATH}")
    print(f"{'='*70}")

if __name__ == "__main__":
    main()