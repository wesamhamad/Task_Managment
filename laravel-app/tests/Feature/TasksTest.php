<?php

namespace Tests\Feature;

use App\Models\Tasks;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TasksTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    private $user;
    private $admin;
    private $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();
        $this->user = $this->createUser();
        $this->task = $this->getTask();

        // Authenticate as user
        $this->actingAs($this->user);
    }

    private function getTask()
    {
        $task = [
            'id' => 1,
            'name' => 'New Task',
            'deadlin' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'Pending',
            'description' => 'Task description',
        ];

        return $task;
    }
    private function createAdmin(): User
    {

        return User::factory()->create(['usertype' => 'admin']);
    }

    private function createUser(): User
    {
        return User::factory()->create(['usertype' => 'user']);
    }

    private function actingAsGuest($guard = null)
    {
        Auth::guard($guard)->logout();
        Auth::shouldUse($guard);
    }

    public function test_task_validation_rules(): void
    {
        // Simulate task creation request with invalid data
        $response = $this->post(route('tasks.store'), [
            // Missing required 'name' field
            'deadline' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'Pending',
            'description' => 'Task description',
        ]);

        // dd(session()->all());

        $response->assertStatus(302);
        // Assert that the response contains validation errors
        $response->assertSessionHasErrors(['name']);
    }

    public function test_task_update(): void
    {

        // Create a task with initial data
        $initialTask = $this->task;
        $initialTask['created_by'] = $this->user->id; // Set the created_by attribute

        // Make sure the task exists in the database before attempting to update
        $task = Tasks::create($initialTask);

        // Define updated task data
        $updatedTask = [
            'name' => 'Updated Task',
            'deadlin' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'Pending',
            'description' => 'Task description',
            'created_by' => $this->user->id,
        ];

        $newTaskId = $task->id;

        // Update the task with the updated data
        $response = $this->put("/user-home/{$newTaskId}", $updatedTask);

        $response->assertStatus(302);
        $response->assertRedirect('/user-home');

        // Check if the updated task attributes exist in the database
        $this->assertDatabaseHas('user_tasks', $updatedTask);

        $this->get('/user-home')->assertSee($updatedTask['name']);
    }

    public function test_task_delete(): void
    {
        // Arrange: Create a task and associate it with the user
        $initialTask = $this->task;
        $initialTask['created_by'] = $this->user->id;
        $task = Tasks::create($initialTask);

        // Act: Delete the task
        $response = $this->delete("/user-home/{$task->id}");

        // Assert: Check the response
        $response->assertStatus(302);
        $response->assertRedirect('/user-home');
    }

    public function test_only_authenticated_user_can_access_tasks_index()
    {
        $this->actingAsGuest('web');
        $response = $this->get(route('tasks.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_only_authenticated_user_can_create_task()
    {
        $this->actingAsGuest('web');
        $response = $this->get(route('tasks.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_task_creation(): void
    {
        // Simulate task creation request
        $response = $this->post(route('tasks.store'), $this->task);

        // Assert that the user is redirected and task is created successfully
        $response->assertStatus(302)
            ->assertRedirect('/tasks')
            ->assertSessionHasNoErrors();

        // Assert that the task is stored successfully into the database
        $this->assertDatabaseHas('user_tasks', ['name' => $this->task['name']]);

        // Assert that the stored task is shown in the user page successfully
        $this->get('/user-home')->assertSee($this->task['name']);
    }

    public function test_task_creation_with_assigned_users(): void
    {
        // Simulate task creation request
        $assignedUserIds = [$this->user->id, $this->admin->id]; // Example user IDs to associate with the task
        $requestData = array_merge($this->task, ['assignTo' => $assignedUserIds]);

        // Ensure the creator is included in the assigned users
        if (!in_array($this->user->id, $assignedUserIds)) {
            $requestData['assignTo'][] = $this->user->id;
        }

        // Simulate task creation request with assigned users
        $response = $this->post(route('tasks.store'), $requestData);

        // Assert that the user is redirected and task is created successfully
        $response->assertStatus(302)
            ->assertRedirect('/tasks')
            ->assertSessionHasNoErrors();

        // Retrieve the task ID from the database
        $task = Tasks::where('name', $this->task['name'])->first();
        $taskId = $task->id;

        // Assert that the task is stored successfully into the database
        $this->assertDatabaseHas('user_tasks', ['name' => $this->task['name']]);

        // Assert that the task is associated with the assigned users
        foreach ($assignedUserIds as $userId) {
            $this->assertDatabaseHas('task_user', [
                'task_id' => $taskId,
                'user_id' => $userId,
            ]);
        }

        // Assert that the stored task is shown in the user page successfully
        $this->get('/user-home')->assertSee($this->task['name']);
    }
    public function test_show_task_details(): void
    {
        // Create a task
        $task = Tasks::factory()->create();

        // Simulate accessing the show method
        $response = $this->get("/user-home/{$task->id}");

        // Assert that the task details are displayed correctly
        $response->assertStatus(200)
            ->assertSee($task->name)
            ->assertSee($task->description);
    }

    public function test_edit_task(): void
    {
        // Create a task
        $task = Tasks::factory()->create();

        // Simulate accessing the edit method
        $response = $this->get("/user-home/{$task->id}/edit");

        // Assert that the task details are loaded correctly in the edit form
        $response->assertStatus(200)
            ->assertSee($task->name)
            ->assertSee($task->description);
    }
    public function test_error_message_displayed_when_task_not_found(): void
    {
        // Simulate accessing a task that does not exist
        $nonExistentTaskId = 9999;
        $response = $this->get("/user-home/{$nonExistentTaskId}");

        // Assert that appropriate error message is displayed
        $response->assertSessionHas('error_message', 'Task not found.');
    }

    public function test_task_deletion_removes_task_from_database(): void
    {
        // Create a task
        $task = Tasks::factory()->create();

        // Simulate task deletion
        $response = $this->delete("/user-home/{$task->id}");

        // Verify deletion
        $this->assertDatabaseMissing('user_tasks', ['id' => $task->id]);

        // Assert that user is redirected after deletion
        $response->assertRedirect('/user-home');
    }
    public function test_task_update_validation(): void
    {
        // Create a task
        $task = Tasks::factory()->create();

        // Attempt to update the task with invalid data
        $response = $this->put("/user-home/{$task->id}", ['name' => '']); // Invalid: Name cannot be empty

        // Verify that the task name remains unchanged in the database
        $this->assertDatabaseHas('user_tasks', ['id' => $task->id, 'name' => $task->name]);

        // Verify that the response contains validation errors
        $response->assertSessionHasErrors(['name']);
    }
    public function test_task_deletion_cascade(): void
    {
        // Create a task
        $task = Tasks::factory()->create();

        // Attach some users to the task
        $users = User::factory()->count(3)->create();
        $task->users()->attach($users);

        // Simulate task deletion
        $response = $this->delete("/user-home/{$task->id}");

        // Verify that the task is deleted from the database
        $this->assertDatabaseMissing('user_tasks', ['id' => $task->id]);

        // Verify that related records in the task_user pivot table are also deleted
        foreach ($users as $user) {
            $this->assertDatabaseMissing('task_user', ['task_id' => $task->id, 'user_id' => $user->id]);
        }

        // Assert that user is redirected after deletion
        $response->assertRedirect('/user-home');
    }
    public function test_non_existent_task_access(): void
    {
        // Simulate accessing a task that does not exist
        $nonExistentTaskId = 9999;
        $response = $this->get("/user-home/{$nonExistentTaskId}");

        // Assert that appropriate error message is displayed
        $response->assertSessionHas('error_message', 'Task not found.');
    }

    public function test_error_message_displayed_when_task_update_validation_fails(): void
    {
        // Create a task
        $task = Tasks::factory()->create();

        // Attempt to update the task with invalid data
        $response = $this->put("/user-home/{$task->id}", ['name' => '']); // Invalid: Name cannot be empty

        // Verify that the task name remains unchanged in the database
        $this->assertDatabaseHas('user_tasks', ['id' => $task->id, 'name' => $task->name]);

        // Verify that the response contains validation errors
        $response->assertSessionHasErrors(['name']);
    }
    public function test_task_visibility(): void
    {
        // Create two users
        $user1 = $this->user;
        $user2 = $this->admin;

        // Authenticate as User 1
        $this->actingAs($user1);

        // Create a task as User 1
        $taskName = 'Task for User 1';
        $this->post(route('tasks.store'), ['name' => $taskName]);

        // Logout User 1 and authenticate as User 2
        $this->actingAs($user2);

        // Access the page where tasks are displayed for User 2
        $response = $this->get('/user-home');

        // Assert that the task created by User 1 is not visible to User 2
        $response->assertDontSee($taskName);
    }

    public function test_no_tasks_created_by_user_message_displayed()
    {
        // Arrange: Authenticate as the user
        $this->actingAs($this->user);

        // Act: Visit the user's home page
        $response = $this->get('/user-home');

        // Assert: Check that the message for no tasks created by the user is displayed
        $response->assertSee('No tasks created by you.');
    }

}