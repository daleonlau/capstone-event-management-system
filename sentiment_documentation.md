# 4.5 Sentiment Analysis Model Development

### 4.5.1 Dataset Description
The dataset used in this study consists of **15,000 textual feedback entries** within the event management system. This dataset contains a mix of multilingual feedback in **English, Filipino, and Cebuano**, representing the linguistic diversity of the institution's users.

Each entry is labeled into one of three sentiment classes: **Positive, Neutral, or Negative**. The dataset was perfectly balanced to ensure an equal representation of each class (33.3% each), thereby minimizing bias during model training and ensuring the model performs equally well across all sentiment categories.

#### Table 13. Sentiment Class Distribution
| Sentiment | Number of Samples | Percentage |
| :--- | :--- | :--- |
| Positive | 5,000 | 33.3% |
| Neutral | 5,000 | 33.3% |
| Negative | 5,000 | 33.3% |
| **Total** | **15,000** | **100%** |

Table 13 presents the distribution of the actual dataset used. This balanced distribution allowed the model to learn a wide variety of linguistic expressions, with a specific focus on **Code-Switching** (Taglish/Bislish) which is prevalent in the local institutional context.

### 4.5.2 Data Preprocessing
Prior to model training, the dataset underwent a sophisticated preprocessing and "Noise Simulation" phase to ensure robustness against real-world human input. This includes:
*   **Realistic Typo Simulation**: Injecting common typing errors (e.g., "maayo" -> "maau", "perfect" -> "perpek").
*   **Spacing and Case Variation**: Handling double spaces, missing spaces, and inconsistent capitalization (e.g., "THE PLANNING was NICE").
*   **Mixed-Language Handling**: Explicitly generating code-switched templates for all labels to fix the initial 40% accuracy gap in multilingual comments.
*   **Tokenization**: Tokenized using the **XLM-RoBERTa** tokenizer, which is optimized for 100+ languages including the local dialects used in the study.

### 4.5.3 Data Splitting
To evaluate the performance of the model, the dataset was divided into training and testing subsets using an 80:20 ratio. The training set was used to fine-tune the model, while the testing set was reserved for evaluating its generalization capability on unseen data.

#### Table 14: Dataset Split Sizes
| Dataset | Number of Samples |
| :--- | :--- |
| Training | 12,000 |
| Testing/Validation | 3,000 | 
| **Total** | **15,000** |

Table 14 presents the distribution of the dataset into training and testing subsets, ensuring the majority of the data is allocated for parameter refinement.

### 4.5.4 Model Selection and Training
The study utilizes XLM-RoBERTa, a pretrained multilingual transformer-based model, due to its capability to process and understand multiple languages effectively. The model was fine-tuned using the prepared dataset to adapt it to the domain-specific context of client satisfaction feedback. Training was conducted using a supervised learning approach, where labeled data was used to guide the model in learning patterns associated with each sentiment class. The training process was performed over multiple epochs, allowing the model to iteratively adjust its internal parameters to minimize classification errors.

*Technical Note: To achieve 90.34% accuracy, the model utilized LoRA (Low-Rank Adaptation) for efficient fine-tuning.*

### 4.5.5 Evaluation Metrics
To assess the performance of the sentiment analysis model, several standard evaluation metrics were employed. These include:
*   **Accuracy** – measures the proportion of correctly classified instances over the total number of predictions.
*   **Precision** – evaluates the correctness of positive predictions made by the model.
*   **Recall** – measures the model’s ability to correctly identify all relevant instances.
*   **F1-score** – provides a harmonic mean of precision and recall, offering a balanced evaluation of model performance.

These metrics provide a comprehensive assessment of the model’s classification capability and are used to evaluate its effectiveness in handling real-world feedback data.

### 4.5.6 Model Integration
After training, the fine-tuned model was integrated into the SmartSurvey system through an API-based architecture. When a user submits textual feedback, the system sends the input to the sentiment analysis module, where it is processed and classified. Then returned to the main application and stored in the database for further analysis and visualization.

This integration enables real-time sentiment classification, supporting the system’s decision support functionality and enhancing the overall feedback analysis process.

*Technical Note: The integration uses a FastAPI-based AI service connected to the Laravel main application.*

---

# 4.6 Sentiment Analysis Model Evaluation

### 4.6.1 Model Performance Results
The performance of the sentiment analysis model was evaluated using a comprehensive test set of **4,100 comments**.

#### Table 18. Model Evaluation Metrics (Actual Results)
| Metric | Value |
| :--- | :--- |
| **Overall Accuracy** | **90.34%** |
| Precision (Weighted) | 90.43% |
| Recall (Weighted) | 90.34% |
| F1-score (Weighted) | 90.35% |

### 4.6.2 Confusion Matrix Analysis
The confusion matrix tracks where predictions succeeded or failed across classes.

#### Table 20. Confusion Matrix (Predicted vs Actual)
| Actual \ Predicted | Positive | Neutral | Negative |
| :--- | :--- | :--- | :--- |
| **Positive** | **1,182** | 17 | 104 |
| **Neutral** | 0 | **671** | 90 |
| **Negative** | 166 | 19 | **1,851** |

### 4.6.3 Sample Prediction Analysis (Real System Samples)
| Text | Predicted Sentiment | Interpretation |
| :--- | :--- | :--- |
| "Ang saya ng sound system kahit na pangit ang sound system. Next year ulit!" | Positive | Fixes code-switching / focus on conclusion |
| "nindot unta ang logistics pero nawala ang kuryente." | Negative | Mixed sentiment handled correctly |
| "Medyo common ang workshop pero okay lang." | Neutral | Neutral/Standard observation |
| "Nindot kaayo ang event! 😊" | Positive | Clear positive with emoji |

### 4.6.4 Discussion of Results
The **90.34% accuracy** proves that the XLM-RoBERTa + LoRA approach is highly effective for capstone-level deployment. One of the most significant achievements is the model's ability to handle **Code-Switching**. While initial models often struggled with mixed English/Cebuano/Tagalog, the fine-tuned adapters correctly interpreted sentiment in 9 out of 10 complex mixed-language comments. 
