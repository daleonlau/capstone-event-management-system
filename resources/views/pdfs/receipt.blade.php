<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt #{{ $payment->receipt_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .receipt-box { 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px;
            border: 1px solid #ddd;
        }
        .header { 
            text-align: center; 
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .details { margin-bottom: 20px; }
        .row { margin-bottom: 10px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="receipt-box">
        <div class="header">
            <h2>OFFICIAL RECEIPT</h2>
            <p>Receipt #: {{ $payment->receipt_number }}</p>
            <p>Date: {{ $payment->created_at->format('F d, Y h:i A') }}</p>
        </div>
        
        <div class="details">
            <div class="row">
                <span class="label">Student ID:</span> {{ $student->student_id }}
            </div>
            <div class="row">
                <span class="label">Student Name:</span> {{ $student->firstname }} {{ $student->lastname }}
            </div>
            <div class="row">
                <span class="label">Course/Year:</span> {{ $student->course }} - {{ $student->yearlevel }}
            </div>
            <div class="row">
                <span class="label">Event:</span> {{ $event->event_name }}
            </div>
            <div class="row">
                <span class="label">Amount Paid:</span> ₱{{ number_format($payment->amount_paid, 2) }}
            </div>
            <div class="row">
                <span class="label">Payment Method:</span> {{ ucfirst($payment->payment_method) }}
            </div>
            @if($payment->payment_notes)
            <div class="row">
                <span class="label">Notes:</span> {{ $payment->payment_notes }}
            </div>
            @endif
            <div class="row">
                <span class="label">Received by:</span> {{ $treasurer->name ?? 'Treasurer' }}
            </div>
        </div>
        
        <div style="margin-top: 40px; text-align: center;">
            <p>Thank you for your payment!</p>
            <p style="font-size: 12px; color: #666;">This is a system-generated receipt.</p>
        </div>
    </div>
</body>
</html>