<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
        }
        .school-name {
            font-size: 14px;
            font-weight: bold;
            color: #065f46;
        }
        .content {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .event-details {
            background-color: #e5e7eb;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .button {
            display: inline-block;
            background-color: #10b981;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">CSUCC EMS</div>
        <div class="school-name">Caraga State University - Cabadbaran Campus</div>
        <div class="school-name">Office of the Planning and Quality Management Services</div>
    </div>

    <p>Dear {{ $organization->name }},</p>

    <p>Greetings from the Office of the Planning and Quality Management Services!</p>

    <div class="content">
        <p>Please find attached the evaluation report for your recently concluded event:</p>
        
        <div class="event-details">
            <strong>Event:</strong> {{ $event->event_name }}<br>
            <strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date_start)->format('F d, Y') }}<br>
            <strong>Total Responses:</strong> {{ $evaluation->total_responses }}<br>
            <strong>Overall Satisfaction:</strong> {{ number_format($evaluation->predicted_satisfaction ?? 3.0, 2) }}/5.0<br>
            <strong>Report Generated:</strong> {{ $report_date }}
        </div>

        <p>The report contains detailed analysis including:</p>
        <ul>
            <li>Executive Summary and AI-Powered Insights</li>
            <li>Category Performance Analysis</li>
            <li>Strengths and Areas for Improvement</li>
            <li>Participant Comments and Suggestions</li>
            <li>Actionable Recommendations</li>
        </ul>

        <p>Should you have any questions or need further clarification, please don't hesitate to contact our office.</p>

        <center>
            <a href="{{ url('/admin/reports/' . $evaluation->id . '/download') }}" class="button">
                Download Full Report
            </a>
        </center>
    </div>

    <p>Thank you for your continued support in maintaining quality standards at CSUCC.</p>

    <p>Respectfully yours,<br>
    <strong>Office of the Planning and Quality Management Services</strong></p>

    <div class="footer">
        <p>This is an automated email from CSUCC EMS. Please do not reply to this email.</p>
        <p>© {{ date('Y') }} Caraga State University - Cabadbaran Campus. All rights reserved.</p>
    </div>
</body>
</html>