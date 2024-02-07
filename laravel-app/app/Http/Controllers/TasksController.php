<?php

namespace App\Http\Controllers;

// use App\Models\Project;
use App\Models\Tasks;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
// use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;

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
            $user_tasks = Tasks::all();
            return view('user.home', compact('user_tasks'));
        } catch (\Exception $e) {
            return redirect('user-home')->with('flash_error_message', 'An error occurred while fetching tasks.');
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

            Tasks::create([
                'name' => $validatedData['name'],
                'assignTo' => $validatedData['assignTo'],
                'deadlin' => $validatedData['deadlin'],
                'status' => $validatedData['status'],
                'description' => $validatedData['description'],
            ]);

            // Redirect to the tasks index page with a success message
            return redirect()->route('tasks.index')->with('success_message', 'Task added successfully!');
        } catch (\Exception $e) {
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

            $task->delete();

            return redirect('user-home')->with('success_message', 'Task Deleted!');
        } catch (\Exception $e) {
            return redirect('user-home')->with('error_message', 'An error occurred while deleting the task.');
        }

    }
}