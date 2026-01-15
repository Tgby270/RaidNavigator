<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\RAIDS;
use App\Models\COURSE;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the course creation page requires authentication
     */
    public function test_create_course_page_requires_authentication(): void
    {
        $raid = RAIDS::factory()->create();

        $response = $this->get("/raids/{$raid->RAID_ID}/courses/create");

        $response->assertRedirect('/');
    }

    /**
     * Test that an authenticated user can access the creation page
     */
    public function test_authenticated_user_can_access_create_course_page(): void
    {
        $user = User::factory()->create();
        $raid = RAIDS::factory()->create();

        $response = $this->actingAs($user)->get("/raids/{$raid->RAID_ID}/courses/create");

        $response->assertStatus(200);
    }

    /**
     * Test that course dates must be within raid dates
     */
    public function test_course_dates_must_be_within_raid_dates(): void
    {
        $user = User::factory()->create();
        $raid = RAIDS::factory()->create([
            'raid_date_debut' => '2026-03-01',
            'raid_date_fin' => '2026-03-05',
        ]);

        // Test with start date too early
        $courseData = [
            'title' => 'Course Test',
            'type' => 'loisirs-mixte',
            'duration' => 120,
            'dateB' => '2026-02-28',
            'hourB' => '08:00',
            'dateE' => '2026-03-02',
            'hourE' => '18:00',
            'participantsMin' => 10,
            'participantsMax' => 50,
            'participantNbByTeam' => 5,
            'teamMin' => 2,
            'teamMax' => 10,
            'use_id' => $user->USE_ID,
            'mealPrice' => 15,
            'priceUnder18' => 20,
            'priceOver18' => 30,
            'discount' => 10,
            'difficulte' => 'facile',
        ];

        $response = $this->actingAs($user)->post("/courses/{$raid->RAID_ID}/create", $courseData);

        $response->assertSessionHasErrors('dateB');
    }

    /**
     * Test that the end date cannot be after the raid end date
     */
    public function test_course_end_date_cannot_be_after_raid_end_date(): void
    {
        $user = User::factory()->create();
        $raid = RAIDS::factory()->create([
            'raid_date_debut' => '2026-03-01',
            'raid_date_fin' => '2026-03-05',
        ]);

        $courseData = [
            'title' => 'Course Test',
            'type' => 'loisirs-mixte',
            'duration' => 120,
            'dateB' => '2026-03-02',
            'hourB' => '08:00',
            'dateE' => '2026-03-06', // After the raid end date
            'hourE' => '18:00',
            'participantsMin' => 10,
            'participantsMax' => 50,
            'participantNbByTeam' => 5,
            'teamMin' => 2,
            'teamMax' => 10,
            'use_id' => $user->USE_ID,
            'mealPrice' => 15,
            'priceUnder18' => 20,
            'priceOver18' => 30,
            'discount' => 10,
            'difficulte' => 'facile',
        ];

        $response = $this->actingAs($user)->post("/courses/{$raid->RAID_ID}/create", $courseData);

        $response->assertSessionHasErrors('dateE');
    }

    /**
     * Test that the start date must be before the end date
     */
    public function test_course_start_date_must_be_before_end_date(): void
    {
        $user = User::factory()->create();
        $raid = RAIDS::factory()->create([
            'raid_date_debut' => '2026-03-01',
            'raid_date_fin' => '2026-03-05',
        ]);

        $courseData = [
            'title' => 'Course Test',
            'type' => 'loisirs-mixte',
            'duration' => 120,
            'dateB' => '2026-03-04',
            'hourB' => '18:00',
            'dateE' => '2026-03-04',
            'hourE' => '08:00', // Before the start time
            'participantsMin' => 10,
            'participantsMax' => 50,
            'participantNbByTeam' => 5,
            'teamMin' => 2,
            'teamMax' => 10,
            'use_id' => $user->USE_ID,
            'mealPrice' => 15,
            'priceUnder18' => 20,
            'priceOver18' => 30,
            'discount' => 10,
            'difficulte' => 'facile',
        ];

        $response = $this->actingAs($user)->post("/courses/{$raid->RAID_ID}/create", $courseData);

        $response->assertSessionHasErrors('dateB');
    }

    /**
     * Test that minimum participants must be <= maximum
     */
    public function test_participants_min_must_be_less_than_or_equal_to_max(): void
    {
        $user = User::factory()->create();
        $raid = RAIDS::factory()->create([
            'raid_date_debut' => '2026-03-01',
            'raid_date_fin' => '2026-03-05',
        ]);

        $courseData = [
            'title' => 'Course Test',
            'type' => 'loisirs-mixte',
            'duration' => 120,
            'dateB' => '2026-03-02',
            'hourB' => '08:00',
            'dateE' => '2026-03-03',
            'hourE' => '18:00',
            'participantsMin' => 60, // Greater than max
            'participantsMax' => 50,
            'participantNbByTeam' => 5,
            'teamMin' => 2,
            'teamMax' => 10,
            'use_id' => $user->USE_ID,
            'mealPrice' => 15,
            'priceUnder18' => 20,
            'priceOver18' => 30,
            'discount' => 10,
            'difficulte' => 'facile',
        ];

        $response = $this->actingAs($user)->post("/courses/{$raid->RAID_ID}/create", $courseData);

        $response->assertSessionHasErrors('participantsMin');
    }

    /**
     * Test that minimum teams must be <= maximum
     */
    public function test_teams_min_must_be_less_than_or_equal_to_max(): void
    {
        $user = User::factory()->create();
        $raid = RAIDS::factory()->create([
            'raid_date_debut' => '2026-03-01',
            'raid_date_fin' => '2026-03-05',
        ]);

        $courseData = [
            'title' => 'Course Test',
            'type' => 'loisirs-mixte',
            'duration' => 120,
            'dateB' => '2026-03-02',
            'hourB' => '08:00',
            'dateE' => '2026-03-03',
            'hourE' => '18:00',
            'participantsMin' => 10,
            'participantsMax' => 50,
            'participantNbByTeam' => 5,
            'teamMin' => 15, // Greater than max
            'teamMax' => 10,
            'use_id' => $user->USE_ID,
            'mealPrice' => 15,
            'priceUnder18' => 20,
            'priceOver18' => 30,
            'discount' => 10,
            'difficulte' => 'facile',
        ];

        $response = $this->actingAs($user)->post("/courses/{$raid->RAID_ID}/create", $courseData);

        $response->assertSessionHasErrors('teamMin');
    }

    /**
     * Test that creation fails if the raid doesn't exist
     */
    public function test_course_creation_fails_with_invalid_raid_id(): void
    {
        $user = User::factory()->create();

        $courseData = [
            'title' => 'Course Test',
            'type' => 'loisirs-mixte',
            'duration' => 120,
            'dateB' => '2026-03-02',
            'hourB' => '08:00',
            'dateE' => '2026-03-03',
            'hourE' => '18:00',
            'participantsMin' => 10,
            'participantsMax' => 50,
            'participantNbByTeam' => 5,
            'teamMin' => 2,
            'teamMax' => 10,
            'use_id' => $user->USE_ID,
            'mealPrice' => 15,
            'priceUnder18' => 20,
            'priceOver18' => 30,
            'discount' => 10,
            'difficulte' => 'facile',
        ];

        $response = $this->actingAs($user)->post("/courses/99999/create", $courseData);

        $response->assertSessionHasErrors('error');
    }

    /**
     * Test that all required fields are validated
     */
    public function test_course_creation_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $raid = RAIDS::factory()->create();

        $response = $this->actingAs($user)->post("/courses/{$raid->RAID_ID}/create", []);

        $response->assertSessionHasErrors([
            'title',
            'type',
            'duration',
            'dateB',
            'hourB',
            'dateE',
            'hourE',
            'participantsMin',
            'participantsMax',
            'participantNbByTeam',
            'teamMin',
            'teamMax',
            'use_id',
            'difficulte',
        ]);
    }
}
