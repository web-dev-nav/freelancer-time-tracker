<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Activity Report</title>
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:Arial,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="760" cellspacing="0" cellpadding="0" style="max-width:95%;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">
                    <tr>
                        <td style="padding:20px 24px;background:#111827;color:#ffffff;">
                            <h1 style="margin:0;font-size:20px;font-weight:700;">Daily Activity Report</h1>
                            <p style="margin:6px 0 0;font-size:13px;opacity:0.9;">Date: {{ $reportDate }} ({{ $timezone }})</p>
                            @if(!empty($clientName) || !empty($clientEmail))
                                <p style="margin:6px 0 0;font-size:13px;opacity:0.9;">
                                    Client:
                                    {{ $clientName ?: 'Client' }}
                                    @if(!empty($clientEmail))
                                        ({{ $clientEmail }})
                                    @endif
                                </p>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 24px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:18px;">
                                <tr>
                                    <td style="padding:12px;border:1px solid #e5e7eb;border-radius:8px;">
                                        <strong style="display:block;font-size:12px;color:#6b7280;text-transform:uppercase;">Total Sessions</strong>
                                        <span style="font-size:24px;font-weight:700;">{{ $summary['total_sessions'] }}</span>
                                    </td>
                                    <td width="12"></td>
                                    <td style="padding:12px;border:1px solid #e5e7eb;border-radius:8px;">
                                        <strong style="display:block;font-size:12px;color:#6b7280;text-transform:uppercase;">Total Hours</strong>
                                        <span style="font-size:24px;font-weight:700;">{{ number_format($summary['total_hours_decimal'], 2) }}</span>
                                    </td>
                                </tr>
                            </table>

                            <h2 style="margin:0 0 10px;font-size:16px;">Project Summary</h2>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin-bottom:20px;">
                                <thead>
                                    <tr>
                                        <th align="left" style="padding:10px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;text-transform:uppercase;">Project</th>
                                        <th align="right" style="padding:10px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;text-transform:uppercase;">Sessions</th>
                                        <th align="right" style="padding:10px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;text-transform:uppercase;">Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($summary['projects'] as $project)
                                        <tr>
                                            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ $project['project_name'] }}</td>
                                            <td align="right" style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ $project['sessions'] }}</td>
                                            <td align="right" style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ sprintf('%d:%02d', intdiv($project['minutes'], 60), $project['minutes'] % 60) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" style="padding:12px;color:#6b7280;">No completed sessions for today.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <h2 style="margin:0 0 10px;font-size:16px;">Activity Details</h2>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                <thead>
                                    <tr>
                                        @php
                                            $activityColumns = $activityColumns ?? ['project', 'clock_in', 'clock_out', 'duration', 'description'];
@endphp
@php
    $descriptionCellStyle = 'padding:10px;border-bottom:1px solid #f3f4f6;line-height:1.55;';
@endphp
                                        @if(in_array('date', $activityColumns, true))
                                            <th align="left" style="padding:10px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;text-transform:uppercase;">Date</th>
                                        @endif
                                        @if(in_array('project', $activityColumns, true))
                                            <th align="left" style="padding:10px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;text-transform:uppercase;">Project</th>
                                        @endif
                                        @if(in_array('clock_in', $activityColumns, true))
                                            <th align="left" style="padding:10px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;text-transform:uppercase;">In</th>
                                        @endif
                                        @if(in_array('clock_out', $activityColumns, true))
                                            <th align="left" style="padding:10px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;text-transform:uppercase;">Out</th>
                                        @endif
                                        @if(in_array('duration', $activityColumns, true))
                                            <th align="left" style="padding:10px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;text-transform:uppercase;">Duration</th>
                                        @endif
                                        @if(in_array('description', $activityColumns, true))
                                            <th align="left" style="padding:10px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;text-transform:uppercase;">Description</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr>
                                            @if(in_array('date', $activityColumns, true))
                                                <td style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ $log['date'] }}</td>
                                            @endif
                                            @if(in_array('project', $activityColumns, true))
                                                <td style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ $log['project'] }}</td>
                                            @endif
                                            @if(in_array('clock_in', $activityColumns, true))
                                                <td style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ $log['clock_in'] }}</td>
                                            @endif
                                            @if(in_array('clock_out', $activityColumns, true))
                                                <td style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ $log['clock_out'] }}</td>
                                            @endif
                                            @if(in_array('duration', $activityColumns, true))
                                                <td style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ $log['duration'] }}</td>
                                            @endif
                                            @if(in_array('description', $activityColumns, true))
                                                <td style="{{ $descriptionCellStyle }}">
                                                    {!! $log['description_html'] ?? '-' !!}
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($activityColumns) }}" style="padding:12px;color:#6b7280;">No detailed activity entries for today.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
