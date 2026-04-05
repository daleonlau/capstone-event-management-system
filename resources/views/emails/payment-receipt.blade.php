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
        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #065f46;
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

    <div class="footer">
        <p>This is an official receipt from CSUCC EMS. Please keep this for your records.</p>
        <p>For any inquiries, please contact the treasurer's office.</p>
        <p>Processed by: {{ $treasurer->name ?? 'Treasurer' }}</p>
    </div>
</body>
</html>