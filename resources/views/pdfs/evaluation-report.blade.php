<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Evaluation Report - {{ $event->event_name }}</title>
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
        
        /* Fixed header that repeats on every page - IMAGE ONLY - FULL WIDTH */
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
        
        /* Page breaks with proper spacing */
        .page-break {
            page-break-before: always;
            margin-top: 0;
        }
        
        /* Ensure all sections start with proper top spacing */
        .section-header {
            font-size: 13px;
            font-weight: bold;
            margin: 15px 0 15px 0;
            background-color: #e5e7eb;
            padding: 8px;
            border-left: 4px solid #1a472a;
            clear: both;
        }
        
        /* First section after page break needs extra spacing */
        .page-break + .section-header,
        .page-break + .report-title,
        .page-break + div > .section-header {
            margin-top: 0;
        }
        
        /* First element on new page gets top padding */
        .content > :first-child {
            margin-top: 0;
        }
        
        /* Memorandum Styles */
        .memorandum {
            margin: 0;
            padding: 0;
        }
        
        .memo-date {
            text-align: right;
            margin: 15px 0 20px 0;
            font-size: 11px;
        }
        
        .memo-to, .memo-from, .memo-subject {
            margin: 12px 0;
            line-height: 1.6;
            font-size: 11px;
        }
        
        .memo-body {
            text-align: justify;
            margin: 15px 0;
            line-height: 1.6;
            font-size: 11px;
        }
        
        .memo-list {
            margin: 10px 0 10px 30px;
            padding-left: 0;
        }
        
        .memo-list li {
            margin: 5px 0;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 250px;
            margin-top: 30px;
            padding-top: 5px;
        }
        
        /* Event Details */
        .event-details {
            margin: 12px 0;
            line-height: 1.8;
            font-size: 10px;
        }
        
        .event-details p {
            margin: 5px 0;
        }
        
        /* Introduction Text */
        .intro-text {
            margin: 12px 0;
            text-align: justify;
            line-height: 1.6;
            font-size: 10px;
        }
        
        /* Likert Table */
        .likert-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 9px;
        }
        
        .likert-table th,
        .likert-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        
        .likert-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .subsection-header {
            font-size: 11px;
            font-weight: bold;
            margin: 12px 0 8px 0;
            background-color: #f9fafb;
            padding: 6px 10px;
            border-bottom: 1px solid #ddd;
        }
        
        /* Data Tables */
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
            background-color: #f0f0f0;
            text-align: left;
            font-weight: bold;
        }
        
        .data-table .text-center {
            text-align: center;
        }
        
        /* Profile Grid */
        .profile-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 15px 0;
        }
        
        .profile-card {
            flex: 1;
            min-width: 280px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
        }
        
        .profile-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #1a472a;
            font-size: 11px;
        }
        
        /* Bar Chart Styles */
        .bar-chart-item {
            margin: 8px 0;
        }
        
        .bar-chart-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 9px;
        }
        
        .bar-chart-bar-container {
            width: 100%;
            height: 18px;
            background-color: #f0f0f0;
            border-radius: 9px;
            overflow: hidden;
        }
        
        .bar-chart-bar {
            height: 100%;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 6px;
            color: white;
            font-size: 8px;
            font-weight: bold;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .stat-card {
            flex: 1;
            min-width: 80px;
            text-align: center;
            padding: 8px;
            background-color: #f9fafb;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }
        
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #1a472a;
        }
        
        .stat-label {
            font-size: 9px;
            color: #666;
        }
        
        /* Sentiment Gauge - Fixed Display with ALL segments visible */
        .sentiment-gauge {
            margin: 15px 0;
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        .gauge-container {
            display: table;
            width: 100%;
            height: 32px;
            border-radius: 16px;
            overflow: hidden;
            margin: 10px 0;
            background-color: #e0e0e0;
            table-layout: fixed;
        }
        
        .gauge-positive {
            display: table-cell;
            background-color: #27ae60;
            color: white;
            text-align: center;
            line-height: 32px;
            font-size: 10px;
            font-weight: bold;
            vertical-align: middle;
        }
        
        .gauge-neutral {
            display: table-cell;
            background-color: #f39c12;
            color: white;
            text-align: center;
            line-height: 32px;
            font-size: 10px;
            font-weight: bold;
            vertical-align: middle;
        }
        
        .gauge-negative {
            display: table-cell;
            background-color: #e74c3c;
            color: white;
            text-align: center;
            line-height: 32px;
            font-size: 10px;
            font-weight: bold;
            vertical-align: middle;
        }
        
        .gauge-legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 10px;
        }
        
        .legend-bullet {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 2px;
            margin-right: 5px;
            vertical-align: middle;
        }
        
        /* Comments Section - Allow natural page flow */
        .comment-group {
            margin-bottom: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            overflow: visible;
            page-break-inside: avoid;
        }
        
        .comment-group-title {
            font-weight: bold;
            padding: 8px 12px;
            margin: 0;
            font-size: 10px;
        }
        
        .comment-positive {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .comment-negative {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .comment-neutral {
            background-color: #fff3e0;
            color: #ef6c00;
        }
        
        .comments-list {
            padding: 8px 12px;
            max-height: none;
            overflow: visible;
        }
        
        .comment-item {
            padding: 5px 0;
            border-bottom: 1px solid #e0e0e0;
            font-size: 8px;
            line-height: 1.3;
            page-break-inside: avoid;
        }
        
        .comment-number {
            font-weight: bold;
            margin-right: 6px;
            color: #1a472a;
            display: inline-block;
            min-width: 30px;
        }
        
        /* Recommendations */
        .rec-card {
            margin: 8px 0;
            padding: 8px;
            border-left: 3px solid;
            background-color: #f9fafb;
            font-size: 8px;
        }
        
        .rec-priority-high {
            border-left-color: #e74c3c;
            background-color: #fef2f2;
        }
        
        .rec-priority-medium {
            border-left-color: #f39c12;
            background-color: #fef9e6;
        }
        
        .rec-priority-low {
            border-left-color: #27ae60;
            background-color: #e8f5e9;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 30px;
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
        
        .text-center {
            text-align: center;
        }
        
        /* Rating Colors */
        .rating-excellent { color: #27ae60; font-weight: bold; }
        .rating-good { color: #2ecc71; font-weight: bold; }
        .rating-average { color: #f39c12; font-weight: bold; }
        .rating-poor { color: #e67e22; font-weight: bold; }
        .rating-very-poor { color: #e74c3c; font-weight: bold; }
        
        .theme-tag {
            display: inline-block;
            background: #e3f2fd;
            padding: 3px 8px;
            border-radius: 15px;
            margin: 3px;
            font-size: 8px;
        }
        
        /* Ensure content doesn't break badly */
        table {
            page-break-inside: avoid;
        }
        
        .profile-card {
            page-break-inside: avoid;
        }
        
        /* Content wrapper to account for fixed header */
        .content {
            margin-top: 0;
            padding-top: 0;
        }
        
        /* Clear floats */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        
        /* Ensure proper spacing after page breaks */
        .page-break + * {
            margin-top: 0;
        }
        
        /* Add top padding to first element after page break for spacing */
        .page-break + .report-title,
        .page-break + .section-header,
        .page-break + div > .section-header {
            margin-top: 0;
            padding-top: 0;
        }
        
        /* Allow individual comment items to break if necessary */
        .comment-item {
            page-break-inside: avoid;
        }
        
        /* Extra spacing for content after page break */
        .page-break + div:not(.fixed-header) {
            margin-top: 0;
        }
    </style>
</head>
<body>

<!-- FIXED HEADER - REPEATS ON EVERY PAGE - FULL WIDTH IMAGE -->
<div class="fixed-header">
    <div class="header-content">
        @if(isset($header_image) && $header_image)
            <img src="data:image/png;base64,{{ $header_image }}" class="header-image" alt="CSUCC Logo">
        @endif
    </div>
</div>

<div class="content">
    <!-- PAGE 1: MEMORANDUM LETTER -->
    <div class="memorandum">
        <div class="memo-date">
            {{ $report_date }}
        </div>

        <div class="memo-to">
            <strong>OPQMS Memorandum Order<br>No. 78, s. 2025</strong>
        </div>

        <div class="memo-to">
            <strong>TO :</strong> MS. QUEENCHY MICABALO<br>Adviser, STMS
        </div>

        <div class="memo-from">
            <strong>FROM :</strong> THE HEAD<br>Office of the Planning and Quality Management Services
        </div>

        <div class="memo-subject">
            <strong>Subject:</strong> EVENT EVALUATION RESULT OF THE {{ strtoupper($event->event_name) }} ON {{ $event_date }}
        </div>

        <div class="memo-body">
            <p>Submitting herewith the results of the event evaluation survey conducted during {{ $event->event_name }} on {{ $event_date }}. The event evaluation survey aimed to gather feedback from participants to assess the effectiveness of the activity and identify areas of improvement.</p>
            
            <p>In the context, the event evaluation survey result covers the following details:</p>
            <ul class="memo-list">
                <li>Profile of the Respondents</li>
                <li>Information Dissemination</li>
                <li>Secretariat</li>
                <li>Facilities</li>
                <li>Design of the Event</li>
                <li>Outcomes of the Event</li>
                <li>Food</li>
                <li>Other Comments and Suggestions</li>
            </ul>
            
            <p>Overall, the results indicate a general mean of <strong>{{ number_format($overall_satisfaction, 2) }}</strong> with a descriptive rating of <strong>{{ $satisfaction_interpretation }}</strong>. While feedback was generally positive, participants provided several useful suggestions and recommendations for future improvements.</p>
            
            <p>For your information and appropriate action.</p>
        </div>

        <div class="memo-signature">
            <div class="signature-line"></div>
            <div class="signature-name">ALMA LIGAYA A. BERMUDEZ, PhD</div>
            <div class="signature-title">Head, OPQMS</div>
        </div>

        <div class="memo-signature" style="margin-top: 20px;">
            <div class="signature-line"></div>
            <div class="signature-name">RONALD A. MONZON, MIT</div>
            <div class="signature-title">Division Chief for Executive Operations</div>
        </div>
    </div>

    <!-- PAGE 2: REPORT CONTENT -->
    <div class="page-break"></div>
    
    <div class="report-title">EVENT EVALUATION REPORT</div>

    <div class="event-details">
        <p><strong>Title of Activity:</strong> {{ $event->event_name }}</p>
        <p><strong>Inclusive Dates:</strong> {{ $event_date }}</p>
        <p><strong>Venue:</strong> {{ $venue }}</p>
        <p><strong>Number of Attendees:</strong> {{ number_format($total_eligible) }}</p>
        <p><strong>Number of Responses:</strong> {{ number_format($total_responses) }}</p>
    </div>

    <div class="intro-text">
        The event evaluation result is a critical component in assessing the success and effectiveness of an event. 
        Whether it's a conference, workshop, seminar, or any other gathering, evaluating its outcomes provides invaluable 
        insights that can inform future planning, decision-making, and conducted for participants of 
        <strong>{{ $event->event_name }}</strong> through feedback collected from the online and in-person survey. 
        The survey instrument used for this purpose is indicated in the subsequent page of the result.
    </div>

    <div class="satisfaction-section">
        <div class="satisfaction-title">Satisfaction Level of the Respondents</div>
        <p>The satisfaction level of the respondents was scored using a 5-point Likert Scale. The simple average or grand mean of the rating was used to get the overall score. The interpretation of the results are as follows:</p>
    </div>

    <table class="likert-table">
        <thead>
            <tr><th>Scale</th><th>Average Rating</th><th>Interpretation</th><th>Verbal Interpretation</th> </>
        </thead>
        <tbody>
            <tr><td class="text-center">5<\/td><td class="text-center">4.50 – 5.00<\/td><td class="text-center">Very Satisfied<\/td><td class="text-center">Outstanding<\/td> \]
            <tr><td class="text-center">4<\/td><td class="text-center">3.50 – 4.49<\/td><td class="text-center">Satisfied<\/td><td class="text-center">Very Satisfactory<\/td> \]
            <tr><td class="text-center">3<\/td><td class="text-center">2.50 – 3.49<\/td><td class="text-center">Neither Satisfied nor Dissatisfied<\/td><td class="text-center">Satisfactory<\/td> \]
            <tr><td class="text-center">2<\/td><td class="text-center">1.50 – 2.49<\/td><td class="text-center">Dissatisfied<\/td><td class="text-center">Poor<\/td> \]
            <tr><td class="text-center">1<\/td><td class="text-center">1.00 – 1.49<\/td><td class="text-center">Very Dissatisfied<\/td><td class="text-center">Very Poor<\/td> \]
        </tbody>
     </table>

    <div class="text-center" style="margin: 15px 0; padding: 12px; background-color: #f0fdf4; border-radius: 8px;">
        <div style="font-size: 24px; font-weight: bold; color: #1a472a;">{{ number_format($overall_satisfaction, 2) }}/5.0</div>
        <div style="font-size: 11px; font-weight: bold;">{{ strtoupper($satisfaction_interpretation) }}</div>
    </div>

    <!-- PART I: Profile of Respondents -->
    <div class="section-header">I. PROFILE OF RESPONDENTS</div>

    <div class="profile-grid">
        <!-- A. Sex - Bar Chart -->
        <div class="profile-card">
            <div class="profile-title">A. Sex</div>
            @php
                $maleCount = $gender_counts['Male'] ?? 0;
                $femaleCount = $gender_counts['Female'] ?? 0;
                $nonbinaryCount = $gender_counts['Nonbinary/Intersex'] ?? 0;
                $preferNotCount = $gender_counts['Prefer not to say'] ?? 0;
                $totalGender = $maleCount + $femaleCount + $nonbinaryCount + $preferNotCount;
            @endphp
            @if($maleCount > 0)
                @php $percent = $totalGender > 0 ? round(($maleCount / $totalGender) * 100, 1) : 0; @endphp
                <div class="bar-chart-item">
                    <div class="bar-chart-label"><span>👨 Male</span><span>{{ $maleCount }} ({{ $percent }}%)</span></div>
                    <div class="bar-chart-bar-container"><div class="bar-chart-bar" style="width: {{ $percent }}%; background: #27ae60;">@if($percent > 15) {{ $percent }}% @endif</div></div>
                </div>
            @endif
            @if($femaleCount > 0)
                @php $percent = $totalGender > 0 ? round(($femaleCount / $totalGender) * 100, 1) : 0; @endphp
                <div class="bar-chart-item">
                    <div class="bar-chart-label"><span>👩 Female</span><span>{{ $femaleCount }} ({{ $percent }}%)</span></div>
                    <div class="bar-chart-bar-container"><div class="bar-chart-bar" style="width: {{ $percent }}%; background: #f39c12;">@if($percent > 15) {{ $percent }}% @endif</div></div>
                </div>
            @endif
            @if($nonbinaryCount > 0)
                @php $percent = $totalGender > 0 ? round(($nonbinaryCount / $totalGender) * 100, 1) : 0; @endphp
                <div class="bar-chart-item">
                    <div class="bar-chart-label"><span>⚧ Nonbinary/Intersex</span><span>{{ $nonbinaryCount }} ({{ $percent }}%)</span></div>
                    <div class="bar-chart-bar-container"><div class="bar-chart-bar" style="width: {{ $percent }}%; background: #3498db;">@if($percent > 15) {{ $percent }}% @endif</div></div>
                </div>
            @endif
            @if($preferNotCount > 0)
                @php $percent = $totalGender > 0 ? round(($preferNotCount / $totalGender) * 100, 1) : 0; @endphp
                <div class="bar-chart-item">
                    <div class="bar-chart-label"><span>🤐 Prefer not to say</span><span>{{ $preferNotCount }} ({{ $percent }}%)</span></div>
                    <div class="bar-chart-bar-container"><div class="bar-chart-bar" style="width: {{ $percent }}%; background: #95a5a6;">@if($percent > 15) {{ $percent }}% @endif</div></div>
                </div>
            @endif
        </div>
        
        <!-- B. Age - Bar Chart -->
        <div class="profile-card">
            <div class="profile-title">B. Age</div>
            @php
                $ageColors = ['#27ae60', '#f39c12', '#e74c3c', '#3498db', '#9b59b6', '#1abc9c', '#e67e22'];
                $ageColorIndex = 0;
            @endphp
            @foreach($age_groups as $group => $count)
                @php
                    $percent = $totalResp > 0 ? round(($count / $totalResp) * 100, 1) : 0;
                    $color = $ageColors[$ageColorIndex % count($ageColors)];
                    $ageColorIndex++;
                @endphp
                <div class="bar-chart-item">
                    <div class="bar-chart-label"><span>👤 {{ $group }}</span><span>{{ $count }} ({{ $percent }}%)</span></div>
                    <div class="bar-chart-bar-container"><div class="bar-chart-bar" style="width: {{ $percent }}%; background: {{ $color }};">@if($percent > 15) {{ $percent }}% @endif</div></div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="profile-grid">
        <!-- C. Type of Respondents - Bar Chart -->
        <div class="profile-card">
            <div class="profile-title">C. Type of Respondents</div>
            @php
                $typeColors = ['#27ae60', '#f39c12', '#3498db', '#9b59b6', '#e74c3c'];
                $typeColorIndex = 0;
            @endphp
            @foreach($respondent_type_counts as $type => $count)
                @php
                    $percent = $totalResp > 0 ? round(($count / $totalResp) * 100, 1) : 0;
                    $color = $typeColors[$typeColorIndex % count($typeColors)];
                    $typeColorIndex++;
                @endphp
                <div class="bar-chart-item">
                    <div class="bar-chart-label"><span>👥 {{ $type }}</span><span>{{ $count }} ({{ $percent }}%)</span></div>
                    <div class="bar-chart-bar-container"><div class="bar-chart-bar" style="width: {{ $percent }}%; background: {{ $color }};">@if($percent > 15) {{ $percent }}% @endif</div></div>
                </div>
            @endforeach
        </div>
        
        <!-- D. Title Distribution - Bar Chart -->
        <div class="profile-card">
            <div class="profile-title">D. Title Distribution</div>
            @php
                $titleColors = ['#27ae60', '#f39c12', '#3498db', '#9b59b6', '#1abc9c'];
                $titleColorIndex = 0;
                $totalTitles = array_sum($title_counts);
            @endphp
            @foreach($title_counts as $title => $count)
                @php
                    $percent = $totalTitles > 0 ? round(($count / $totalTitles) * 100, 1) : 0;
                    $color = $titleColors[$titleColorIndex % count($titleColors)];
                    $titleColorIndex++;
                @endphp
                <div class="bar-chart-item">
                    <div class="bar-chart-label"><span>🏷️ {{ $title }}</span><span>{{ $count }} ({{ $percent }}%)</span></div>
                    <div class="bar-chart-bar-container"><div class="bar-chart-bar" style="width: {{ $percent }}%; background: {{ $color }};">@if($percent > 15) {{ $percent }}% @endif</div></div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="profile-grid">
        <!-- E. Year Level Distribution - Bar Chart -->
        <div class="profile-card">
            <div class="profile-title">E. Year Level Distribution</div>
            @php
                $yearColors = ['#27ae60', '#f39c12', '#3498db', '#9b59b6'];
                $yearColorIndex = 0;
                $totalYears = array_sum($year_level_counts);
            @endphp
            @foreach($year_level_counts as $level => $count)
                @if($count > 0)
                    @php
                        $percent = $totalYears > 0 ? round(($count / $totalYears) * 100, 1) : 0;
                        $color = $yearColors[$yearColorIndex % count($yearColors)];
                        $yearColorIndex++;
                    @endphp
                    <div class="bar-chart-item">
                        <div class="bar-chart-label"><span>📚 {{ $level }}</span><span>{{ $count }} ({{ $percent }}%)</span></div>
                        <div class="bar-chart-bar-container"><div class="bar-chart-bar" style="width: {{ $percent }}%; background: {{ $color }};">@if($percent > 15) {{ $percent }}% @endif</div></div>
                    </div>
                @endif
            @endforeach
        </div>
        
        <!-- F. Summary Statistics -->
        <div class="profile-card">
            <div class="profile-title">F. Summary Statistics</div>
            <div class="bar-chart-item">
                <div class="bar-chart-label"><span>Total Respondents</span><span><strong>{{ number_format($total_responses) }}</strong></span></div>
            </div>
            <div class="bar-chart-item">
                <div class="bar-chart-label"><span>Response Rate</span><span><strong>{{ $response_rate }}%</strong></span></div>
                <div class="bar-chart-bar-container"><div class="bar-chart-bar" style="width: {{ $response_rate }}%; background: #27ae60;">@if($response_rate > 15) {{ $response_rate }}% @endif</div></div>
            </div>
            <div class="bar-chart-item">
                <div class="bar-chart-label"><span>Overall Satisfaction</span><span><strong>{{ number_format($overall_satisfaction, 2) }}/5.0</strong></span></div>
                <div class="bar-chart-bar-container"><div class="bar-chart-bar" style="width: {{ ($overall_satisfaction / 5) * 100 }}%; background: #f39c12;">@if(($overall_satisfaction / 5) * 100 > 15) {{ number_format(($overall_satisfaction / 5) * 100, 0) }}% @endif</div></div>
            </div>
        </div>
    </div>

    <!-- PART II: Event Evaluation -->
    <div class="section-header">II. EVENT EVALUATION</div>

    @foreach($category_scores as $category => $data)
        @if($category != 'Other')
            <div class="subsection-header">{{ $category }}</div>
            <table class="data-table">
                <thead>
                    <tr><th>Indicators</th><th width="15%">Mean</th><th width="25%">Interpretation</th>  </>
                </thead>
                <tbody>
                    @foreach($data['questions'] as $indicator => $mean)
                      <tr>
                          <td>{{ $indicator }}</td>
                        <td class="text-center">{{ number_format($mean, 2) }}</td>
                        <td class="text-center">
                            @if($mean >= 4.5) <span class="rating-excellent">Outstanding</span>
                            @elseif($mean >= 3.5) <span class="rating-good">Very Satisfactory</span>
                            @elseif($mean >= 2.5) <span class="rating-average">Satisfactory</span>
                            @elseif($mean >= 1.5) <span class="rating-poor">Poor</span>
                            @else <span class="rating-very-poor">Very Poor</span> @endif
                        </td>
                      </tr>
                    @endforeach
                    <tr style="background-color: #f0f0f0; font-weight: bold;">
                        <td><strong>Overall Average</strong></td>
                        <td class="text-center"><strong>{{ number_format($data['average'], 2) }}</strong></td>
                        <td class="text-center">
                            @if($data['average'] >= 4.5) <span class="rating-excellent">Outstanding</span>
                            @elseif($data['average'] >= 3.5) <span class="rating-good">Very Satisfactory</span>
                            @elseif($data['average'] >= 2.5) <span class="rating-average">Satisfactory</span>
                            @elseif($data['average'] >= 1.5) <span class="rating-poor">Poor</span>
                            @else <span class="rating-very-poor">Very Poor</span> @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif
    @endforeach

    <div class="subsection-header">Overall Rating on the Event</div>
    <table class="data-table">
        <thead>
            <tr><th>Indicator</th><th width="15%">Mean</th><th width="25%">Interpretation</th> </>
        </thead>
        <tbody>
              <tr>
                <td><strong>Overall rating on the event</strong></td>
                <td class="text-center"><strong>{{ number_format($overall_satisfaction, 2) }}</strong></td>
                <td class="text-center"><strong>{{ $satisfaction_interpretation }}</strong></td>
              </tr>
        </tbody>
    </table>

    <!-- PART III: Sentiment Analysis & Comments - STARTS ON NEW PAGE, COMMENTS FLOW NATURALLY -->
    <div class="page-break"></div>
    
    <div class="section-header">III. SENTIMENT ANALYSIS & COMMENTS</div>

    <!-- Sentiment Gauge -->
    <div class="sentiment-gauge">
        <div class="gauge-container">
            <div class="gauge-positive" style="width: {{ $positive_percentage }}%;">Positive {{ $positive_percentage }}%</div>
            <div class="gauge-neutral" style="width: {{ $neutral_percentage }}%;">Neutral {{ $neutral_percentage }}%</div>
            <div class="gauge-negative" style="width: {{ $negative_percentage }}%;">Negative {{ $negative_percentage }}%</div>
        </div>
        <div class="gauge-legend">
            <div><span class="legend-bullet" style="background: #27ae60;"></span> Positive ({{ $positive_percentage }}%)</div>
            <div><span class="legend-bullet" style="background: #f39c12;"></span> Neutral ({{ $neutral_percentage }}%)</div>
            <div><span class="legend-bullet" style="background: #e74c3c;"></span> Negative ({{ $negative_percentage }}%)</div>
        </div>
        <div class="text-center" style="margin-top: 8px; font-size: 9px; color: #666;">Based on {{ number_format($total_comments) }} total comments</div>
        <div class="text-center" style="margin-top: 4px; font-size: 8px; color: #888;">(Positive: {{ count($positive_comments) }}, Negative: {{ count($negative_comments) }}, Neutral: {{ count($neutral_comments) }})</div>
    </div>

    <!-- Comments - Will start immediately after gauge and flow naturally to next page if needed -->
    <!-- Positive Comments -->
    <div class="comment-group">
        <div class="comment-group-title comment-positive">✅ POSITIVE COMMENTS ({{ count($positive_comments) }})</div>
        <div class="comments-list">
            @php $commentIndex = 1; @endphp
            @foreach($positive_comments as $comment)
                @if(!empty(trim($comment)))
                    <div class="comment-item"><span class="comment-number">{{ $commentIndex }}.</span> {{ trim($comment) }}</div>
                    @php $commentIndex++; @endphp
                @endif
            @endforeach
            @if(count($positive_comments) == 0)
                <div class="comment-item">No positive comments submitted</div>
            @endif
        </div>
    </div>

    <!-- Negative Comments -->
    <div class="comment-group">
        <div class="comment-group-title comment-negative">❌ NEGATIVE COMMENTS ({{ count($negative_comments) }})</div>
        <div class="comments-list">
            @php $commentIndex = 1; @endphp
            @foreach($negative_comments as $comment)
                @if(!empty(trim($comment)))
                    <div class="comment-item"><span class="comment-number">{{ $commentIndex }}.</span> {{ trim($comment) }}</div>
                    @php $commentIndex++; @endphp
                @endif
            @endforeach
            @if(count($negative_comments) == 0)
                <div class="comment-item">No negative comments submitted</div>
            @endif
        </div>
    </div>

    <!-- Neutral Comments -->
    <div class="comment-group">
        <div class="comment-group-title comment-neutral">😐 NEUTRAL COMMENTS ({{ count($neutral_comments) }})</div>
        <div class="comments-list">
            @php $commentIndex = 1; @endphp
            @foreach($neutral_comments as $comment)
                @if(!empty(trim($comment)))
                    <div class="comment-item"><span class="comment-number">{{ $commentIndex }}.</span> {{ trim($comment) }}</div>
                    @php $commentIndex++; @endphp
                @endif
            @endforeach
            @if(count($neutral_comments) == 0)
                <div class="comment-item">No neutral comments submitted</div>
            @endif
        </div>
    </div>

    <!-- Common Themes -->
    @if(count($common_themes) > 0)
    <div class="comment-group">
        <div class="comment-group-title" style="background: #e3f2fd; color: #1565c0;">📊 COMMON THEMES</div>
        <div class="comments-list">
            @foreach($common_themes as $theme)
                <span class="theme-tag">{{ $theme }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- PART IV: AI-Powered Insights - STARTS ON NEW PAGE -->
    <div class="page-break"></div>
    
    <div class="section-header">IV. AI-POWERED INSIGHTS</div>

    <!-- Strengths -->
    <div class="comment-group">
        <div class="comment-group-title" style="background: #e8f5e9; color: #2e7d32;">✅ STRENGTHS & SUCCESS FACTORS</div>
        <div class="comments-list">
            @forelse($strengths as $strength)
                <div class="comment-item">✓ {{ $strength }}</div>
            @empty
                <div class="comment-item">No specific strengths identified</div>
            @endforelse
        </div>
    </div>

    <!-- Weaknesses -->
    <div class="comment-group">
        <div class="comment-group-title" style="background: #ffebee; color: #c62828;">⚠️ AREAS FOR IMPROVEMENT</div>
        <div class="comments-list">
            @forelse($weaknesses as $weakness)
                <div class="comment-item">• {{ $weakness }}</div>
            @empty
                <div class="comment-item">No areas for improvement identified</div>
            @endforelse
        </div>
    </div>

    <!-- Recommendations -->
    @if(count($recommendations) > 0)
    <div class="comment-group">
        <div class="comment-group-title" style="background: #fff3e0; color: #ef6c00;">💡 ACTIONABLE RECOMMENDATIONS</div>
        <div class="comments-list">
            @foreach($recommendations as $rec)
                @php
                    $priority = is_array($rec) ? ($rec['priority'] ?? 'medium') : 'medium';
                    $category = is_array($rec) ? ($rec['category'] ?? 'General') : 'General';
                    $title = is_array($rec) ? ($rec['title'] ?? $rec) : $rec;
                @endphp
                <div class="rec-card rec-priority-{{ $priority }}">
                    <strong>{{ strtoupper($priority) }} PRIORITY - {{ $category }}:</strong><br>
                    {{ $title }}
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- What-If Analysis -->
    @if(!empty($what_if_targeted) && !empty($what_if_optimistic))
    <div class="comment-group">
        <div class="comment-group-title" style="background: #e3f2fd; color: #1565c0;">🔮 WHAT-IF ANALYSIS</div>
        <div class="comments-list">
            @if(is_array($what_if_targeted))
            <div class="rec-card rec-priority-medium">
                <strong>TARGETED IMPROVEMENTS:</strong> Current: {{ $what_if_targeted['current_satisfaction'] ?? 'N/A' }} → Projected: {{ $what_if_targeted['projected_satisfaction'] ?? 'N/A' }} (+{{ $what_if_targeted['gain'] ?? 'N/A' }})
            </div>
            @endif
            @if(is_array($what_if_optimistic))
            <div class="rec-card rec-priority-high">
                <strong>OPTIMISTIC SCENARIO:</strong> Current: {{ $what_if_optimistic['current_satisfaction'] ?? 'N/A' }} → Projected: {{ $what_if_optimistic['projected_satisfaction'] ?? 'N/A' }} (+{{ $what_if_optimistic['gain'] ?? 'N/A' }})
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-name">{{ $generated_by }}</div>
            <div class="signature-title">Prepared by</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-name">ALMA LIGAYA A. BERMUDEZ, PhD</div>
            <div class="signature-title">Head, OPQMS</div>
        </div>
    </div>

    <div class="signature-section" style="margin-top: 15px;">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-name">RONALD A. MONZON, MIT</div>
            <div class="signature-title">Division Chief for Executive Operations</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-name">_____________________</div>
            <div class="signature-title">Noted by</div>
        </div>
    </div>

    <div class="footer">
        This is a system-generated report from CSUCC EMS. For official use only.<br>
        Generated on {{ $report_date }}
    </div>
</div>

</body>
</html>