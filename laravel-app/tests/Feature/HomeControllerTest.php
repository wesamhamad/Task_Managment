<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class HomeControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    private $user;
    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();
        $this->user = $this->createUser();
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
        return $this->get('/');
    }


    public function test_index_method_redirects_guest_users_to_welcome_page()
    {
        $this->actingAsGuest('web')->assertStatus(200);
    }


    public function test_index_method_returns_user_home_view_for_authenticated_users()
    {
        $this->actingAs($this->user);

        $response = $this->get('/user-home'); // Update route
        $response->assertViewIs('user.home');
    }

    public function test_index_method_returns_admin_home_view_for_authenticated_admin_users()
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin-home'); // Update route
        $response->assertViewIs('admin.adminhome');
    }

    public function test_unauthnticated_acess(): void
    {
        $this->actingAs($this->user);
        // Make a request to the admin-home route
        $response = $this->get('/admin-home');

        // Assert that the response has a status code of 401 (Unauthorized)
        $response->assertStatus(401);
    }

    public function test_authenticated_user_redirected_to_login_page_after_logout_and_access_home_page()
    {
        // Act as the logged-in user
        $this->actingAs($this->user);

        // Log the user out using Laravel's logout feature
        $this->post('/logout');

        // Make a request to a specific page
        $response = $this->get('/user-home');

        // Assert that the response redirects to the welcome page
        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_redirected_to_login_page_after_logout_and_access_home_page()
    {
        // Act as the logged-in user
        $this->actingAs($this->admin);

        // Log the user out using Laravel's logout feature
        $this->post('/logout');

        // Make a request to a specific page
        $response = $this->get('/admin-home');

        // Assert that the response redirects to the welcome page
        $response->assertRedirect('/login');
    }
    public function test_error_handling_invalid_route()
    {
        $response = $this->get('/invalid-route');
        $response->assertStatus(404); // Not Found
    }
    public function test_session_management()
    {
        // Test if session persists after login
        $this->actingAs($this->user);
        $response = $this->get('/user-home');
        $response->assertStatus(200);
        $this->assertAuthenticated(); // Check if user is authenticated

        // Test if session is cleared after logout
        $this->post('/logout');
        $this->assertGuest(); // Check if user is logged out
    }

}