<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Collection Report - {{ $event->event_name }}</title>
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
        .summary {
            margin: 20px 0;
        }
        .badge-paid {
            color: green;
            font-weight: bold;
        }
        .badge-pending {
            color: orange;
            font-weight: bold;
        }
        .badge-unpaid {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>COLLECTION REPORT</h1>
        <h2>{{ $event->event_name }}</h2>
        <p>Generated on: {{ $report_date }} | By: {{ $generated_by }} | Organization: {{ $org_name }}</p>
        <p>Event Date: {{ date('F d, Y', strtotime($event->event_date_start)) }}</p>
        <p>Event Fee: ₱{{ number_format($event->event_fee, 2) }}</p>
    </div>

    <div class="summary">
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
                <td>{{ $summary['total_students'] }}</td>
                <td>-</td>
                <td>100%</td>
            </tr>
            <tr>
                <td>Fully Paid Students</td>
                <td>{{ $summary['paid_students'] }}</td>
                <td>₱{{ number_format($summary['total_collected'], 2) }}</td>
                <td>{{ $summary['collection_rate'] }}%</td>
            </tr>
            <tr>
                <td>Pending Students</td>
                <td>{{ $summary['pending_students'] }}</td>
                <td>-</td>
                <td>{{ $summary['total_students'] > 0 ? round(($summary['pending_students'] / $summary['total_students']) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Not Paid Students</td>
                <td>{{ $summary['not_paid_students'] }}</td>
                <td>-</td>
                <td>{{ $summary['total_students'] > 0 ? round(($summary['not_paid_students'] / $summary['total_students']) * 100, 1) : 0 }}%</td>
            </tr>
            <tr style="background-color: #e8f5e9;">
                <td><strong>Total Collected</strong></td>
                <td>-</td>
                <td><strong>₱{{ number_format($summary['total_collected'], 2) }}</strong></td>
                <td><strong>{{ $summary['collection_rate'] }}%</strong></td>
            </tr>
        </table>
    </div>

    <div>
        <h3>STUDENT PAYMENT LIST</h3>
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
                @forelse($students as $student)
                <tr>
                    <td>{{ $student['student_id'] }}</td>
                    <td>{{ $student['name'] }}</td>
                    <td>{{ $student['course'] }}</td>
                    <td>{{ $student['year_level'] }}</td>
                    <td>₱{{ number_format($student['amount'], 2) }}</td>
                    <td>
                        @if($student['status'] == 'Paid')
                            <span class="badge-paid">Paid</span>
                        @elseif($student['status'] == 'Pending')
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
                    <td colspan="8" style="text-align: center;">No students found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 40px;">
        <table style="width: 100%; border: none;">
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