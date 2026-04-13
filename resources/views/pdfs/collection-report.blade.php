<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection Report - {{ $event->event_name ?? 'N/A' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #1a472a;
            padding-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1a472a;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info {
            font-size: 9px;
            color: #666;
            margin-bottom: 3px;
        }
        .section {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            background-color: #1a472a;
            color: white;
            padding: 6px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #e8f5e9;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge-paid { color: #27ae60; font-weight: bold; }
        .badge-pending { color: #f39c12; font-weight: bold; }
        .badge-unpaid { color: #e74c3c; font-weight: bold; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 30px;
            padding-top: 5px;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="title">COLLECTION REPORT</div>
    <div class="subtitle">{{ $event->event_name ?? 'N/A' }}</div>
    <div class="info">Generated on: {{ $report_date ?? date('F d, Y') }} | By: {{ $generated_by ?? 'System' }}</div>
    <div class="info">Organization: {{ $org_name ?? 'N/A' }}</div>
    <div class="info">Event Date: {{ date('F d, Y', strtotime($event->event_date_start)) }}</div>
    <div class="info">Event Fee: ₱{{ number_format($event->event_fee ?? 0, 2) }}</div>
</div>

<div class="section">
    <div class="section-title">COLLECTION SUMMARY</div>
    <table>
        <thead>
            <tr>
                <th>Metric</th>
                <th>Count</th>
                <th>Amount (₱)</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
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
                <td>⏳ Pending Students (Partial Payment)</td>
                <td class="text-center">{{ $summary['pending_students'] ?? 0 }}</td>
                <td class="text-right">-</td>
                <td class="text-center">{{ ($summary['total_students'] ?? 0) > 0 ? round((($summary['pending_students'] ?? 0) / ($summary['total_students'] ?? 1)) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>❌ Unpaid Students (No Payment)</td>
                <td class="text-center">{{ $summary['not_paid_students'] ?? 0 }}</td>
                <td class="text-right">-</td>
                <td class="text-center">{{ ($summary['total_students'] ?? 0) > 0 ? round((($summary['not_paid_students'] ?? 0) / ($summary['total_students'] ?? 1)) * 100, 1) : 0 }}%</td>
            </tr>
            <tr style="background-color: #e8f5e9;">
                <td><strong>Actual Total Collected</strong></td>
                <td class="text-center">-</td>
                <td class="text-right"><strong>₱{{ number_format($summary['total_collected'] ?? 0, 2) }}</strong></td>
                <td class="text-center"><strong>{{ $summary['collection_rate'] ?? 0 }}%</strong></td>
            </tr>
            <tr style="background-color: #fff3e0;">
                <td><strong>Outstanding Balance</strong></td>
                <td class="text-center">{{ ($summary['pending_students'] ?? 0) + ($summary['not_paid_students'] ?? 0) }}</td>
                <td class="text-right"><strong>₱{{ number_format(($summary['expected_total'] ?? 0) - ($summary['total_collected'] ?? 0), 2) }}</strong></td>
                <td class="text-center">{{ ($summary['expected_total'] ?? 0) > 0 ? number_format((((($summary['expected_total'] ?? 0) - ($summary['total_collected'] ?? 0)) / ($summary['expected_total'] ?? 1)) * 100), 1) : 0 }}%</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="section">
    <div class="section-title">STUDENT PAYMENT LIST</div>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Program</th>
                <th>Year</th>
                <th>Amount (₱)</th>
                <th>Status</th>
                <th>Payment Date</th>
                <th>Receipt No.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students ?? [] as $index => $student)
            <tr style="{{ $index % 2 == 0 ? 'background-color: #f9fafb;' : '' }}">
                <td class="text-center">{{ $student['student_id'] ?? 'N/A' }}</td>
                <td>{{ $student['name'] ?? 'N/A' }}</td>
                <td class="text-center">{{ $student['course'] ?? 'N/A' }}</td>
                <td class="text-center">{{ $student['year_level'] ?? 'N/A' }}</td>
                <td class="text-right">₱{{ number_format($student['amount'] ?? 0, 2) }}</td>
                <td class="text-center">
                    @if(($student['status'] ?? '') == 'Paid')
                        <span class="badge-paid">Paid</span>
                    @elseif(($student['status'] ?? '') == 'Pending')
                        <span class="badge-pending">Pending</span>
                    @else
                        <span class="badge-unpaid">Not Paid</span>
                    @endif
                </td>
                <td class="text-center">{{ $student['paid_at'] ?? '—' }}</td>
                <td class="text-center">{{ $student['receipt_number'] ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No students found for this event.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="signature">
    <div class="signature-box">
        <div class="signature-line"></div>
        <div>{{ $generated_by ?? '_____________________' }}</div>
        <div>Treasurer / Finance Officer</div>
    </div>
    <div class="signature-box">
        <div class="signature-line"></div>
        <div>_____________________</div>
        <div>Adviser</div>
    </div>
    <div class="signature-box">
        <div class="signature-line"></div>
        <div>_____________________</div>
        <div>President</div>
    </div>
</div>

<div class="footer">
    This is a system-generated report from CSUCC EMS. For official use only.<br>
    Generated on {{ $report_date ?? date('F d, Y') }} | Report ID: COL-{{ date('Ymd') }}-{{ $event->id ?? '000' }}
</div>

</body>
</html>