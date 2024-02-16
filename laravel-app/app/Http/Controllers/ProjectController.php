<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreProjectRequest;

class ProjectController extends Controller
{
    /**
     * Display a listing of all projects with their tasks.
     *
     *@return ViewContract|ViewFactory|RedirectResponse
     */

    public function index()
    {
        try {
            // Retrieve all projects with their associated tasks, filtered by the authenticated user who created them.
            $projects = Project::with('tasks')->where('created_by', Auth::id())->get();
            return view('admin.adminhome', compact('projects'));
        } catch (Exception $e) {
            // Redirect with an error message if there's an issue fetching projects.
            return redirect()->route('admin-home')->with('error_message', 'An error occurred while fetching projects.');
        }
    }

    /**
     * Show the form for creating a new project.
     *
     * @return ViewContract|ViewFactory|RedirectResponse
     */

    public function create()
    {
        // Fetch all tasks to allow admin to attach tasks to the project.
        $tasks = Tasks::all();
        return view('admin.create', compact('tasks'));
    }
    /**
     * Store a newly created project in storage.
     *
     * @param  StoreProjectRequest  $request
     * @return \Illuminate\Http\Response|RedirectResponse
     */

    public function store(StoreProjectRequest $request)
    {
        try {
            // Start a transaction, to roll back due to an error
            DB::beginTransaction();

            $project = Project::create([
                'title' => $request->title,
                'description' => $request->description,
                'created_by' => Auth::id(),
            ]);

            // Check if tasks are provided and attach them to the project
            if ($request->filled('tasks')) {
                $project->tasks()->attach($request->tasks);
            }

            // If everything went well, commit the transaction
            DB::commit();

            return redirect()->route('projects.index')->with('success_message', 'Project created successfully!');
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Redirect back with an error message
            return back()->withInput()->with('error_message', 'An error occurred while creating the project. Please try again.');
        }
    }

    /**
     * Confirm and delete the specified project from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function confirmAndDestroy(Request $request, Project $project)
    {
        // Check if project name matches
        if ($request->project_name !== $project->title) {
            return redirect()->back()->with('error_message', 'The selected project name is incorrect.');
        }

        // Validate confirmation
        try {
            $request->validate([
                'project_name' => 'required|in:' . $project->title, // Match project title for confirmation
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->with('error_message', $e->getMessage())->withErrors($e->errors());
        }

        // Call the destroy method
        return $this->destroy($request, $project);
    }

    /**
     * Delete the specified project from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Project $project)
    {
        try {
            $project->delete();
            return redirect()->route('projects.index')->with('success_message', 'Project deleted successfully!');
        } catch (\Throwable $e) {
            \Log::error('Error deleting project: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->route('projects.index')->with('error_message', 'An error occurred while deleting the project.');
        }
    }
}