<?php

namespace App\Http\Controllers\TimeLogs;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\TimeLog;
use Carbon\Carbon;
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
            $timeLog = TimeLog::where('project_id', $projectId)->where('user_id', $userId)->whereNull('end_time')->firstOrFail();
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

            return ResponseHelper::Out(true, 'Time log created successfully', 201);
        } catch (ValidationException $e) {
            return ResponseHelper::Out(false, $e->errors(), 422);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to start time log', 500);
        }
    }
    // Manual Entry End ***************************************
}
