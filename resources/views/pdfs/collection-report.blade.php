<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection Report - {{ $event->event_name }}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
            margin-top: 3.5cm;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Segoe UI', 'Inter', system-ui, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #1f2937;
            margin: 0;
            padding: 0;
            background: white;
        }
        
        /* Fixed header that repeats on every page */
        .fixed-header {
            position: fixed;
            top: -2.5cm;
            left: 0;
            right: 0;
            height: 2.8cm;
            text-align: center;
            background: white;
            z-index: 1000;
            padding: 5px 0;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .header-content {
            text-align: center;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .header-image {
            width: auto;
            max-width: 100%;
            height: auto;
            max-height: 2.2cm;
            object-fit: contain;
            margin: 0 auto;
            display: block;
        }
        
        /* Main content wrapper with top padding to avoid header overlap */
        .main-content {
            margin-top: 1.2cm;
            padding-top: 0;
        }
        
        /* Page break with proper spacing for new pages */
        .page-break {
            page-break-before: always;
            margin-top: 1.2cm;
        }
        
        /* Table header repeat on every page */
        .data-table thead {
            display: table-header-group;
        }
        
        .data-table tr {
            page-break-inside: avoid;
        }
        
        /* Professional table styling */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .summary-table th,
        .summary-table td {
            border: 1px solid #d1d5db;
            padding: 10px 12px;
        }
        
        .summary-table th {
            background-color: #1e4620;
            color: white;
            font-weight: 600;
            font-size: 11px;
            text-align: center;
        }
        
        .summary-table td {
            background-color: white;
            font-size: 10px;
        }
        
        /* Data table for students */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
            font-size: 9px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #d1d5db;
            padding: 8px 10px;
        }
        
        .data-table th {
            background-color: #2d6a4f;
            color: white;
            font-weight: 600;
            text-align: center;
        }
        
        .data-table td {
            vertical-align: top;
        }
        
        /* Section header */
        .section-header {
            background-color: #e5e7eb;
            padding: 8px 12px;
            border-left: 4px solid #1e4620;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 12px;
        }
        
        /* Badge styles */
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
        
        .receipt-number {
            font-family: monospace;
            font-size: 8px;
        }
        
        /* Info row styling */
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
            color: #1e4620;
        }
        
        /* Stats row - compact */
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
            color: #059669;
        }
        
        .stat-number-pending {
            color: #d97706;
        }
        
        .stat-number-unpaid {
            color: #dc2626;
        }
        
        .stat-number-rate {
            color: #1e4620;
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
        
        /* Progress bar */
        .progress-container {
            width: 100%;
            background-color: #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
            height: 6px;
            margin-top: 6px;
        }
        
        .progress-bar {
            background-color: #1e4620;
            height: 6px;
            border-radius: 20px;
        }
        
        /* Note box */
        .note-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .success-box {
            background-color: #d1fae5;
            border-left: 4px solid #059669;
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
        
        /* Table footer */
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
        
        /* Signature section */
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 180px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 35px;
            padding-top: 5px;
            width: 160px;
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
            margin-top: 30px;
            text-align: center;
            font-size: 7px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
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
            <img src="data:image/png;base64,{{ $header_image }}" class="header-image" alt="CSUCC Logo">
        @else
            <div style="text-align: center;">
                <div style="font-size: 14px; font-weight: bold; color: #1e4620;">CARAGA STATE UNIVERSITY</div>
                <div style="font-size: 9px; color: #6b7280;">Cabadbaran Campus</div>
            </div>
        @endif
    </div>
</div>

<!-- MAIN CONTENT - Lowered to avoid header overlap -->
<div class="main-content">
    <!-- PAGE 1: SUMMARY SECTION -->
    <div>
        <!-- Report Header -->
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="font-size: 20px; font-weight: bold; color: #1f2937; margin-bottom: 6px;">COLLECTION REPORT</h1>
            <h2 style="font-size: 14px; font-weight: bold; color: #1e4620; margin-bottom: 6px;">{{ $event->event_name }}</h2>
            <p style="font-size: 9px; color: #6b7280;">Generated on: {{ $report_date }} | By: {{ $generated_by }} | Organization: {{ $org_name }}</p>
        </div>

        <!-- Event Information Row -->
        <div class="info-row">
            <div class="info-group">
                <div class="info-item">
                    <div class="info-label">Event Date</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($event->event_date_start)->format('F d, Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Event Fee</div>
                    <div class="info-value info-value-highlight">₱{{ number_format($event->event_fee, 2) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Total Students</div>
                    <div class="info-value">{{ number_format($summary['total_students']) }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Collection Rate</div>
                <div class="info-value info-value-highlight">{{ $summary['collection_rate'] }}%</div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: {{ $summary['collection_rate'] }}%;"></div>
                </div>
            </div>
        </div>

        <!-- Compact Stats Row -->
        <div class="stats-row">
            <div class="stat-compact">
                <div class="stat-number stat-number-paid">{{ $summary['paid_students'] }}</div>
                <div class="stat-label-compact">Fully Paid</div>
                <div class="stat-label-compact" style="font-size: 7px;">{{ number_format(($summary['paid_students'] / max($summary['total_students'], 1)) * 100, 1) }}%</div>
            </div>
            <div class="divider"></div>
            <div class="stat-compact">
                <div class="stat-number stat-number-pending">{{ $summary['pending_students'] }}</div>
                <div class="stat-label-compact">Partial Payment</div>
                <div class="stat-label-compact" style="font-size: 7px;">{{ number_format(($summary['pending_students'] / max($summary['total_students'], 1)) * 100, 1) }}%</div>
            </div>
            <div class="divider"></div>
            <div class="stat-compact">
                <div class="stat-number stat-number-unpaid">{{ $summary['not_paid_students'] }}</div>
                <div class="stat-label-compact">Not Paid</div>
                <div class="stat-label-compact" style="font-size: 7px;">{{ number_format(($summary['not_paid_students'] / max($summary['total_students'], 1)) * 100, 1) }}%</div>
            </div>
            <div class="divider"></div>
            <div class="stat-compact">
                <div class="stat-number stat-number-rate">₱{{ number_format($summary['total_collected'], 2) }}</div>
                <div class="stat-label-compact">Total Collected</div>
                <div class="stat-label-compact" style="font-size: 7px;">of ₱{{ number_format($summary['expected_total'], 2) }}</div>
            </div>
        </div>

        <!-- SUMMARY TABLE -->
        <div class="section-header">📊 COLLECTION SUMMARY</div>
        <table class="summary-table">
            <thead>
                <tr>
                    <th style="width: 40%">Collection Metrics</th>
                    <th style="width: 20%">Count</th>
                    <th style="width: 20%">Amount (₱)</th>
                    <th style="width: 20%">Percentage</th>
                </thead>
            <tbody>
                <tr>
                    <td class="font-bold">Total Students Assigned</td>
                    <td class="text-center font-bold">{{ $summary['total_students'] }}</td>
                    <td class="text-right">—</td>
                    <td class="text-center">100%</td>
                </tr>
                 <tr>
                    <td>✓ Fully Paid Students</td>
                    <td class="text-center">{{ $summary['paid_students'] }}</td>
                    <td class="text-right" style="color: #059669; font-weight: bold;">₱{{ number_format($summary['total_collected'], 2) }}</td>
                    <td class="text-center">{{ $summary['collection_rate'] }}%</td>
                 </tr>
                 <tr>
                    <td>⏳ Pending Students (Partial Payment)</td>
                    <td class="text-center">{{ $summary['pending_students'] }}</td>
                    <td class="text-right">—</td>
                    <td class="text-center">{{ $summary['total_students'] > 0 ? number_format(($summary['pending_students'] / $summary['total_students']) * 100, 1) : 0 }}%</td>
                 </tr>
                 <tr>
                    <td>❌ Unpaid Students (No Payment)</td>
                    <td class="text-center">{{ $summary['not_paid_students'] }}</td>
                    <td class="text-right">—</td>
                    <td class="text-center">{{ $summary['total_students'] > 0 ? number_format(($summary['not_paid_students'] / $summary['total_students']) * 100, 1) : 0 }}%</td>
                 </tr>
                <tr style="background-color: #f0fdf4;">
                    <td class="font-bold">Expected Total Collection</td>
                    <td class="text-center">—</td>
                    <td class="text-right font-bold">₱{{ number_format($summary['expected_total'], 2) }}</td>
                    <td class="text-center">100%</td>
                 </tr>
                <tr style="background-color: #e8f5e9;">
                    <td class="font-bold">Actual Total Collected</td>
                    <td class="text-center">—</td>
                    <td class="text-right font-bold" style="color: #059669;">₱{{ number_format($summary['total_collected'], 2) }}</td>
                    <td class="text-center font-bold">{{ $summary['collection_rate'] }}%</td>
                 </tr>
                <tr style="background-color: #fff3e0;">
                    <td class="font-bold">Outstanding Balance (To be Collected)</td>
                    <td class="text-center">{{ $summary['pending_students'] + $summary['not_paid_students'] }}</td>
                    <td class="text-right font-bold" style="color: #d97706;">₱{{ number_format($summary['expected_total'] - $summary['total_collected'], 2) }}</td>
                    <td class="text-center">{{ $summary['expected_total'] > 0 ? number_format((($summary['expected_total'] - $summary['total_collected']) / $summary['expected_total']) * 100, 1) : 0 }}%</td>
                 </tr>
            </tbody>
        </table>

        <!-- Note Box -->
        @if(($summary['pending_students'] + $summary['not_paid_students']) > 0)
        <div class="note-box">
            <p class="note-text"><strong>📌 Note:</strong> {{ $summary['pending_students'] + $summary['not_paid_students'] }} student(s) have outstanding balances totaling <strong>₱{{ number_format($summary['expected_total'] - $summary['total_collected'], 2) }}</strong>. Please see detailed student list on the next page.</p>
        </div>
        @else
        <div class="success-box">
            <p class="success-text"><strong>✅ Fully Collected!</strong> All {{ $summary['total_students'] }} assigned students have successfully paid their event fees. Total collected: <strong>₱{{ number_format($summary['total_collected'], 2) }}</strong></p>
        </div>
        @endif
    </div>

    <!-- PAGE 2: DETAILED STUDENT PAYMENT LIST -->
    <div class="page-break">
        <div class="section-header">👥 DETAILED STUDENT PAYMENT LIST</div>
        
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                     <tr>
                        <th style="width: 12%">Student ID</th>
                        <th style="width: 28%">Student Name</th>
                        <th style="width: 12%">Course</th>
                        <th style="width: 8%">Year</th>
                        <th style="width: 12%">Amount (₱)</th>
                        <th style="width: 12%">Status</th>
                        <th style="width: 12%">Payment Date</th>
                        <th style="width: 8%">Receipt No.</th>
                     </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    <tr style="{{ $index % 2 == 0 ? 'background-color: #f9fafb;' : '' }}">
                        <td class="text-center" style="font-family: monospace;">{{ $student['student_id'] }}</td>
                        <td class="text-left">{{ $student['name'] }}</td>
                        <td class="text-center">{{ $student['course'] }}</td>
                        <td class="text-center">{{ $student['year_level'] }}</td>
                        <td class="text-right">₱{{ number_format($student['amount'], 2) }}</td>
                        <td class="text-center">
                            @if($student['status'] == 'Paid')
                                <span class="badge badge-paid">Paid</span>
                            @elseif($student['status'] == 'Pending')
                                <span class="badge badge-pending">Pending</span>
                            @else
                                <span class="badge badge-unpaid">Not Paid</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $student['paid_at'] ?? '—' }}</td>
                        <td class="text-center receipt-number">
                            @if(isset($student['receipt_number']) && !empty($student['receipt_number']))
                                {{ $student['receipt_number'] }}
                            @else
                                —
                            @endif
                        </td>
                     </tr>
                    @empty
                     <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            No students found for this event.
                        </td>
                     </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer Summary -->
        <div class="table-footer">
            <div class="footer-stats">
                <div>👥 Total Students: <strong>{{ $summary['total_students'] }}</strong></div>
                <div>✅ Fully Paid: <strong>{{ $summary['paid_students'] }}</strong></div>
                <div>⏳ Pending: <strong>{{ $summary['pending_students'] }}</strong></div>
                <div>❌ Unpaid: <strong>{{ $summary['not_paid_students'] }}</strong></div>
                <div>💰 Total Collected: <strong style="color: #059669;">₱{{ number_format($summary['total_collected'], 2) }}</strong></div>
            </div>
        </div>

        @if(($summary['pending_students'] + $summary['not_paid_students']) > 0)
        <div class="note-box" style="margin-top: 20px;">
            <p class="note-text"><strong>⚠️ Collection Reminder:</strong> {{ $summary['pending_students'] + $summary['not_paid_students'] }} student(s) have outstanding balances totaling <strong>₱{{ number_format($summary['expected_total'] - $summary['total_collected'], 2) }}</strong>. Please follow up with the students listed above.</p>
        </div>
        @endif
    </div>

    <!-- SIGNATURE SECTION -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-name">{{ $generated_by }}</div>
            <div class="signature-title">Treasurer / Finance Officer</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-name">_____________________</div>
            <div class="signature-title">Adviser</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-name">_____________________</div>
            <div class="signature-title">President</div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        This is a system-generated report from CSUCC EMS. For official use only.<br>
        Generated on {{ $report_date }} | Report ID: COL-{{ date('Ymd') }}-{{ $event->id ?? '000' }}
    </div>
</div>

</body>
</html>