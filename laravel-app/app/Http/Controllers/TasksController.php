<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use App\Models\Tasks;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Requests\TaskRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class TasksController extends Controller
{
    /**
     * Display a listing of the tasks.
     * 
     * @return View
     */
    public function index()
    {
        try {
            $current_user = Auth::id();

            // Fetch tasks created by the authenticated user
            $user_created_tasks = Tasks::where('created_by', $current_user)->get();

            // Fetch tasks assigned to the authenticated user
            $user_assigned_tasks = Tasks::whereHas('users', function ($query) use ($current_user) {
                $query->where('user_id', $current_user);
            })->with('projects')->get();

            // Fetch unique projects associated with tasks assigned to the authenticated user
            $projects_assigned_to_user = $user_assigned_tasks->flatMap->projects->unique();

            // Fetch unique projects created by the authenticated user
            $projects_created_by_user = Project::where('created_by', $current_user)->get();

            // Merge the two collections of projects
            $projects = $projects_assigned_to_user->merge($projects_created_by_user)->unique();

            return view('user.home', compact('user_created_tasks', 'projects'));
        } catch (\Exception $e) {
            return redirect('user-home')->with('flash_error_message', 'An error occurred while fetching tasks and projects.');
        }

    }

    /**
     * Show the form for creating a new task.
     * 
     * @return View
     */
    public function create()
    {
        try {
            $users = User::pluck('name', 'id');
            return view('user.create', compact('users'));
        } catch (\Exception $e) {
            return redirect('user-home')->with('flash_error_message', 'An error occurred while fetching user data for task creation.');
        }
    }

    /**
     * Store a newly task in storage.
     * @return RedirectResponse
     */
    public function store(TaskRequest $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validated();

            // Store the authenticated user's ID along with the task
            $createdBy = Auth::id();
            $validatedData['created_by'] = $createdBy;

            // Extract the users from the validated data
            $selectedUsers = $request->input('assignTo', []);

            // Remove the creator's ID from the selected users if present
            $selectedUsers = array_diff($selectedUsers, [$createdBy]);

            // Begin a transaction
            DB::beginTransaction();

            // Create the task
            $task = Tasks::create($validatedData);

            // Check if task creation fails
            if (!$task) {
                throw new \Exception('Failed to create task.');
            }

            // Attach the task to selected users
            foreach ($selectedUsers as $userId) {
                \Log::info('Inserting task-user association:', ['task_id' => $task->id, 'user_id' => $userId]);
                DB::table('task_user')->insert([
                    'task_id' => $task->id,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // If the creator is selected, attach the task to them as well
            if (in_array($createdBy, $request->input('assignTo', []))) {
                \Log::info('Inserting task-user association for creator:', ['task_id' => $task->id, 'user_id' => $createdBy]);
                DB::table('task_user')->insert([
                    'task_id' => $task->id,
                    'user_id' => $createdBy,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Attach the task to the selected project
            $task->projects()->attach($request->project_id);

            // Commit the transaction
            DB::commit();

            // Redirect to the tasks index page with a success message
            return redirect()->route('tasks.index')->with('success_message', 'Task added successfully!');
        } catch (\Exception $e) {
            \Log::error('Error creating task: ' . $e->getMessage());

            // Rollback the transaction in case of an exception
            DB::rollback();

            // Redirect back to the create page with an error message if an exception occurs
            return redirect()->route('tasks.create')->with('error_message', 'An error occurred while creating the task.');
        }

    }



    /**
     * Display the specified task.
     * 
     * @return View
     */
    public function show(string $id): View
    {
        try {
            $tasksDetails = Tasks::find($id);

            if (!$tasksDetails) {
                return redirect('user-home')->with('error_message', 'Task not found.');
            }

            return view('user.show', compact('tasksDetails'));
        } catch (\Exception $e) {
            return redirect('user-home')->with('error_message', 'An error occurred while fetching the task details.');
        }
    }

    /**
     * Show the form for editing the task .
     * 
     * @return View
     */
    public function edit(string $id)
    {
        try {
            $tasksDetails = Tasks::find($id);

            if (!$tasksDetails) {
                return redirect('user-home')->with('error_message', 'Task not found.');
            }

            $users = User::pluck('name', 'id');
            return view('user.edit', compact('tasksDetails', 'users'));
        } catch (\Exception $e) {
            return redirect('user-home')->with('error_message', 'An error occurred while fetching the task details for editing.');
        }
    }

    /**
     * Update the specified task in storage.
     * 
     * @return RedirectResponse
     */
    public function update(TaskRequest $request, string $id)
    {
        try {
            $task = Tasks::find($id);
            if (!$task) {
                return redirect('user-home')->with('error_message', 'Task not found.');
            }
            // Filter out any fields that shouldn't be updated
            $validatedData = $request->validated();
            unset($validatedData['created_by']); // Remove 'created_by' if it's not intended to be updated
            unset($validatedData['updated_at']); // Remove 'updated_at' if it's not intended to be updated

            $validatedData = $request->validated();
            $task->update($validatedData);
            return redirect('user-home')->with('success_message', 'Task Updated!');
        } catch (\Exception $e) {
            return redirect('user-home')->with('error_message', 'An error occurred while updating the task.');
        }

    }

    /**
     * Remove the specified task from storage.
     * 
     * @return RedirectResponse
     */
    public function destroy(string $id)
    {
        try {
            $task = Tasks::find($id);

            if (!$task) {
                return redirect('user-home')->with('error_message', 'Task not found.');
            }

            // Delete related records in the task_user pivot table
            $task->users()->detach();

            // Delete the task
            $task->delete();

            return redirect('user-home')->with('success_message', 'Task Deleted!');
        } catch (\Exception $e) {
            \Log::error('Error deleting task: ' . $e->getMessage());
            return redirect('user-home')->with('error_message', 'An error occurred while deleting the task.');
        }
    }


}