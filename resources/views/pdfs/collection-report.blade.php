<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Collection Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>COLLECTION REPORT</h1>
        <h2>{{ $event->event_name ?? 'N/A' }}</h2>
        <p>Generated on: {{ $report_date ?? date('F d, Y') }} | By: {{ $generated_by ?? 'System' }}</p>
        <p>Organization: {{ $org_name ?? 'N/A' }}</p>
        <p>Event Date: {{ date('F d, Y', strtotime($event->event_date_start)) }}</p>
        <p>Event Fee: ₱{{ number_format($event->event_fee ?? 0, 2) }}</p>
    </div>

    <h3>COLLECTION SUMMARY</h3>
    <table>
        <tr>
            <th>Metric</th>
            <th>Count</th>
            <th>Amount (₱)</th>
            <th>Percentage</th>
        </tr>
        <tr>
            <td>Total Students Assigned</td>
            <td class="text-center">{{ $summary['total_students'] ?? 0 }}</td>
            <td class="text-right">-</td>
            <td class="text-center">100%</td>
        </tr>
        <tr>
            <td>✓ Fully Paid Students</td>
            <td class="text-center">{{ $summary['paid_students'] ?? 0 }}</td>
            <td class="text-right">₱{{ number_format($summary['total_collected'] ?? 0, 2) }}</td>
            <td class="text-center">{{ $summary['collection_rate'] ?? 0 }}%</td>
        </tr>
        <tr>
            <td>⏳ Pending Students</td>
            <td class="text-center">{{ $summary['pending_students'] ?? 0 }}</td>
            <td class="text-right">-</td>
            <td class="text-center">-</td>
        </tr>
        <tr>
            <td>❌ Not Paid Students</td>
            <td class="text-center">{{ $summary['not_paid_students'] ?? 0 }}</td>
            <td class="text-right">-</td>
            <td class="text-center">-</td>
        </tr>
        <tr style="background-color: #e8f5e9;">
            <td><strong>Total Collected</strong></td>
            <td class="text-center">-</td>
            <td class="text-right"><strong>₱{{ number_format($summary['total_collected'] ?? 0, 2) }}</strong></td>
            <td class="text-center"><strong>{{ $summary['collection_rate'] ?? 0 }}%</strong></td>
        </tr>
    </table>

    <div class="footer" style="margin-top: 30px; text-align: center; font-size: 9px;">
        This is a system-generated report from CSUCC EMS. For official use only.<br>
        Generated on {{ $report_date ?? date('F d, Y') }}
    </div>
</body>
</html>