<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
        }
        .logo-placeholder {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
            background-color: #f0fdf4;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #10b981;
        }
        .logo-text {
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
        .org-name {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
            margin: 5px 0;
        }
        .report-title {
            font-size: 20px;
            font-weight: bold;
            color: #047857;
            margin: 15px 0 5px;
            text-transform: uppercase;
        }
        .date-range {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin: 5px 0;
        }
        .report-meta {
            font-size: 11px;
            color: #666;
            margin-top: 10px;
        }
        .summary-section {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
        }
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #047857;
            margin-bottom: 10px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 3px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #047857;
        }
        .amount-value {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #10b981;
            color: white;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-placeholder">
            <span class="logo-text">C</span>
        </div>
        <div class="school-name">{{ $school_name }}</div>
        <div class="org-name">{{ $org_name }}</div>
        <div class="report-title">COLLECTION SUMMARY REPORT</div>
        <div class="date-range">
            {{ \Carbon\Carbon::parse($date_range['from'])->format('F d, Y') }} - 
            {{ \Carbon\Carbon::parse($date_range['to'])->format('F d, Y') }}
        </div>
        <div class="report-meta">
            Generated on: {{ $report_date }} | By: {{ $generated_by }}
        </div>
    </div>

    <div class="summary-section">
        <div class="summary-title">OVERALL SUMMARY</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Events</div>
                <div class="summary-value">{{ $summary['total_events'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Students</div>
                <div class="summary-value">{{ $summary['total_students'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Paid</div>
                <div class="summary-value">{{ $summary['total_paid'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Collected</div>
                <div class="amount-value">₱{{ number_format($summary['total_collected'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Overall Rate</div>
                <div class="amount-value">{{ $summary['overall_rate'] }}%</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Fee</th>
                <th class="text-right">Total Students</th>
                <th class="text-right">Paid</th>
                <th class="text-right">Collection Rate</th>
                <th class="text-right">Collected Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $event)
            <tr>
                <td>{{ $event['event_name'] }}</td>
                <td>{{ $event['event_date'] }}</td>
                <td>₱{{ number_format($event['event_fee'], 2) }}</td>
                <td class="text-right">{{ $event['total_students'] }}</td>
                <td class="text-right">{{ $event['paid_count'] }}</td>
                <td class="text-right">{{ $event['collection_rate'] }}%</td>
                <td class="text-right">₱{{ number_format($event['total_collected'], 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">No events found in the selected date range</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Prepared by:</div>
            <div class="font-bold">{{ $generated_by }}</div>
            <div style="font-size: 10px;">Treasurer</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Noted by:</div>
            <div class="font-bold">_____________________</div>
            <div style="font-size: 10px;">Adviser</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Approved by:</div>
            <div class="font-bold">_____________________</div>
            <div style="font-size: 10px;">President</div>
        </div>
    </div>

    <div class="footer">
        This is a system-generated report from CSUCC EMS. For official use only.
    </div>
</body>
</html>