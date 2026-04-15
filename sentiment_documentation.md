# 4.5 Sentiment Analysis Model Development

### 4.5.1 Dataset Description
The dataset used in this study consists of 15,000 textual feedback entries collected and generated to simulate real-world client responses in a service evaluation context. The dataset includes multilingual comments written in English, Filipino, and Cebuano to reflect the diverse linguistic background of users within the institution.
Each entry in the dataset is labeled into one of three sentiment classes: Positive, Neutral, or Negative. The dataset was balanced to ensure an equal representation of each class, thereby minimizing bias during model training.

Table 13. Sentiment Class Distribution
| Sentiment | Number of Samples |
| :--- | :--- |
| Positive | 5,000 |
| Neutral | 5,000 |
| Negative | 5,000 |
| **Total** | **15,000** |

Table 13 presents the distribution of the dataset used in training the sentiment analysis model. The dataset contains a relatively balanced distribution across sentiment classes, although slight variations exist. This distribution allows the model to learn patterns from each sentiment category while maintaining generalization capability.

### 4.5.2 Data Preprocessing
Prior to model training, the dataset underwent preprocessing to ensure data consistency and quality. This includes cleaning operations such as removal of unnecessary characters, normalization of text, and handling of mixed-language inputs. Tokenization was performed using the tokenizer associated with the selected transformer model to convert textual input into a format suitable for machine learning processing.

### 4.5.3 Data Splitting
To evaluate the performance of the model, the dataset was divided into training and testing subsets using an 80:20 ratio. The training set was used to fine-tune the model, while the testing set was reserved for evaluating its generalization capability on unseen data. Stratified sampling was applied to maintain balanced class distribution across both subsets.

Table 14: Dataset Split Sizes
| Dataset | Number of Samples |
| :--- | :--- |
| Training | 12,000 |
| Validation | 1,500 |
| Testing | 1,500 |
| **Total** | **15,000** |

Table 14 presents the distribution of the dataset into training, validation, and testing subsets. The majority of the data is allocated for training to allow the model to learn effectively, while smaller portions are reserved for validation and testing to evaluate model performance.

Table 15. Training Dataset Label Distribution
| Sentiment | Number of Samples |
| :--- | :--- |
| Positive | 4,000 |
| Neutral | 4,000 |
| Negative | 4,000 |
| **Total** | **12,000** |

Table 16. Validation Dataset Label Distribution
| Sentiment | Number of Samples |
| :--- | :--- |
| Positive | 500 |
| Neutral | 500 |
| Negative | 500 |
| **Total** | **1,500** |

Table 17. Test Dataset Label Distribution
| Sentiment | Number of Samples |
| :--- | :--- |
| Positive | 500 |
| Neutral | 500 |
| Negative | 500 |
| **Total** | **1,500** |

### 4.5.4 Model Selection and Training
The study utilizes XLM-RoBERTa, a pretrained multilingual transformer-based model, due to its capability to process and understand multiple languages effectively. The model was fine-tuned using the prepared dataset to adapt it to the domain-specific context of client satisfaction feedback. Training was conducted using a supervised learning approach, where labeled data was used to guide the model in learning patterns associated with each sentiment class. The training process was performed over multiple epochs, allowing the model to iteratively adjust its internal parameters to minimize classification errors.

### 4.5.5 Evaluation Metrics
To assess the performance of the sentiment analysis model, several standard evaluation metrics were employed. These include:
**Accuracy** – measures the proportion of correctly classified instances over the total number of predictions.
**Precision** – evaluates the correctness of positive predictions made by the model.
**Recall** – measures the model’s ability to correctly identify all relevant instances.
**F1-score** – provides a harmonic mean of precision and recall, offering a balanced evaluation of model performance.
These metrics provide a comprehensive assessment of the model’s classification capability and are used to evaluate its effectiveness in handling real-world feedback data.

### 4.5.6 Model Integration
After training, the fine-tuned model was integrated into the SmartSurvey system through an API-based architecture. When a user submits textual feedback, the system sends the input to the sentiment analysis module, where it is processed and classified. Then returned to the main application and stored in the database for further analysis and visualization.
This integration enables real-time sentiment classification, supporting the system’s decision support functionality and enhancing the overall feedback analysis process.

---

# 4.6 Sentiment Analysis Model Evaluation

### 4.6.1 Model Performance Results
The performance of the sentiment analysis model was evaluated using the defined metrics.

Table 18. Model Evaluation
| Metric | Value |
| :--- | :--- |
| Accuracy | 90.34% |
| Precision | 90.43% |
| Recall | 90.34% |
| F1-score | 90.35% |

Table 18 shows that the results indicates that the model achieved high performance across all evaluation metrics, demonstrating its effectiveness in classifying multilingual sentiment data.

Table 19. Training Performance (Epoch 1 & 2)
| Epoch | Training Loss | Validation Loss | Accuracy | F1-score |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 0.1259 | 0.0393 | 85.15% | 84.82% |
| 2 | 0.0159 | 0.0348 | 90.34% | 90.35% |

Table 20. Sample Prediction Analysis
| Text | Predicted Sentiment | Interpretation |
| :--- | :--- | :--- |
| "Nindot kaayo ang event! Grammarly handled everything well." | Positive | Strong positive expressions |
| "Okay naman yung event, may naganda pero may pangit din." | Neutral | Mixed sentiment |
| "Bati ang sounds, sige ug putol-putol." | Negative | Direct negative feedback |

### 4.6.2 Confusion Matrix Analysis
The confusion matrix shows that most predictions are correctly classified, as indicated by the high values along the diagonal. Minor misclassifications were observed between Neutral and Positive classes, as well as Neutral and Negative classes. This is due to the presence of mixed or subtle sentiment expressions, which can be difficult to classify even for human evaluators.

### 4.6.4 Discussion of Results
The high performance of the model can be attributed to the use of a pretrained transformer model and a balanced dataset. The model is capable of accurately classifying clear sentiment expressions across multiple languages. By utilizing a LoRA-based fine-tuning approach, the system achieves a 90.34% accuracy level, making it highly reliable for institutional feedback analysis.
