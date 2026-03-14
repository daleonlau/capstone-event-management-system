# Enhanced Event Management System with AI-Powered Decision Support

A capstone project for Caraga State University - Cabadbaran Campus (CSUCC) that streamlines event planning, financial tracking, and provides AI-powered insights for student-led organizations.

## 📋 About The Project

The Enhanced Event Management System is designed to address the limitations of manual event management processes currently used by student organizations at CSUCC. The system provides a centralized platform for:

- **Event Management**: Create and track event proposals with adviser approval workflow
- **Collection Management**: Process payments, generate PDF receipts, and send email confirmations
- **Evaluation System**: Customizable questionnaires with QR code access for student feedback
- **AI-Powered Insights**: Random Forest algorithm analyzes feedback to provide actionable recommendations
- **Role-Based Access**: Separate dashboards for Presidents, Advisers, and Treasurers

## 🛠️ Built With

- **Backend**: [Laravel 11](https://laravel.com) - PHP framework with expressive, elegant syntax
- **Frontend**: [Vue.js 3](https://vuejs.org) with [Inertia.js](https://inertiajs.com)
- **Database**: MySQL
- **AI Service**: Python [FastAPI](https://fastapi.tiangolo.com) with [Scikit-learn](https://scikit-learn.org) (Random Forest)
- **Queue**: Laravel Queues for background processing
- **Email**: SMTP with Gmail
- **PDF Generation**: DomPDF

## 🚀 Features

### Event Management Module
- Structured event proposal templates
- Digital document upload for signed proposals
- Adviser approval workflow with rejection reasons
- Status tracking (pending_document, pending_approval, approved, rejected, finished)
- Automatic population of eligible students into participant lists

### Collection Management Module
- Individual and bulk payment processing
- Automatic receipt number generation
- PDF receipt generation and email delivery
- Real-time collection summaries
- Receipt viewing, downloading, and resending

### Evaluation Module
- Customizable questionnaire builder with categories
- Likert scale (1-5) ratings and open-ended comments
- Dynamic question-to-category mapping based on keyword recognition
- QR code generation for each evaluation form
- Student verification against event participant lists
- 75% response threshold for AI analysis

### AI Decision Support
- Python FastAPI microservice with Random Forest algorithm
- Category performance breakdowns with average ratings
- Identification of strengths (≥3.5) and weaknesses (<3.5)
- Actionable recommendations for improvement
- Overall satisfaction scores and success probability calculations
- Interactive visual dashboards

## 📁 Project Structure
