<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $payment->receipt_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
        }
        
        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #10b981;
        }
        
        .school-name {
            font-size: 14px;
            font-weight: bold;
            color: #065f46;
            margin: 5px 0;
        }
        
        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            color: #047857;
            margin: 20px 0;
            text-align: center;
            text-decoration: underline;
        }
        
        .receipt-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .receipt-number {
            font-size: 14px;
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
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .details-table td:first-child {
            font-weight: bold;
            width: 35%;
        }
        
        .total-amount {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
            text-align: right;
            margin-top: 20px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        
        .thank-you {
            text-align: center;
            font-size: 14px;
            color: #047857;
            margin: 20px 0;
        }
        
        .payment-method {
            display: inline-block;
            padding: 2px 6px;
            background-color: #e5e7eb;
            border-radius: 4px;
            font-size: 9px;
            text-transform: uppercase;
        }
        
        .contact-info {
            background-color: #f0fdf4;
            padding: 10px;
            border-radius: 6px;
            margin-top: 15px;
            text-align: center;
        }
        
        .contact-info p {
            margin: 3px 0;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .signature-name {
            font-weight: bold;
            margin-top: 5px;
            text-align: center;
        }
        
        .signature-title {
            font-size: 8px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">CSUCC EMS</div>
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

    <div class="contact-info">
        <p><strong>Processed by:</strong> {{ $treasurer->name ?? 'Treasurer' }}</p>
        <p><strong>Email:</strong> {{ $treasurer->email ?? config('mail.from.address') }}</p>
    </div>

    <div class="signature-line"></div>
    <div class="signature-name">{{ $treasurer->name ?? 'Treasurer' }}</div>
    <div class="signature-title">Treasurer / Finance Officer</div>

    <div class="footer">
        <p>This is an official receipt from CSUCC EMS. Please keep this for your records.</p>
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>