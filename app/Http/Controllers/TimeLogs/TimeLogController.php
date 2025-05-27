<?php

namespace App\Http\Controllers\TimeLogs;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\TimeLog;
use App\Notifications\DailyWorkLimitExceeded;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TimeLogController extends Controller
{
    // Start time log Start ***********************************
    public function start(Request $request)
    {
        try {
            $validated = $request->validate([
                'description' => 'nullable|string|max:255',
                'tags' => 'nullable|in:billable,non-billable',
            ]);
            $userId = auth()->id();
            $projectId = $request->projectId;
            $project = Project::where('id', $projectId)->whereHas('client', fn ($q) => $q->where('user_id', $userId))->first();

            if (! $project) {
                return ResponseHelper::Out(false, 'Invalid project ID', 403);
            }
            // Check if a time log is already running for the project
            $isRunning = TimeLog::where('user_id', $userId)
                ->where('project_id', $projectId)
                ->whereNull('end_time')
                ->exists();
            if ($isRunning) {
                return ResponseHelper::Out(false, 'A time log is already running for this project', 403);
            }
            // define today's date for checking hours
            $logDate = Carbon::today()->toDateString();
            $totalHours = TimeLog::whereDate('start_time', $logDate)
                ->where('user_id', $userId)
                ->sum('hours');
            // Check if the user has exceeded the daily work limit
            if ($totalHours > 8) {
                auth()->user()->notify(new DailyWorkLimitExceeded($logDate, $totalHours));
            }

            TimeLog::create([
                'user_id' => $userId,
                'project_id' => $projectId,
                'start_time' => now(),
                'description' => $validated['description'] ?? null,
                'tags' => $validated['tags'] ?? null,
            ]);

            return ResponseHelper::Out(true, 'Time log started successfully', 201);

        } catch (ValidationException $e) {
            return ResponseHelper::Out(false, $e->errors(), 422);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to start time log', 500);
        }

    }
    // Start time log End ***************************************

    // Stop time log Start ***********************************
    public function end(Request $request)
    {
        try {
            $userId = auth()->id();
            $projectId = $request->projectId;
            $timeLog = TimeLog::where('project_id', $projectId)->where('user_id', $userId)->whereNull('end_time')->first();
            if (! $timeLog) {
                return ResponseHelper::Out(false, 'Invalid Project ID', 403);
            }
            $timeLog->end_time = now();
            $timeLog->hours = $timeLog->start_time->floatDiffInHours($timeLog->end_time);
            $timeLog->save();

            return ResponseHelper::Out(true, 'Time log stopped successfully', 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to stop time log', 500);
        }
    }
    // Stop time log End ***************************************

    // Manual Entry Start ***********************************
    public function manualEntry(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'description' => 'nullable|string|max:255',
                'tags' => 'nullable|in:billable,non-billable',
            ]);
            $userId = auth()->id();
            $projectId = $request->projectId;
            $project = Project::where('id', $projectId)->whereHas('client', fn ($q) => $q->where('user_id', $userId))->first();
            if (! $project) {
                return ResponseHelper::Out(false, 'Invalid project ID', 403);
            }
            $start = Carbon::parse($validated['start_time']);
            $end = Carbon::parse($validated['end_time']);
            $hours = $start->floatDiffInHours($end);
            $timeLog = TimeLog::create([
                'user_id' => $userId,
                'project_id' => $projectId,
                'start_time' => $start,
                'end_time' => $end,
                'description' => $validated['description'] ?? null,
                'hours' => $hours,
                'tags' => $validated['tags'] ?? null,
            ]);

            // define today's date for checking hours
            $logDate = $start->toDateString();
            $totalHours = TimeLog::whereDate('start_time', $logDate)
                ->where('user_id', $userId)
                ->sum('hours');
            // Check if the user has exceeded the daily work limit
            if ($totalHours > 8) {
                auth()->user()->notify(new DailyWorkLimitExceeded($logDate, $totalHours));
            }

            return ResponseHelper::Out(true, 'Time log created successfully', 201);
        } catch (ValidationException $e) {
            return ResponseHelper::Out(false, $e->errors(), 422);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to start time log', 500);
        }
    }
    // Manual Entry End ***************************************

    // Get Time Logs Start ***********************************
    public function getTimeLogs(Request $request)
    {
        try {
            $userId = auth()->id();
            $timeLogs = TimeLog::where('user_id', $userId)->with('project')->get();

            return ResponseHelper::Out(true, $timeLogs, 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to retrieve time logs', 500);
        }
    }
    // Get Time Logs End ***************************************

    // Get Time Log By Id Start ********************************
    public function getTimeLogById(Request $request)
    {
        try {
            $userId = auth()->id();
            $timeLog = TimeLog::where('id', $request->id)->where('user_id', $userId)->with('project')->first();
            if (! $timeLog) {
                return ResponseHelper::Out(false, 'Invalid time log ID', 403);
            }

            return ResponseHelper::Out(true, $timeLog, 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to retrieve time log', 500);
        }
    }
    // Get Time Log By Id End ***********************************

    // Update Time Log Start ***********************************
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'description' => 'nullable|string|max:255',
                'tags' => 'nullable|in:billable,non-billable',
            ]);
            $userId = auth()->id();
            $timeLog = TimeLog::where('id', $request->id)->where('user_id', $userId)->first();
            if (! $timeLog) {
                return ResponseHelper::Out(false, 'Invalid time log ID', 403);
            }
            $start = Carbon::parse($validated['start_time']);
            $end = Carbon::parse($validated['end_time']);
            $hours = $start->floatDiffInHours($end);
            $timeLog->update([
                'start_time' => $start,
                'end_time' => $end,
                'description' => $validated['description'] ?? null,
                'hours' => $hours,
                'tags' => $validated['tags'] ?? null,
            ]);

            // define today's date for checking hours
            $logDate = $start->toDateString();
            $totalHours = TimeLog::whereDate('start_time', $logDate)
                ->where('user_id', $userId)
                ->sum('hours');
            // Check if the user has exceeded the daily work limit
            if ($totalHours > 8) {
                auth()->user()->notify(new DailyWorkLimitExceeded($logDate, $totalHours));
            }

            // If the time log was updated successfully, return a success response
            return ResponseHelper::Out(true, 'Time log updated successfully', 200);
        } catch (ValidationException $e) {
            return ResponseHelper::Out(false, $e->errors(), 422);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to update time log', 500);
        }
    }
    // Update Time Log End ***********************************

    // Delete Time Log Start ***********************************
    public function delete(Request $request)
    {
        try {
            $userId = auth()->id();
            $timeLog = TimeLog::where('id', $request->id)->where('user_id', $userId)->first();
            if (! $timeLog) {
                return ResponseHelper::Out(false, 'Invalid time log ID', 403);
            }
            $timeLog->delete();

            return ResponseHelper::Out(true, 'Time log deleted successfully', 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to delete time log', 500);
        }
    }
    // Delete Time Log End ***********************************

    // Delete All Time Logs Start ***********************************
    public function deleteAll(Request $request)
    {
        try {
            $userId = auth()->id();
            TimeLog::where('user_id', $userId)->delete();

            return ResponseHelper::Out(true, 'All time logs deleted successfully', 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to delete all time logs', 500);
        }
    }
    // Delete All Time Logs End ***********************************

    // Search Time Logs By Per Day and Week Start *****************
    public function search(Request $request)
    {
        try {
            if ($request->filled('client_id')) {
                $clientExists = Client::where('id', $request->client_id)
                    ->where('user_id', auth()->id())
                    ->exists();

                if (! $clientExists) {
                    return ResponseHelper::Out(false, 'Client not found', 404);
                }
            }

            if ($request->filled('project_id')) {
                $projectExists = Project::where('id', $request->project_id)
                    ->whereHas('client', fn ($q) => $q->where('user_id', auth()->id()))
                    ->exists();

                if (! $projectExists) {
                    return ResponseHelper::Out(false, 'Project not found', 404);
                }
            }

            $query = TimeLog::query()->where('user_id', auth()->id());

            if ($request->filled('client_id')) {
                $query->whereHas('project', fn ($q) => $q->where('client_id', $request->client_id));
            }

            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            if ($request->filled('date')) {
                $query->whereDate('start_time', Carbon::parse($request->date));
            }

            if ($request->filled('from') && $request->filled('to')) {
                $from = Carbon::parse($request->from)->startOfDay();
                $to = Carbon::parse($request->to)->endOfDay();
                $query->whereBetween('start_time', [$from, $to]);
            }

            $totalHours = $query->sum('hours');

            return ResponseHelper::Out(true, $totalHours, 200);

        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to search time logs', 500);
        }
    }
    // Search Time Logs By Per Day and Week End *****************

    // Time Log Pdf Start ***********************************
    public function exportTimeLogs(Request $request)
    {
        try {
            if ($request->filled('client_id')) {
                $clientExists = Client::where('id', $request->client_id)
                    ->where('user_id', auth()->id())
                    ->exists();

                if (! $clientExists) {
                    return ResponseHelper::Out(false, 'Client not found', 404);
                }
            }

            if ($request->filled('project_id')) {
                $projectExists = Project::where('id', $request->project_id)
                    ->whereHas('client', fn ($q) => $q->where('user_id', auth()->id()))
                    ->exists();

                if (! $projectExists) {
                    return ResponseHelper::Out(false, 'Project not found', 404);
                }
            }

            $query = TimeLog::query()->where('user_id', auth()->id());

            if ($request->filled('client_id')) {
                $query->whereHas('project', fn ($q) => $q->where('client_id', $request->client_id));
            }

            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            if ($request->filled('date')) {
                $query->whereDate('start_time', Carbon::parse($request->date));
            }

            if ($request->filled('from') && $request->filled('to')) {
                $from = Carbon::parse($request->from)->startOfDay();
                $to = Carbon::parse($request->to)->endOfDay();
                $query->whereBetween('start_time', [$from, $to]);
            }

            if ($request->filled('tag')) {
                $query->where('tags', $request->tag);
            }

            $logs = $query->with('project.client')->get();

            $pdf = Pdf::loadView('pdf.time_logs', compact('logs'));

            return $pdf->stream('time_logs_report.pdf');

        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to search time logs', 500);
        }
    }
    // Time Log Pdf End ***********************************
}
