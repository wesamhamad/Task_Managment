<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Tasks;
use App\Models\User;
use Laravel\Dusk\Browser;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $admin;
    private $task;
    private $project;
    protected function setUp(): void
    {
        parent::setUp();

        // Create admin, user, and task
        $this->admin = User::factory()->create(['usertype' => 'admin']);
        $this->user = User::factory()->create(['usertype' => 'user']);

        // Authenticate as admin user
        $this->actingAs($this->admin);

        // Create a project associated with the authenticated user
        $this->project = Project::factory()->create(['created_by' => $this->admin->id]);
        // Create a task associated with the regular user
        $this->task = Tasks::factory()->create(['created_by' => $this->user->id]);
    }

    public function test_projects_index_returns_view_with_projects()
    {
        // Act: Visit the projects index route
        $response = $this->get(route('projects.index'));

        // Assert: Check that the view is returned and contains the project
        $response->assertViewIs('admin.adminhome');
    }

    public function test_no_projects_assigned_to_user_message_displayed()
    {
        // Arrange: Authenticate as the user
        $this->actingAs($this->user);

        // Act: Visit the user's home page
        $response = $this->get('/user-home');

        // Assert: Check that the message for no projects assigned is displayed
        $response->assertSee('No projects assigned to you.');
    }

    public function test_can_access_create_project_form()
    {
        // Act: Visit the create project route
        $response = $this->get(route('projects.create'));

        // Assert: Check that the create project form is returned
        $response->assertStatus(200)
            ->assertSee('Create Project');
    }

    public function test_project_creation()
    {
        // Arrange: Prepare project data
        $projectData = [
            'title' => 'New Project',
            'description' => 'Description of the new project',
            'tasks' => [$this->task->id], // Assuming you have tasks associated with the project
        ];

        // Act: Submit the form to store the new project
        $response = $this->post(route('projects.store'), $projectData);

        // Assert: Check that the project is stored successfully
        $response->assertStatus(302)
            ->assertSessionHas('success_message', 'Project created successfully!');

        // Assert that the project is stored in the database
        $this->assertDatabaseHas('projects', [
            'title' => 'New Project',
            'created_by' => $this->admin->id,
        ]);

        // Assert that the stored project is shown in the projects index page successfully
        $this->get(route('projects.index'))->assertSee('New Project');
    }

    public function test_project_validation_rules(): void
    {
        // Simulate project creation request with invalid data
        $response = $this->post(route('projects.store'), [
            // Missing required 'title' field
            'description' => 'Description of the new project',
            'tasks' => [], // Assuming no tasks associated with the project initially
        ]);

        // Assert that the response contains validation errors
        $response->assertSessionHasErrors(['title']);
    }

    public function test_project_added_to_project_task_table_with_associated_tasks()
    {
        // Arrange: Prepare project data with associated tasks
        $projectData = [
            'title' => 'New Project',
            'description' => 'Description of the new project',
            'tasks' => [$this->task->id], // Assuming you have tasks associated with the project
        ];

        // Act: Submit the form to store the new project
        $this->post(route('projects.store'), $projectData);

        // Assert: Check that the project is stored in the project_task table with associated tasks
        $this->assertDatabaseHas('project_task', [
            'project_id' => Project::where('title', 'New Project')->first()->id,
            'task_id' => $this->task->id,
        ]);
    }

    public function test_a_project_assigned_to_users_are_displayed()
    {
        // Create three users
        $user1 = $this->admin;
        $user2 = User::factory()->create(['usertype' => 'user']);
        $user3 = User::factory()->create(['usertype' => 'user']);

        // Create a project with a task assigned to the first two users
        $project = $this->project;
        $task = $this->task;
        $project->tasks()->save($task);
        $task->users()->attach([$user1->id, $user2->id]);

        // Authenticate as the first user
        $this->actingAs($user1);

        // Visit the first user's home page
        $response1 = $this->get('/user-home');

        // Assert that the project is displayed for the first user
        $response1->assertSee($project->title);

        // Authenticate as the second user
        $this->actingAs($user2);

        // Visit the second user's home page
        $response2 = $this->get('/user-home');

        // Assert that the project is displayed for the second user
        $response2->assertSee($project->title);

        // Authenticate as the third user
        $this->actingAs($user3);

        // Visit the third user's home page
        $response3 = $this->get('/user-home');

        // Assert that the project is not displayed for the third user
        $response3->assertDontSee($project->title);
    }


    public function test_project_not_assigned_to_user_is_not_displayed()
    {
        // Create a user
        $user = $this->user;

        // Create a project with a task not assigned to the user
        $project = Project::factory()->create(['created_by' => $this->admin->id]);
        $task = Tasks::factory()->create(['created_by' => $this->admin->id]);
        $project->tasks()->save($task);

        // Authenticate as the user
        $this->actingAs($user);

        // Visit the user's home page
        $response = $this->get('/user-home');

        // Assert that the project is not displayed
        $response->assertDontSee($project->title);
    }

    public function test_projects_created_by_admin_are_displayed_in_admin_home()
    {
        // Authenticate as the admin
        $this->actingAs($this->admin);

        // Create another admin
        $otherAdmin = User::factory()->create(['usertype' => 'admin']);

        // Create a project associated with the other admin
        $projectForOtherAdmin = Project::factory()->create(['created_by' => $otherAdmin->id]);

        // Visit the admin home page
        $response = $this->get('/admin-home');

        // Assert that the project created by the admin is displayed
        $response->assertSee($this->project->title);

        // Assert that the project created by the other admin is not displayed
        $response->assertDontSee($projectForOtherAdmin->title);
    }

    public function test_can_delete_project()
    {
        // Act: Submit the form to delete the project
        $response = $this->delete(route('projects.destroy', $this->project->id), ['project_name' => $this->project->title]);

        // Assert: Check that the project is deleted successfully
        $response->assertStatus(302) // Assuming the response is a redirect after deletion
            ->assertSessionHas('success_message', 'Project deleted successfully!');

        // Assert that the project is no longer in the database
        $this->assertDatabaseMissing('projects', ['id' => $this->project->id]);

        // Assert that user is redirected after deletion
        $response->assertRedirect(route('projects.index'));
    }

    public function test_delete_cascade()
    {
        // Create a project with associated tasks
        $project = $this->project;
        $task1 = Tasks::factory()->create(['created_by' => $this->admin->id]);
        $task2 = Tasks::factory()->create(['created_by' => $this->admin->id]);

        // Attach tasks to the project
        $project->tasks()->attach([$task1->id, $task2->id]);

        // Act: Delete the project
        $response = $this->delete(route('projects.destroy', $project->id), ['project_name' => $project->title]);

        // Assert: Check that the project is deleted successfully
        $response->assertStatus(302)
            ->assertSessionHas('success_message', 'Project deleted successfully!');

        // Assert that the project is no longer in the database
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);

        // Assert that associated records in project_task table are also deleted
        $this->assertDatabaseMissing('project_task', ['project_id' => $project->id]);
    }


    public function test_error_message_not_displayed_when_confirmation_fails_with_matching_name()
    {
        // Prepare project data
        $project = $this->project;

        // Simulate confirmation request with matching project name
        $response = $this->post(route('projects.confirm-destroy', $project->id), [
            'project_name' => $project->title, // Pass correct project name
        ]);

        // Assert that no error message is flashed
        $response->assertStatus(302)
            ->assertSessionMissing('error_message');
    }

    public function test_error_message_displayed_when_confirmation_fails_with_non_matching_name()
    {
        // Prepare project data
        $project = $this->project;

        // Simulate confirmation request with incorrect project name
        $response = $this->post(route('projects.confirm-destroy', $project->id), [
            'project_name' => 'Incorrect Project Name', // Pass incorrect project name
        ]);

        // Assert that appropriate error message is flashed
        $response->assertStatus(302)
            ->assertSessionHas('error_message', 'The selected project name is incorrect.');
    }

    public function test_project_not_shown_for_assigned_users_after_deletion()
    {
        // Create a project with associated tasks
        $project = $this->project;
        $task1 = Tasks::factory()->create(['created_by' => $this->admin->id]);
        $task2 = Tasks::factory()->create(['created_by' => $this->admin->id]);

        // Assign users to tasks
        $assignedUsers = [$this->user->id, $this->admin->id];
        $task1->users()->attach($assignedUsers);

        // Attach tasks to the project
        $project->tasks()->attach([$task1->id, $task2->id]);

        // Act: Delete the project
        $response = $this->delete(route('projects.destroy', $project->id), ['project_name' => $project->title]);

        // Assert: Check that the project is deleted successfully
        $response->assertStatus(302)
            ->assertSessionHas('success_message', 'Project deleted successfully!');

        // Assert that the project is no longer in the database
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);

        // Assert that associated records in project_task table are also deleted
        $this->assertDatabaseMissing('project_task', ['project_id' => $project->id]);

        // Assert that each project associated with users is not shown to those users
        foreach ($assignedUsers as $userId) {
            foreach ($project->tasks as $task) {
                // Check if the project title is not visible in the user's home page
                $response = $this->actingAs(User::find($userId))->get('/user-home');
                $response->assertDontSee($project->title);
            }
        }

    }




}