<?php

namespace App\Http\Controllers\Projects;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    // Create a new project Start ********************************
    public function createProject(Request $request)
    {
        try {
            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'nullable|in:active,completed',
                'deadline' => 'nullable|date',
            ]);

            $client = Client::where('id', $validated['client_id'])
                ->where('user_id', auth()->id())
                ->first();

            if (! $client) {
                return ResponseHelper::Out(false, 'Invalid client ID', 403);
            }

            $client->projects()->create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'] ?? 'active',
                'deadline' => $validated['deadline'] ?? null,
            ]);

            return ResponseHelper::Out(true, 'Project created successfully', 201);

        } catch (ValidationException $e) {
            return ResponseHelper::Out(false, $e->errors(), 422);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Project creation failed', 500);
        }
    }
    // Create a new project End **********************************

    // Get all projects Start ********************************
    public function getAllProjects(Request $request)
    {
        try {
            $userId = auth()->id();

            $projects = Project::whereHas('client', fn ($q) => $q->where('user_id', $userId))
                ->with('client')
                ->get();

            return ResponseHelper::Out(true, $projects, 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to get projects', 500);
        }
    }
    // Get all projects End **********************************

    // Get a specific project Start ********************************
    public function getProject(Request $request)
    {
        try {
            $userId = auth()->id();

            $project = Project::where('id', $request->id)->whereHas('client', fn ($q) => $q->where('user_id', $userId))->with('client')->first();

            if (! $project) {
                return ResponseHelper::Out(false, 'Project not found', 404);
            }

            return ResponseHelper::Out(true, $project, 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to get project', 500);
        }
    }
    // Get a specific project End **********************************

    // Update a project Start ********************************
    public function updateProject(Request $request)
    {
        try {
            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'nullable|in:active,completed',
                'deadline' => 'nullable|date',
            ]);

            $userId = auth()->id();

            $project = Project::where('id', $request->id)
                ->whereHas('client', fn ($q) => $q->where('user_id', $userId))->first();

            if (! $project) {
                return ResponseHelper::Out(false, 'Project not found', 404);
            }

            $project->update([
                'client_id' => $validated['client_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'] ?? 'active',
                'deadline' => $validated['deadline'] ?? null,
            ]);

            return ResponseHelper::Out(true, 'Project updated successfully', 200);

        } catch (ValidationException $e) {
            return ResponseHelper::Out(false, $e->errors(), 422);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Project update failed', 500);
        }

    }
    // Update a project End **********************************

    // Delete a project Start ********************************
    public function deleteProject(Request $request)
    {
        try {
            $userId = auth()->id();

            $project = Project::where('id', $request->id)
                ->whereHas('client', fn ($q) => $q->where('user_id', $userId))->first();

            if (! $project) {
                return ResponseHelper::Out(false, 'Project not found', 404);
            }

            $project->delete();

            return ResponseHelper::Out(true, 'Project deleted successfully', 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to delete project', 500);
        }
    }
    // Delete a project End **********************************

    // Delete all projects Start ********************************
    public function deleteAllProjects(Request $request)
    {
        try {
            $userId = auth()->id();

            $deleted = Project::whereHas('client', fn ($q) => $q->where('user_id', $userId))->delete();

            return ResponseHelper::Out(true, 'All projects deleted successfully', 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to delete all projects', 500);
        }
    }
    // Delete all projects End **********************************

    // Get projects by client Start ********************************
    public function getProjectsByClient(Request $request)
    {
        try {
            $userId = auth()->id();

            $projects = Project::where('client_id', $request->clientId)
                ->whereHas('client', fn ($q) => $q->where('user_id', $userId))->with('client')->get();

            return ResponseHelper::Out(true, $projects, 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to get projects by client', 500);
        }
    }
    // Get projects by client End **********************************
}
