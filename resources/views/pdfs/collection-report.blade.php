<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Collection Report - {{ $event->event_name ?? 'N/A' }}</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
            margin-top: 3.8cm;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .fixed-header {
            position: fixed;
            top: -2.5cm;
            left: 0;
            right: 0;
            height: 2.2cm;
            text-align: center;
            background: white;
            z-index: 1000;
            padding: 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .header-content {
            text-align: center;
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        .header-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            margin: 0;
            padding: 0;
            display: block;
        }
        
        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
            text-decoration: underline;
            text-align: center;
        }
        
        .page-break {
            page-break-before: always;
            margin-top: 0;
        }
        
        .section-header {
            font-size: 13px;
            font-weight: bold;
            margin: 15px 0 15px 0;
            background-color: #e5e7eb;
            padding: 8px;
            border-left: 4px solid #1a472a;
            clear: both;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 12px 16px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }
        
        .info-group {
            display: flex;
            gap: 32px;
        }
        
        .info-item {
            text-align: center;
        }
        
        .info-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        
        .info-value {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .info-value-highlight {
            color: #1a472a;
        }
        
        .stats-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 12px 16px;
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }
        
        .stat-compact {
            text-align: center;
            flex: 1;
        }
        
        .stat-number {
            font-size: 20px;
            font-weight: bold;
        }
        
        .stat-number-paid {
            color: #27ae60;
        }
        
        .stat-number-pending {
            color: #f39c12;
        }
        
        .stat-number-unpaid {
            color: #e74c3c;
        }
        
        .stat-number-rate {
            color: #1a472a;
        }
        
        .stat-label-compact {
            font-size: 8px;
            color: #6b7280;
            margin-top: 4px;
        }
        
        .divider {
            width: 1px;
            background-color: #e5e7eb;
            margin: 0 8px;
        }
        
        .progress-container {
            width: 100%;
            background-color: #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
            height: 6px;
            margin-top: 6px;
        }
        
        .progress-bar {
            background-color: #1a472a;
            height: 6px;
            border-radius: 20px;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .summary-table th,
        .summary-table td {
            border: 1px solid #000;
            padding: 10px 12px;
        }
        
        .summary-table th {
            background-color: #1a472a;
            color: white;
            font-weight: 600;
            font-size: 11px;
            text-align: center;
        }
        
        .summary-table td {
            background-color: white;
            font-size: 10px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 9px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        
        .data-table th {
            background-color: #2d6a4f;
            color: white;
            text-align: center;
            font-weight: bold;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 8px;
            font-weight: 600;
        }
        
        .badge-paid {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-pending {
            background: #fed7aa;
            color: #9b2c1d;
        }
        
        .badge-unpaid {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .note-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .success-box {
            background-color: #d1fae5;
            border-left: 4px solid #27ae60;
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .note-text {
            font-size: 9px;
            color: #92400e;
        }
        
        .success-text {
            font-size: 9px;
            color: #065f46;
        }
        
        .table-footer {
            margin-top: 16px;
            padding: 10px 12px;
            background-color: #f9fafb;
            border-radius: 6px;
            font-size: 9px;
        }
        
        .footer-stats {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
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
            border-top: 1px solid #000;
            margin-top: 30px;
            padding-top: 5px;
            width: 180px;
        }
        
        .signature-name {
            font-weight: bold;
            margin-top: 5px;
            font-size: 9px;
        }
        
        .signature-title {
            font-size: 8px;
            color: #666;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 7px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

<!-- FIXED HEADER -->
<div class="fixed-header">
    <div class="header-content">
        @if(isset($header_image) && $header_image)
            <img src="data:image/png;base64,{{ $header_image }}" class="header-image" alt="Logo">
        @endif
    </div>
</div>

<div class="content">
    <div>
        <div style="text-align: center; margin-bottom: 20px;">
            <div class="report-title">COLLECTION REPORT</div>
            <h2 style="font-size: 14px; font-weight: bold; color: #1a472a; margin-bottom: 6px;">{{ $event->event_name ?? 'N/A' }}</h2>
            <p style="font-size: 9px; color: #6b7280;">Generated on: {{ $report_date ?? date('F d, Y') }} | By: {{ $generated_by ?? 'System' }} | Organization: {{ $org_name ?? 'N/A' }}</p>
        </div>

        <div class="info-row">
            <div class="info-group">
                <div class="info-item">
                    <div class="info-label">Event Date</div>
                    <div class="info-value">{{ date('F d, Y', strtotime($event->event_date_start)) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Event Fee</div>
                    <div class="info-value info-value-highlight">₱{{ number_format($event->event_fee ?? 0, 2) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Total Students</div>
                    <div class="info-value">{{ number_format($summary['total_students'] ?? 0) }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Collection Rate</div>
                <div class="info-value info-value-highlight">{{ $summary['collection_rate'] ?? 0 }}%</div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: {{ $summary['collection_rate'] ?? 0 }}%;"></div>
                </div>
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-compact">
                <div class="stat-number stat-number-paid">{{ $summary['paid_students'] ?? 0 }}</div>
                <div class="stat-label-compact">Fully Paid</div>
            </div>
            <div class="divider"></div>
            <div class="stat-compact">
                <div class="stat-number stat-number-pending">{{ $summary['pending_students'] ?? 0 }}</div>
                <div class="stat-label-compact">Pending</div>
            </div>
            <div class="divider"></div>
            <div class="stat-compact">
                <div class="stat-number stat-number-unpaid">{{ $summary['not_paid_students'] ?? 0 }}</div>
                <div class="stat-label-compact">Not Paid</div>
            </div>
            <div class="divider"></div>
            <div class="stat-compact">
                <div class="stat-number stat-number-rate">₱{{ number_format($summary['total_collected'] ?? 0, 2) }}</div>
                <div class="stat-label-compact">Total Collected</div>
            </div>
        </div>

        <div class="section-header">📊 COLLECTION SUMMARY</div>
        <table class="summary-table">
            <thead><tr><th>Collection Metrics</th><th>Count</th><th>Amount (₱)</th><th>Percentage</th></tr></thead>
            <tbody>
                <tr><td class="font-bold">Total Students Assigned</td><td class="text-center font-bold">{{ $summary['total_students'] ?? 0 }}</td><td class="text-right">—</td><td class="text-center">100%</td></tr>
                <tr><td>✓ Fully Paid Students</td><td class="text-center">{{ $summary['paid_students'] ?? 0 }}</td><td class="text-right" style="color: #27ae60; font-weight: bold;">₱{{ number_format($summary['total_collected'] ?? 0, 2) }}</td><td class="text-center">{{ $summary['collection_rate'] ?? 0 }}%</td></tr>
                <tr><td>⏳ Pending Students</td><td class="text-center">{{ $summary['pending_students'] ?? 0 }}</td><td class="text-right">—</td><td class="text-center">-</td></tr>
                <tr><td>❌ Not Paid Students</td><td class="text-center">{{ $summary['not_paid_students'] ?? 0 }}</td><td class="text-right">—</td><td class="text-center">-</td></tr>
                <tr style="background-color: #e8f5e9;"><td class="font-bold">Total Collected</td><td class="text-center">—</td><td class="text-right font-bold">₱{{ number_format($summary['total_collected'] ?? 0, 2) }}</td><td class="text-center font-bold">{{ $summary['collection_rate'] ?? 0 }}%</td></tr>
            </tbody>
        </table>
    </div>

    <!-- STUDENT DETAILS TABLE -->
    <div class="section-header">👥 DETAILED STUDENT PAYMENT LIST</div>
    
    <table class="data-table">
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
            @forelse($students as $index => $student)
            <tr style="{{ $index % 2 == 0 ? 'background-color: #f9fafb;' : '' }}">
                <td class="text-center">{{ $student['student_id'] ?? 'N/A' }}</td>
                <td>{{ $student['name'] ?? 'N/A' }}</td>
                <td class="text-center">{{ $student['course'] ?? 'N/A' }}</td>
                <td class="text-center">{{ $student['year_level'] ?? 'N/A' }}</td>
                <td class="text-right">₱{{ number_format($student['amount'] ?? 0, 2) }}</td>
                <td class="text-center">
                    @if(($student['status'] ?? '') == 'Paid')
                        <span class="badge badge-paid">Paid</span>
                    @elseif(($student['status'] ?? '') == 'Pending')
                        <span class="badge badge-pending">Pending</span>
                    @else
                        <span class="badge badge-unpaid">Not Paid</span>
                    @endif
                </td>
                <td class="text-center">{{ $student['paid_at'] ?? '—' }}</td>
                <td class="text-center">{{ $student['receipt_number'] ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">No students found</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="table-footer">
        <div class="footer-stats">
            <div>👥 Total: <strong>{{ $summary['total_students'] ?? 0 }}</strong></div>
            <div>✅ Paid: <strong>{{ $summary['paid_students'] ?? 0 }}</strong></div>
            <div>⏳ Pending: <strong>{{ $summary['pending_students'] ?? 0 }}</strong></div>
            <div>❌ Unpaid: <strong>{{ $summary['not_paid_students'] ?? 0 }}</strong></div>
            <div>💰 Collected: <strong>₱{{ number_format($summary['total_collected'] ?? 0, 2) }}</strong></div>
        </div>
    </div>

    @if((($summary['pending_students'] ?? 0) + ($summary['not_paid_students'] ?? 0)) > 0)
    <div class="note-box"><p class="note-text"><strong>⚠️ Reminder:</strong> {{ ($summary['pending_students'] ?? 0) + ($summary['not_paid_students'] ?? 0) }} student(s) have outstanding balances.</p></div>
    @endif

    <div class="signature-section">
        <div class="signature-box"><div class="signature-line"></div><div class="signature-name">{{ $generated_by ?? '_____________________' }}</div><div class="signature-title">Treasurer</div></div>
        <div class="signature-box"><div class="signature-line"></div><div class="signature-name">_____________________</div><div class="signature-title">Adviser</div></div>
        <div class="signature-box"><div class="signature-line"></div><div class="signature-name">_____________________</div><div class="signature-title">President</div></div>
    </div>

    <div class="footer">Generated on {{ $report_date ?? date('F d, Y') }} | Report ID: COL-{{ date('Ymd') }}-{{ $event->id ?? '000' }}</div>
</div>
</body>
</html>