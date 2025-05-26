<?php
use Carbon\Carbon;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Time Logs Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1, h3 { text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>{{ config('app.name') }} - Time Logs Report</h1>


    @if($logs->isEmpty())
        <p>No time logs found for the selected criteria.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Project</th>
                    <th>Client</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Hours</th>
                    <th>Description</th>
                    <th>Tags</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td>{{ Carbon::parse($log->start_time)->format('Y-m-d') }}</td>
                        <td>{{ $log->project->title ?? 'N/A' }}</td>
                        <td>{{ $log->project->client->name ?? 'N/A' }}</td>
                        <td>{{ Carbon::parse($log->start_time)->format('h:i A') }}</td>
                        <td>{{ Carbon::parse($log->end_time)->format('h:i A') }}</td>
                        <td>{{ $log->hours }}</td>
                        <td>{{ $log->description }}</td>
                        <td>{{ $log->tags }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
