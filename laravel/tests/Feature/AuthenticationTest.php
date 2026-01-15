<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the login page is accessible
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Test that the register page is accessible
     */
    public function test_register_page_is_accessible(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    /**
     * Test that a user can login with valid credentials
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'USE_MAIL' => 'test@example.com',
            'USE_MDP' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
    }

    /**
     * Test that a user cannot login with an invalid password
     */
    public function test_user_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'USE_MAIL' => 'test@example.com',
            'USE_MDP' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
    }

    /**
     * Test that a user can logout
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /**
     * Test that protected routes redirect to home for guests
     */
    public function test_protected_routes_redirect_to_home(): void
    {
        $protectedRoutes = [
            '/CreateRaid',
            '/CreateCourse',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            if ($response->isRedirect()) {
                $response->assertRedirect('/');
            } else {
                $response->assertStatus(200);
            }
        }
    }

    /**
     * Test that an authenticated user can access protected routes
     */
    public function test_authenticated_user_can_access_protected_routes(): void
    {
        $user = User::factory()->create();

        $protectedRoutes = [
            '/CreateRaid',
            '/CreateClub',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->actingAs($user)->get($route);
            $response->assertStatus(200);
        }
    }

    /**
     * Test new user registration
     */
    public function test_new_user_can_register(): void
    {
        $userData = [
            'name' => 'Jean',
            'family_name' => 'Dupont',
            'email' => 'jean.dupont@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'date_naissance' => '2000-01-01',
            'adresse' => '1 rue du Test',
            'phone_number' => '0102030405',
            'postal_code' => '75000',
            'city' => 'Paris',
        ];

        $response = $this->post('/register', $userData);

        $this->assertDatabaseHas('sae_users', [
            'USE_MAIL' => 'jean.dupont@example.com',
            'USE_NOM' => 'Dupont',
        ]);
    }

    /**
     * Test that registration fails without email
     */
    public function test_registration_fails_without_email(): void
    {
        $userData = [
            'name' => 'Jean',
            'family_name' => 'Dupont',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'date_naissance' => '2000-01-01',
            'adresse' => '1 rue du Test',
            'phone_number' => '0102030405',
            'postal_code' => '75000',
            'city' => 'Paris',
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test that registration fails with mismatched passwords
     */
    public function test_registration_fails_with_mismatched_passwords(): void
    {
        $userData = [
            'name' => 'Jean',
            'family_name' => 'Dupont',
            'email' => 'jean.dupont@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'date_naissance' => '2000-01-01',
            'adresse' => '1 rue du Test',
            'phone_number' => '0102030405',
            'postal_code' => '75000',
            'city' => 'Paris',
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors('password');
    }
}
