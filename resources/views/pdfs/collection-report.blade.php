<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection Report - {{ $event->event_name }}</title>
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
        .event-name {
            font-size: 16px;
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
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
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
            font-size: 16px;
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
        .status-paid {
            color: #059669;
            font-weight: bold;
        }
        .status-pending {
            color: #d97706;
            font-weight: bold;
        }
        .status-not-paid {
            color: #dc2626;
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
            margin-top: 40px;
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
        .filters {
            background-color: #f3f4f6;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
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
        <div class="report-title">COLLECTION REPORT</div>
        <div class="event-name">{{ $event->event_name }}</div>
        <div class="report-meta">
            Generated on: {{ $report_date }} | By: {{ $generated_by }}
        </div>
    </div>

    @if(!empty($filters['date_from']) || !empty($filters['date_to']) || !empty($filters['status']))
    <div class="filters">
        <strong>Filters Applied:</strong>
        @if($filters['date_from']) Date From: {{ \Carbon\Carbon::parse($filters['date_from'])->format('M d, Y') }} @endif
        @if($filters['date_to']) | Date To: {{ \Carbon\Carbon::parse($filters['date_to'])->format('M d, Y') }} @endif
        @if($filters['status'] && $filters['status'] != 'all') | Status: {{ ucfirst(str_replace('_', ' ', $filters['status'])) }} @endif
    </div>
    @endif

    <div class="summary-section">
        <div class="summary-title">SUMMARY</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Students</div>
                <div class="summary-value">{{ $summary['total_students'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Paid</div>
                <div class="summary-value">{{ $summary['paid_students'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Pending</div>
                <div class="summary-value">{{ $summary['pending_students'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Not Paid</div>
                <div class="summary-value">{{ $summary['not_paid_students'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Event Fee</div>
                <div class="amount-value">₱{{ number_format($event->event_fee, 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Collected</div>
                <div class="amount-value">₱{{ number_format($summary['total_collected'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Expected Total</div>
                <div class="amount-value">₱{{ number_format($summary['expected_total'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Collection Rate</div>
                <div class="amount-value">{{ $summary['collection_rate'] }}%</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Course</th>
                <th>Year Level</th>
                <th>Status</th>
                <th class="text-right">Amount</th>
                <th>Paid At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
            <tr>
                <td>{{ $student['student_id'] }}</td>
                <td>{{ $student['name'] }}</td>
                <td>{{ $student['course'] }}</td>
                <td>{{ $student['year_level'] }}</td>
                <td>
                    <span class="
                        @if($student['status'] == 'Paid') status-paid
                        @elseif($student['status'] == 'Pending') status-pending
                        @else status-not-paid
                        @endif
                    ">
                        {{ $student['status'] }}
                    </span>
                </td>
                <td class="text-right">₱{{ number_format($student['amount'], 2) }}</td>
                <td>{{ $student['paid_at'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">No students found matching the criteria</td>
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