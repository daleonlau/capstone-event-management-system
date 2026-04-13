<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Collection Report - {{ $event->event_name ?? 'N/A' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
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
        .badge-paid { color: green; font-weight: bold; }
        .badge-pending { color: orange; font-weight: bold; }
        .badge-unpaid { color: red; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
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
            <td>Total Students</td>
            <td class="text-center">{{ $summary['total_students'] ?? 0 }}</td>
            <td class="text-right">-</td>
            <td class="text-center">100%</td>
        </tr>
        <tr>
            <td>Fully Paid</td>
            <td class="text-center">{{ $summary['paid_students'] ?? 0 }}</td>
            <td class="text-right">₱{{ number_format($summary['total_collected'] ?? 0, 2) }}</td>
            <td class="text-center">{{ $summary['collection_rate'] ?? 0 }}%</td>
        </tr>
        <tr>
            <td>Pending</td>
            <td class="text-center">{{ $summary['pending_students'] ?? 0 }}</td>
            <td class="text-right">-</td>
            <td class="text-center">{{ $summary['total_students'] > 0 ? round(($summary['pending_students'] / $summary['total_students']) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td>Not Paid</td>
            <td class="text-center">{{ $summary['not_paid_students'] ?? 0 }}</td>
            <td class="text-right">-</td>
            <td class="text-center">{{ $summary['total_students'] > 0 ? round(($summary['not_paid_students'] / $summary['total_students']) * 100, 1) : 0 }}%</td>
        </tr>
        <tr style="background-color: #e8f5e9;">
            <td><strong>Total Collected</strong></td>
            <td class="text-center">-</td>
            <td class="text-right"><strong>₱{{ number_format($summary['total_collected'] ?? 0, 2) }}</strong></td>
            <td class="text-center"><strong>{{ $summary['collection_rate'] ?? 0 }}%</strong></td>
        </tr>
    </table>

    <h3>STUDENT PAYMENT LIST</h3>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Program</th>
                <th>Year</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment Date</th>
                <th>Receipt No.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students ?? [] as $student)
            <tr>
                <td>{{ $student['student_id'] ?? 'N/A' }}</td>
                <td>{{ $student['name'] ?? 'N/A' }}</td>
                <td>{{ $student['course'] ?? 'N/A' }}</td>
                <td>{{ $student['year_level'] ?? 'N/A' }}</td>
                <td class="text-right">₱{{ number_format($student['amount'] ?? 0, 2) }}</td>
                <td>
                    @if(($student['status'] ?? '') == 'Paid')
                        <span class="badge-paid">Paid</span>
                    @elseif(($student['status'] ?? '') == 'Pending')
                        <span class="badge-pending">Pending</span>
                    @else
                        <span class="badge-unpaid">Not Paid</span>
                    @endif
                </td>
                <td>{{ $student['paid_at'] ?? '—' }}</td>
                <td>{{ $student['receipt_number'] ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No students found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 50px;">
        <table style="border: none;">
            <tr>
                <td style="text-align: center; border: none;">_____________________</td>
                <td style="text-align: center; border: none;">_____________________</td>
                <td style="text-align: center; border: none;">_____________________</td>
            </tr>
            <tr>
                <td style="text-align: center; border: none;">Treasurer</td>
                <td style="text-align: center; border: none;">Adviser</td>
                <td style="text-align: center; border: none;">President</td>
            </tr>
        </table>
    </div>
</body>
</html>