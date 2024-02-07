<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;


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
            //Load tasks with projects to minimize the number of queries.
            $projects = Project::with('tasks')->get();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|RedirectResponse
     */

    public function store(Request $request)
    {
        try {
            // Start a transaction, to roll back due to an error
            DB::beginTransaction();

            $project = Project::create([
                'title' => $request->title,
                'description' => $request->description,
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
     * Remove the specified project from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response|RedirectResponse
     */

    public function destroy(Project $project)
    {
        try {
            $project->delete();
            return redirect()->route('projects.index')->with('success_message', 'Project deleted successfully!');
        } catch (Exception $e) {
            return redirect()->route('projects.index')->with('error_message', 'An error occurred while deleting the project.');
        }
    }
}