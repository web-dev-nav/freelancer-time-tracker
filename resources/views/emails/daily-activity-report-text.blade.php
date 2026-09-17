@php
    $activityColumns = $activityColumns ?? ['summary_sessions', 'summary_hours', 'project', 'clock_in', 'clock_out', 'duration', 'description'];
    $showSummarySessions = in_array('summary_sessions', $activityColumns, true);
    $showSummaryHours = in_array('summary_hours', $activityColumns, true);
@endphp
DAILY ACTIVITY REPORT
Date: {{ $reportDate }} ({{ $timezone }})
@if(!empty($clientName) || !empty($clientEmail))
Client: {{ $clientName ?: 'Client' }}@if(!empty($clientEmail)) ({{ $clientEmail }})@endif

@endif

@if($showSummarySessions || $showSummaryHours)
SUMMARY
@if($showSummarySessions)
Total sessions: {{ $summary['total_sessions'] }}
@endif
@if($showSummaryHours)
Total hours: {{ number_format($summary['total_hours_decimal'], 2) }}
@endif

PROJECT SUMMARY
@forelse($summary['projects'] as $project)
- {{ $project['project_name'] }}: {{ $project['sessions'] }} session(s), {{ sprintf('%d:%02d', intdiv($project['minutes'], 60), $project['minutes'] % 60) }}
@empty
No completed sessions for today.
@endforelse

@endif
ACTIVITY DETAILS
@forelse($logs as $log)
@if(in_array('date', $activityColumns, true))
Date: {{ $log['date'] }}
@endif
@if(in_array('project', $activityColumns, true))
Project: {{ $log['project'] }}
@endif
@if(in_array('clock_in', $activityColumns, true))
In: {{ $log['clock_in'] }}
@endif
@if(in_array('clock_out', $activityColumns, true))
Out: {{ $log['clock_out'] }}
@endif
@if(in_array('duration', $activityColumns, true))
Duration: {{ $log['duration'] }}
@endif
@if(in_array('description', $activityColumns, true))
Description: {{ $log['description_text'] ?? '-' }}
@endif

@empty
No detailed activity entries for today.
@endforelse
