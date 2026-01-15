<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\CLUB;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClubTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the Club model can be created
     */
    public function test_club_can_be_created(): void
    {
        $user = User::factory()->create();
        
        $club = CLUB::create([
            'CLU_NOM' => 'Club Test',
            'CLU_ADRESSE' => '123 Rue Test',
            'CLU_CODE_POSTAL' => '14000',
            'CLU_VILLE' => 'Caen',
            'CLU_CONTACT' => '0102030405',
            'USE_ID' => $user->USE_ID,
        ]);

        $this->assertInstanceOf(CLUB::class, $club);
        $this->assertEquals('Club Test', $club->CLU_NOM);
        $this->assertEquals('14000', $club->CLU_CODE_POSTAL);
    }

    /**
     * Test that the club name is required
     */
    public function test_club_name_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        CLUB::create([
            'CLU_ADRESSE' => '123 Rue Test',
            'CLU_CODE_POSTAL' => '14000',
            'CLU_VILLE' => 'Caen',
            'CLU_CONTACT' => '0102030405',
        ]);
    }

    /**
     * Test the relationship between Club and User
     */
    public function test_club_belongs_to_user(): void
    {
        $user = User::factory()->create();
        
        $club = CLUB::create([
            'CLU_NOM' => 'Club Test',
            'CLU_ADRESSE' => '123 Rue Test',
            'CLU_CODE_POSTAL' => '14000',
            'CLU_VILLE' => 'Caen',
            'CLU_CONTACT' => '0102030405',
            'USE_ID' => $user->USE_ID,
        ]);

        $this->assertEquals($user->USE_ID, $club->USE_ID);
    }

    /**
     * Test that multiple clubs can be created
     */
    public function test_multiple_clubs_can_be_created(): void
    {
        $user = User::factory()->create();
        
        $club1 = CLUB::create([
            'CLU_NOM' => 'Club 1',
            'CLU_ADRESSE' => '123 Rue Test',
            'CLU_CODE_POSTAL' => '14000',
            'CLU_VILLE' => 'Caen',
            'CLU_CONTACT' => '0102030405',
            'USE_ID' => $user->USE_ID,
        ]);

        $club2 = CLUB::create([
            'CLU_NOM' => 'Club 2',
            'CLU_ADRESSE' => '456 Avenue Test',
            'CLU_CODE_POSTAL' => '75001',
            'CLU_VILLE' => 'Paris',
            'CLU_CONTACT' => '0607080910',
            'USE_ID' => $user->USE_ID,
        ]);

        $this->assertCount(2, CLUB::all());
        $this->assertNotEquals($club1->CLU_ID, $club2->CLU_ID);
    }

    /**
     * Test that club data can be updated
     */
    public function test_club_can_be_updated(): void
    {
        $user = User::factory()->create();
        
        $club = CLUB::create([
            'CLU_NOM' => 'Club Test',
            'CLU_ADRESSE' => '123 Rue Test',
            'CLU_CODE_POSTAL' => '14000',
            'CLU_VILLE' => 'Caen',
            'CLU_CONTACT' => '0102030405',
            'USE_ID' => $user->USE_ID,
        ]);

        $club->update([
            'CLU_NOM' => 'Club Modifié',
            'CLU_VILLE' => 'Bayeux',
        ]);

        $this->assertEquals('Club Modifié', $club->fresh()->CLU_NOM);
        $this->assertEquals('Bayeux', $club->fresh()->CLU_VILLE);
    }

    /**
     * Test that the club can be deleted
     */
    public function test_club_can_be_deleted(): void
    {
        $user = User::factory()->create();
        
        $club = CLUB::create([
            'CLU_NOM' => 'Club Test',
            'CLU_ADRESSE' => '123 Rue Test',
            'CLU_CODE_POSTAL' => '14000',
            'CLU_VILLE' => 'Caen',
            'CLU_CONTACT' => '0102030405',
            'USE_ID' => $user->USE_ID,
        ]);

        $clubId = $club->CLU_ID;
        $club->delete();

        $this->assertNull(CLUB::find($clubId));
    }
}
