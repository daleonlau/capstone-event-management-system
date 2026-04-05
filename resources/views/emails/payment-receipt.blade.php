<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
        }
        .organization-name {
            font-size: 18px;
            font-weight: bold;
            color: #065f46;
            margin: 5px 0;
        }
        .school-name {
            font-size: 14px;
            color: #047857;
            margin: 5px 0;
        }
        .receipt-title {
            font-size: 22px;
            font-weight: bold;
            color: #047857;
            margin: 20px 0;
            text-align: center;
        }
        .receipt-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .receipt-number {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
            text-align: center;
            margin-bottom: 20px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .details-table td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #059669;
            text-align: right;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .thank-you {
            text-align: center;
            font-size: 16px;
            color: #047857;
            margin: 20px 0;
        }
        .payment-method {
            display: inline-block;
            padding: 3px 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            font-size: 11px;
            text-transform: uppercase;
        }
        .contact-info {
            background-color: #f0fdf4;
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
            text-align: center;
        }
        .contact-info p {
            margin: 5px 0;
        }
        .reply-note {
            font-size: 11px;
            color: #059669;
            margin-top: 10px;
        }
        .org-footer {
            background-color: #f0fdf4;
            padding: 8px;
            border-radius: 6px;
            margin-top: 10px;
            text-align: center;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">EventFlow</div>
        <div class="organization-name">{{ $organizationName }}</div>
        <div class="school-name">Caraga State University - Cabadbaran Campus</div>
        <div class="receipt-title">OFFICIAL RECEIPT</div>
    </div>

    <div class="receipt-box">
        <div class="receipt-number">Receipt No: {{ $payment->receipt_number }}</div>
        
        <table class="details-table">
            <tr>
                <td>Student ID:</td>
                <td>{{ $student->student_id }}</td>
            </tr>
            <tr>
                <td>Student Name:</td>
                <td>{{ $student->firstname }} {{ $student->lastname }}</td>
            </tr>
            <tr>
                <td>Program:</td>
                <td>{{ $student->course }}</td>
            </tr>
            <tr>
                <td>Year Level:</td>
                <td>{{ $student->yearlevel }}</td>
            </tr>
            <tr>
                <td>College:</td>
                <td>{{ $student->department ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Event:</td>
                <td>{{ $event->event_name }}</td>
            </tr>
            <tr>
                <td>Payment Date:</td>
                <td>{{ $payment->created_at->format('F d, Y h:i A') }}</td>
            </tr>
            <tr>
                <td>Payment Method:</td>
                <td><span class="payment-method">{{ ucfirst($payment->payment_method) }}</span></td>
            </tr>
            <tr>
                <td>Notes:</td>
                <td>{{ $payment->payment_notes ?? 'No notes' }}</td>
            </tr>
            <tr>
                <td>Amount Paid:</td>
                <td><strong>₱{{ number_format($payment->amount_paid, 2) }}</strong></td>
            </tr>
        </table>

        <div class="total-amount">
            Total Amount: ₱{{ number_format($payment->amount_paid, 2) }}
        </div>
    </div>

    <div class="thank-you">
        Thank you for your payment!
    </div>

    <!-- Organization Footer -->
    <div class="org-footer">
        <strong>{{ $organizationName }}</strong><br>
        This is an official receipt from {{ $organizationName }}. Please keep this for your records.
    </div>

    <!-- Contact Info with Treasurer's Email -->
    <div class="contact-info">
        <p><strong>Processed by:</strong> {{ $treasurer->name ?? 'Treasurer' }}</p>
        <p><strong>Contact Email:</strong> 
            <a href="mailto:{{ $treasurer->email ?? config('mail.from.address') }}" style="color: #059669;">
                {{ $treasurer->email ?? config('mail.from.address') }}
            </a>
        </p>
        <p class="reply-note">💡 For any questions about this payment, simply reply to this email and it will go directly to the treasurer who processed your payment.</p>
    </div>

    <div class="footer">
        <p>This is a system-generated receipt from Event Flow.</p>
        <p>Generated on: {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html> 