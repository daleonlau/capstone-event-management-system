<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Collection Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>COLLECTION REPORT</h1>
        <h2>{{ $event->event_name ?? 'N/A' }}</h2>
        <p>Generated: {{ $report_date ?? date('F d, Y') }}</p>
    </div>

    <h3>Summary</h3>
    <table>
        <tr><th>Total Students</th><td>{{ $summary['total_students'] ?? 0 }}</td></tr>
        <tr><th>Fully Paid</th><td>{{ $summary['paid_students'] ?? 0 }}</td></tr>
        <tr><th>Pending</th><td>{{ $summary['pending_students'] ?? 0 }}</td></tr>
        <tr><th>Not Paid</th><td>{{ $summary['not_paid_students'] ?? 0 }}</td></tr>
        <tr><th>Total Collected</th><td>₱{{ number_format($summary['total_collected'] ?? 0, 2) }}</td></tr>
        <tr><th>Collection Rate</th><td>{{ $summary['collection_rate'] ?? 0 }}%</td></tr>
    </table>

    <h3>Student List</h3>
    <table>
        <tr><th>Student ID</th><th>Name</th><th>Program</th><th>Year</th><th>Amount</th><th>Status</th></tr>
        @foreach($students ?? [] as $student)
        <tr>
            <td>{{ $student['student_id'] ?? 'N/A' }}</td>
            <td>{{ $student['name'] ?? 'N/A' }}</td>
            <td>{{ $student['course'] ?? 'N/A' }}</td>
            <td>{{ $student['year_level'] ?? 'N/A' }}</td>
            <td>₱{{ number_format($student['amount'] ?? 0, 2) }}</td>
            <td>{{ $student['status'] ?? 'Not Paid' }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>