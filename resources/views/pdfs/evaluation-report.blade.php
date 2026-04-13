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
        
        .date-header {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 15px 0;
            background-color: #1a472a;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        
        .subsection-header {
            font-size: 11px;
            font-weight: bold;
            margin: 12px 0 8px 0;
            background-color: #f9fafb;
            padding: 6px 10px;
            border-bottom: 1px solid #ddd;
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
            background-color: #f0f0f0;
            text-align: left;
            font-weight: bold;
        }
        
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
        
        .event-details {
            margin: 12px 0;
            line-height: 1.8;
            font-size: 10px;
        }
        
        .event-details p {
            margin: 5px 0;
        }
        
        .intro-text {
            margin: 12px 0;
            text-align: justify;
            line-height: 1.6;
            font-size: 10px;
        }
        
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
        
        .table-caption {
            font-size: 8px;
            font-style: italic;
            margin-top: 5px;
            text-align: center;
        }
        
        .satisfaction-section {
            margin: 15px 0;
        }
        
        .satisfaction-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .satisfaction-score {
            text-align: center;
            margin: 15px 0;
            padding: 15px;
            background-color: #f0fdf4;
            border-radius: 8px;
        }
        
        .satisfaction-score .score {
            font-size: 32px;
            font-weight: bold;
            color: #1a472a;
        }
        
        .satisfaction-score .interpretation {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<!-- FIXED HEADER - REPEATS ON EVERY PAGE -->
<div class="fixed-header">
    <div class="header-content">
        @if(isset($header_image) && $header_image)
            <img src="data:image/png;base64,{{ $header_image }}" class="header-image" alt="CSUCC Logo">
        @endif
    </div>
</div>

<div class="content">
    <!-- ==================== PER-DAY COMPLETE REPORTS ==================== -->
    @if(isset($per_date_data) && count($per_date_data) > 0)
        @foreach($per_date_data as $index => $dateData)
            @if($index > 0)
                <div class="page-break"></div>
            @endif
            
            <!-- DAY HEADER -->
            <div class="date-header">
                DAY {{ $dateData['date_index'] }}: {{ $dateData['formatted_date'] }}
                <div style="font-size: 10px; font-weight: normal;">({{ $dateData['response_count'] }} responses)</div>
            </div>

            <!-- REPORT TITLE -->
            <div class="report-title">EVENT EVALUATION REPORT</div>

            <!-- EVENT DETAILS -->
            <div class="event-details">
                <p><strong>Title of Activity:</strong> {{ $event->event_name }}</p>
                <p><strong>Inclusive Dates:</strong> {{ $dateData['formatted_date'] }}</p>
                <p><strong>Venue:</strong> {{ $venue }}</p>
                <p><strong>Number of Attendees:</strong> {{ number_format($total_eligible) }}</p>
                <p><strong>Number of Responses:</strong> {{ number_format($dateData['response_count']) }}</p>
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
                    <tr><th>Scale</th><th>Average Rating</th><th>Interpretation</th><th>Verbal Interpretation</th></tr>
                </thead>
                <tbody>
                    <tr><td class="text-center">5</td><td class="text-center">4.50 – 5.00</td><td class="text-center">Very Satisfied</td><td class="text-center">Outstanding</td></tr>
                    <tr><td class="text-center">4</td><td class="text-center">3.50 – 4.49</td><td class="text-center">Satisfied</td><td class="text-center">Very Satisfactory</td></tr>
                    <tr><td class="text-center">3</td><td class="text-center">2.50 – 3.49</td><td class="text-center">Neither Satisfied nor Dissatisfied</td><td class="text-center">Satisfactory</td></tr>
                    <tr><td class="text-center">2</td><td class="text-center">1.50 – 2.49</td><td class="text-center">Dissatisfied</td><td class="text-center">Poor</td></tr>
                    <tr><td class="text-center">1</td><td class="text-center">1.00 – 1.49</td><td class="text-center">Very Dissatisfied</td><td class="text-center">Very Poor</td></tr>
                </tbody>
            </table>
            <div class="table-caption">Table 1. 5-points Likert Scale</div>

            @php
                // Get interpretation for the day
                $dayOverallAverage = $dateData['overall_satisfaction'] ?? 0;
                if($dayOverallAverage >= 4.50) $dayInterpretation = 'Outstanding';
                elseif($dayOverallAverage >= 3.50) $dayInterpretation = 'Very Satisfactory';
                elseif($dayOverallAverage >= 2.50) $dayInterpretation = 'Satisfactory';
                elseif($dayOverallAverage >= 1.50) $dayInterpretation = 'Poor';
                else $dayInterpretation = 'Very Poor';
            @endphp

            <div class="satisfaction-score">
                <div class="score">{{ number_format($dayOverallAverage, 2) }}/5.0</div>
                <div class="interpretation">{{ strtoupper($dayInterpretation) }}</div>
            </div>

            <!-- PART I: Profile of Respondents for this day -->
            <div class="section-header">I. PROFILE OF RESPONDENTS</div>

            <div class="profile-grid">
                <div class="profile-card">
                    <div class="profile-title">A. Sex</div>
                    @php
                        $maleCount = $dateData['gender_counts']['Male'] ?? 0;
                        $femaleCount = $dateData['gender_counts']['Female'] ?? 0;
                        $totalGender = $maleCount + $femaleCount;
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
                </div>

                <div class="profile-card">
                    <div class="profile-title">B. Age</div>
                    @foreach($dateData['age_groups'] as $group => $count)
                        @if($count > 0)
                            @php $percent = $dateData['response_count'] > 0 ? round(($count / $dateData['response_count']) * 100, 1) : 0; @endphp
                            <div class="bar-chart-item">
                                <div class="bar-chart-label"><span>👤 {{ $group }}</span><span>{{ $count }} ({{ $percent }}%)</span></div>
                                <div class="bar-chart-bar-container"><div class="bar-chart-bar" style="width: {{ $percent }}%; background: #3498db;">@if($percent > 15) {{ $percent }}% @endif</div></div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="profile-grid">
                <div class="profile-card">
                    <div class="profile-title">C. Type of Respondents</div>
                    @foreach($dateData['respondent_types'] as $type => $count)
                        @if($count > 0)
                            @php $percent = $dateData['response_count'] > 0 ? round(($count / $dateData['response_count']) * 100, 1) : 0; @endphp
                            <div class="bar-chart-item">
                                <div class="bar-chart-label"><span>👥 {{ $type }}</span><span>{{ $count }} ({{ $percent }}%)</span></div>
                                <div class="bar-chart-bar-container"><div class="bar-chart-bar" style="width: {{ $percent }}%; background: #9b59b6;">@if($percent > 15) {{ $percent }}% @endif</div></div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="profile-card">
                    <div class="profile-title">D. Title Distribution</div>
                    @foreach($dateData['title_counts'] as $title => $count)
                        @if($count > 0)
                            @php $percent = $dateData['response_count'] > 0 ? round(($count / $dateData['response_count']) * 100, 1) : 0; @endphp
                            <div class="bar-chart-item">
                                <div class="bar-chart-label"><span>🏷️ {{ $title }}</span><span>{{ $count }} ({{ $percent }}%)</span></div>
                                <div class="bar-chart-bar-container"><div class="bar-chart-bar" style="width: {{ $percent }}%; background: #1abc9c;">@if($percent > 15) {{ $percent }}% @endif</div></div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- PART II: Event Evaluation for this day -->
            <div class="section-header">II. EVENT EVALUATION</div>
            
            @if(!empty($dateData['category_scores']))
                @foreach($dateData['category_scores'] as $category => $data)
                    @if($category != 'Other' && !empty($data['questions']))
                        <div class="subsection-header">{{ $category }}</div>
                        <table class="data-table">
                            <thead>
                                <tr><th>Indicators</th><th width="15%">Mean</th><th width="25%">Interpretation</th></tr>
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
                        <tr><th>Indicator</th><th width="15%">Mean</th><th width="25%">Interpretation</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Overall rating on the event</strong></td>
                            <td class="text-center"><strong>{{ number_format($dayOverallAverage, 2) }}</strong></td>
                            <td class="text-center"><strong>{{ $dayInterpretation }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            @else
                <div class="comment-group">
                    <div class="comment-group-title" style="background: #ffebee; color: #c62828;">⚠️ No evaluation data available for this date</div>
                    <div class="comments-list"><div class="comment-item">No responses found for {{ $dateData['formatted_date'] }}</div></div>
                </div>
            @endif

            <!-- PART III: Comments for this day -->
            <div class="section-header">III. COMMENTS</div>
            
            @if(count($dateData['sentiment']['positive_comments']) > 0)
            <div class="comment-group">
                <div class="comment-group-title comment-positive">✅ POSITIVE COMMENTS ({{ count($dateData['sentiment']['positive_comments']) }})</div>
                <div class="comments-list">
                    @php $commentIndex = 1; @endphp
                    @foreach($dateData['sentiment']['positive_comments'] as $comment)
                        <div class="comment-item"><span class="comment-number">{{ $commentIndex }}.</span> {{ trim($comment) }}</div>
                        @php $commentIndex++; @endphp
                    @endforeach
                </div>
            </div>
            @endif

            @if(count($dateData['sentiment']['negative_comments']) > 0)
            <div class="comment-group">
                <div class="comment-group-title comment-negative">❌ NEGATIVE COMMENTS ({{ count($dateData['sentiment']['negative_comments']) }})</div>
                <div class="comments-list">
                    @php $commentIndex = 1; @endphp
                    @foreach($dateData['sentiment']['negative_comments'] as $comment)
                        <div class="comment-item"><span class="comment-number">{{ $commentIndex }}.</span> {{ trim($comment) }}</div>
                        @php $commentIndex++; @endphp
                    @endforeach
                </div>
            </div>
            @endif

            @if(count($dateData['sentiment']['neutral_comments']) > 0)
            <div class="comment-group">
                <div class="comment-group-title comment-neutral">😐 NEUTRAL COMMENTS ({{ count($dateData['sentiment']['neutral_comments']) }})</div>
                <div class="comments-list">
                    @php $commentIndex = 1; @endphp
                    @foreach($dateData['sentiment']['neutral_comments'] as $comment)
                        <div class="comment-item"><span class="comment-number">{{ $commentIndex }}.</span> {{ trim($comment) }}</div>
                        @php $commentIndex++; @endphp
                    @endforeach
                </div>
            </div>
            @endif

            @if(count($dateData['sentiment']['positive_comments']) == 0 && count($dateData['sentiment']['negative_comments']) == 0 && count($dateData['sentiment']['neutral_comments']) == 0)
                <div class="comment-group">
                    <div class="comment-group-title" style="background: #e3f2fd; color: #1565c0;">📝 NO COMMENTS</div>
                    <div class="comments-list"><div class="comment-item">No comments submitted for this date</div></div>
                </div>
            @endif

            <!-- Sentiment Gauge for this day -->
            <div class="sentiment-gauge">
                <div class="gauge-container">
                    <div class="gauge-positive" style="width: {{ $dateData['sentiment']['positive_percentage'] }}%;">Positive {{ $dateData['sentiment']['positive_percentage'] }}%</div>
                    <div class="gauge-neutral" style="width: {{ $dateData['sentiment']['neutral_percentage'] }}%;">Neutral {{ $dateData['sentiment']['neutral_percentage'] }}%</div>
                    <div class="gauge-negative" style="width: {{ $dateData['sentiment']['negative_percentage'] }}%;">Negative {{ $dateData['sentiment']['negative_percentage'] }}%</div>
                </div>
                <div class="gauge-legend">
                    <div><span class="legend-bullet" style="background: #27ae60;"></span> Positive ({{ $dateData['sentiment']['positive_percentage'] }}%)</div>
                    <div><span class="legend-bullet" style="background: #f39c12;"></span> Neutral ({{ $dateData['sentiment']['neutral_percentage'] }}%)</div>
                    <div><span class="legend-bullet" style="background: #e74c3c;"></span> Negative ({{ $dateData['sentiment']['negative_percentage'] }}%)</div>
                </div>
                <div class="text-center" style="margin-top: 8px; font-size: 9px; color: #666;">
                    Based on {{ count($dateData['sentiment']['positive_comments']) + count($dateData['sentiment']['negative_comments']) + count($dateData['sentiment']['neutral_comments']) }} comments
                </div>
            </div>

          <!-- PART IV: DECISION SUPPORT INSIGHTS for this day -->
@if($dateData['has_ai_insights'])
<div class="section-header">IV. DECISION SUPPORT INSIGHTS</div>

    <!-- 1. OVERALL RATING CARD -->
    <div class="comment-group">
        

    <!-- 4. CATEGORY PERFORMANCE -->
    @if(!empty($dateData['category_scores']))
    <div class="comment-group">
        <div class="comment-group-title" style="background: #e8f5e9; color: #2e7d32;">📈 CATEGORY PERFORMANCE</div>
        <div class="comments-list">
            <div class="comment-item" style="font-style: italic; margin-bottom: 8px;">Detailed breakdown of each evaluation category</div>
            @foreach($dateData['category_scores'] as $categoryName => $categoryData)
                @php
                    $catScore = $categoryData['average'] ?? 0;
                    if($catScore >= 4.50) $catRating = 'Outstanding';
                    elseif($catScore >= 3.50) $catRating = 'Very Satisfactory';
                    elseif($catScore >= 2.50) $catRating = 'Satisfactory';
                    elseif($catScore >= 1.50) $catRating = 'Poor';
                    else $catRating = 'Very Poor';
                    
                    $catPercent = ($catScore / 5) * 100;
                @endphp
                <div class="comment-item">
                    <strong>{{ $categoryName }}</strong>
                    <div style="float: right;">{{ number_format($catScore, 2) }}/5.0 - <span style="color: #27ae60;">{{ $catRating }}</span></div>
                    <div style="clear: both;"></div>
                    <div style="width: 100%; height: 8px; background-color: #e0e0e0; border-radius: 4px; margin-top: 4px;">
                        <div style="width: {{ $catPercent }}%; height: 100%; background-color: #27ae60; border-radius: 4px;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 5. LOW SCORING QUESTIONS (Below 3.5) -->
    @php
        $lowScoringQuestions = [];
        if(!empty($dateData['category_scores'])){
            foreach($dateData['category_scores'] as $categoryName => $categoryData){
                if(!empty($categoryData['questions'])){
                    foreach($categoryData['questions'] as $questionText => $qScore){
                        if($qScore < 3.5){
                            $lowScoringQuestions[] = [
                                'question' => $questionText,
                                'score' => $qScore,
                                'category' => $categoryName
                            ];
                        }
                    }
                }
            }
        }
        usort($lowScoringQuestions, function($a, $b) { return $a['score'] <=> $b['score']; });
    @endphp

    @if(count($lowScoringQuestions) > 0)
    <div class="comment-group">
        <div class="comment-group-title" style="background: #ffebee; color: #c62828;">⚠️ LOW SCORING QUESTIONS</div>
        <div class="comments-list">
            <div class="comment-item" style="font-style: italic; margin-bottom: 8px;">Questions with average rating below 3.5/5.0 - Requires immediate attention</div>
            @foreach($lowScoringQuestions as $index => $item)
                <div class="comment-item">
                    <strong>#{{ $index + 1 }}</strong> {{ $item['question'] }}
                    <div style="float: right;"><strong>{{ number_format($item['score'], 2) }}/5.0</strong></div>
                    <div style="clear: both;"></div>
                    <div>Category: {{ $item['category'] }}</div>
                    <div><span style="color: #e74c3c;">High Priority</span> 💡 Review and address this area for improvement</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 6. CRITICAL SUCCESS FACTORS -->
    @if(!empty($dateData['category_scores']))
    <div class="comment-group">
        <div class="comment-group-title" style="background: #fff3e0; color: #ef6c00;">🔥 CRITICAL SUCCESS FACTORS</div>
        <div class="comments-list">
            <div class="comment-item" style="font-style: italic; margin-bottom: 8px;">Factors that have the highest impact on overall satisfaction</div>
            @php
                $sortedCategories = [];
                foreach($dateData['category_scores'] as $catName => $catData){
                    $sortedCategories[] = ['name' => $catName, 'score' => $catData['average'] ?? 0];
                }
                usort($sortedCategories, function($a, $b) { return $b['score'] <=> $a['score']; });
            @endphp
            @foreach($sortedCategories as $cat)
                @php
                    $impactPercent = ($cat['score'] / 5) * 100;
                    $needsText = $cat['score'] < 3.5 ? 'Needs improvement to meet expectations' : 'Meeting expectations';
                @endphp
                <div class="comment-item">
                    <strong>{{ $cat['name'] }}</strong>
                    <div style="float: right;">{{ number_format($cat['score'], 2) }}/5.0 - {{ $impactPercent }}% impact</div>
                    <div style="clear: both;"></div>
                    <div style="color: {{ $cat['score'] < 3.5 ? '#e74c3c' : '#27ae60' }};">{{ $needsText }}</div>
                    <div style="width: 100%; height: 6px; background-color: #e0e0e0; border-radius: 3px; margin-top: 4px;">
                        <div style="width: {{ $impactPercent }}%; height: 100%; background-color: {{ $cat['score'] < 3.5 ? '#e74c3c' : '#27ae60' }}; border-radius: 3px;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 7. STRENGTHS -->
    @if(count($dateData['strengths']) > 0)
    <div class="comment-group">
        <div class="comment-group-title" style="background: #e8f5e9; color: #2e7d32;">✅ STRENGTHS</div>
        <div class="comments-list">
            @foreach($dateData['strengths'] as $strength)
                <div class="comment-item">✓ {{ $strength }}</div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 8. AREAS FOR IMPROVEMENT -->
    @if(count($dateData['weaknesses']) > 0)
    <div class="comment-group">
        <div class="comment-group-title" style="background: #ffebee; color: #c62828;">⚠️ AREAS FOR IMPROVEMENT</div>
        <div class="comments-list">
            @foreach($dateData['weaknesses'] as $weakness)
                <div class="comment-item">[Needs Improvement] {{ $weakness }}</div>
            @endforeach
        </div>
    </div>
    @endif

    
    <!-- 10. ACTIONABLE RECOMMENDATIONS -->
    @if(count($lowScoringQuestions) > 0 || count($dateData['recommendations']) > 0)
    <div class="comment-group">
        <div class="comment-group-title" style="background: #fff3e0; color: #ef6c00;">💡 ACTIONABLE RECOMMENDATIONS</div>
        <div class="comments-list">
            <div class="comment-item" style="font-style: italic; margin-bottom: 8px;">Based on participant feedback and scores</div>
            @foreach($lowScoringQuestions as $item)
                @php
                    $targetScore = $item['score'] < 3.0 ? 4.0 : 3.5;
                    $categoryName = $item['category'];
                @endphp
                <div class="rec-card rec-priority-high">
                    <strong>{{ strtoupper($item['score'] < 3.0 ? 'HIGH' : 'MEDIUM') }} PRIORITY</strong>
                    <div>{{ number_format($item['score'], 2) }}/5.0 → {{ $targetScore }}/5.0</div>
                    <div>Improve {{ $categoryName }}</div>
                    <div style="margin-top: 5px;">{{ $categoryName }} is currently at {{ number_format($item['score'], 2) }}/5.0, below the target of 3.50.</div>
                    <div style="margin-top: 5px;"><strong>Action Steps:</strong> → Review current processes → Gather more specific feedback → Implement targeted improvements</div>
                    <div style="margin-top: 5px;">🎯 Increase {{ $categoryName }} satisfaction to {{ $targetScore }}+</div>
                </div>
            @endforeach
            @foreach($dateData['recommendations'] as $rec)
                @php
                    $priority = is_array($rec) ? ($rec['priority'] ?? 'medium') : 'medium';
                    $title = is_array($rec) ? ($rec['title'] ?? $rec) : $rec;
                @endphp
                <div class="rec-card rec-priority-{{ $priority }}">
                    <strong>{{ strtoupper($priority) }} PRIORITY:</strong> {{ $title }}
                </div>
            @endforeach
        </div>
    </div>
    @endif

@endif
            <!-- Signature for this day's report -->
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
                This is a system-generated report from EventFLow. For official use only.<br>
                Generated on {{ $report_date }}
            </div>
        @endforeach
    @endif
</div>

</body>
</html>