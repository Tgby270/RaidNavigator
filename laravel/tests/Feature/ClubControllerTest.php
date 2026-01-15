<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\CLUB;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClubControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the club creation page is accessible publicly (current behavior)
     */
    public function test_create_club_page_is_publicly_accessible(): void
    {
        $response = $this->get('/CreateClub');

        $response->assertStatus(200);
    }

    /**
     * Test that an authenticated user can access the creation page
     */
    public function test_authenticated_user_can_access_create_club_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/CreateClub');

        $response->assertStatus(200);
    }

    /**
     * Test club creation with valid data
     */
    public function test_club_can_be_created_with_valid_data(): void
    {
        $user = User::factory()->create();

        $clubData = [
            'CLU_NOM' => 'Club Test',
            'CLU_ADRESSE' => '123 Rue Test',
            'CLU_CODE_POSTAL' => '14000',
            'CLU_VILLE' => 'Caen',
            'CLU_CONTACT' => '0102030405',
            'USE_ID' => $user->USE_ID,
        ];

        $response = $this->actingAs($user)->post('/club/create', $clubData);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Club créé avec succès !');

        $this->assertDatabaseHas('sae_clubs', [
            'CLU_NOM' => 'Club Test',
            'CLU_VILLE' => 'Caen',
        ]);
    }

    /**
     * Test that creation fails without name
     */
    public function test_club_creation_fails_without_name(): void
    {
        $user = User::factory()->create();

        $clubData = [
            'CLU_ADRESSE' => '123 Rue Test',
            'CLU_CODE_POSTAL' => '14000',
            'CLU_VILLE' => 'Caen',
            'CLU_CONTACT' => '0102030405',
            'USE_ID' => $user->USE_ID,
        ];

        $response = $this->actingAs($user)->post('/club/create', $clubData);

        $response->assertSessionHasErrors('CLU_NOM');
    }

    /**
     * Test that creation fails without address
     */
    public function test_club_creation_fails_without_address(): void
    {
        $user = User::factory()->create();

        $clubData = [
            'CLU_NOM' => 'Club Test',
            'CLU_CODE_POSTAL' => '14000',
            'CLU_VILLE' => 'Caen',
            'CLU_CONTACT' => '0102030405',
            'USE_ID' => $user->USE_ID,
        ];

        $response = $this->actingAs($user)->post('/club/create', $clubData);

        $response->assertSessionHasErrors('CLU_ADRESSE');
    }

    /**
     * Test that creation fails with non-existent USE_ID
     */
    public function test_club_creation_fails_with_invalid_user_id(): void
    {
        $user = User::factory()->create();

        $clubData = [
            'CLU_NOM' => 'Club Test',
            'CLU_ADRESSE' => '123 Rue Test',
            'CLU_CODE_POSTAL' => '14000',
            'CLU_VILLE' => 'Caen',
            'CLU_CONTACT' => '0102030405',
            'USE_ID' => 99999, // Non-existent ID
        ];

        $response = $this->actingAs($user)->post('/club/create', $clubData);

        $response->assertSessionHasErrors('USE_ID');
    }

    /**
     * Test that postal code is limited to 10 characters
     */
    public function test_club_postal_code_has_max_length(): void
    {
        $user = User::factory()->create();

        $clubData = [
            'CLU_NOM' => 'Club Test',
            'CLU_ADRESSE' => '123 Rue Test',
            'CLU_CODE_POSTAL' => '12345678901', // 11 characters
            'CLU_VILLE' => 'Caen',
            'CLU_CONTACT' => '0102030405',
            'USE_ID' => $user->USE_ID,
        ];

        $response = $this->actingAs($user)->post('/club/create', $clubData);

        $response->assertSessionHasErrors('CLU_CODE_POSTAL');
    }

    /**
     * Test that multiple clubs can be created by the same user
     */
    public function test_multiple_clubs_can_be_created_by_same_user(): void
    {
        $user = User::factory()->create();

        $club1Data = [
            'CLU_NOM' => 'Club 1',
            'CLU_ADRESSE' => '123 Rue Test',
            'CLU_CODE_POSTAL' => '14000',
            'CLU_VILLE' => 'Caen',
            'CLU_CONTACT' => '0102030405',
            'USE_ID' => $user->USE_ID,
        ];

        $club2Data = [
            'CLU_NOM' => 'Club 2',
            'CLU_ADRESSE' => '456 Avenue Test',
            'CLU_CODE_POSTAL' => '75001',
            'CLU_VILLE' => 'Paris',
            'CLU_CONTACT' => '0607080910',
            'USE_ID' => $user->USE_ID,
        ];

        $this->actingAs($user)->post('/club/create', $club1Data);
        $this->actingAs($user)->post('/club/create', $club2Data);

        $this->assertDatabaseHas('sae_clubs', ['CLU_NOM' => 'Club 1']);
        $this->assertDatabaseHas('sae_clubs', ['CLU_NOM' => 'Club 2']);
    }
}
