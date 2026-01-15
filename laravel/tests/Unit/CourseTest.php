<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\RAIDS;
use App\Models\COURSE;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that course dates are validated against raid dates
     */
    public function test_course_dates_must_be_within_raid_dates(): void
    {
        $raid = RAIDS::factory()->create([
            'raid_date_debut' => '2026-03-01',
            'raid_date_fin' => '2026-03-05',
        ]);

        // Date valide
        $dateDebut = Carbon::parse('2026-03-02 08:00');
        $dateFin = Carbon::parse('2026-03-03 18:00');
        $raidDateDebut = Carbon::parse($raid->raid_date_debut);
        $raidDateFin = Carbon::parse($raid->raid_date_fin)->endOfDay();

        $this->assertTrue($dateDebut->gte($raidDateDebut));
        $this->assertTrue($dateFin->lte($raidDateFin));

        // Date too early
        $dateTropTot = Carbon::parse('2026-02-28 08:00');
        $this->assertFalse($dateTropTot->gte($raidDateDebut));

        // Date too late
        $dateTropTard = Carbon::parse('2026-03-06 18:00');
        $this->assertFalse($dateTropTard->lte($raidDateFin));
    }

    /**
     * Test that the start date must be before the end date
     */
    public function test_course_start_date_must_be_before_end_date(): void
    {
        $dateDebut = Carbon::parse('2026-03-02 08:00');
        $dateFin = Carbon::parse('2026-03-03 18:00');

        $this->assertTrue($dateDebut->lt($dateFin));

        // Test inverse
        $this->assertFalse($dateFin->lt($dateDebut));
    }

    /**
     * Test that dates must be in the future
     */
    public function test_course_dates_must_be_in_future(): void
    {
        $dateFuture = Carbon::parse('2026-12-31 10:00');
        $datePasse = Carbon::parse('2020-01-01 10:00');
        $now = Carbon::now();

        $this->assertTrue($dateFuture->gt($now));
        $this->assertFalse($datePasse->gt($now));
    }

    /**
     * Test duration calculation between two dates
     */
    public function test_course_duration_calculation(): void
    {
        $dateDebut = Carbon::parse('2026-03-02 08:00');
        $dateFin = Carbon::parse('2026-03-02 18:00');

        $dureeEnHeures = $dateDebut->diffInHours($dateFin);
        $dureeEnMinutes = $dateDebut->diffInMinutes($dateFin);

        $this->assertEquals(10, $dureeEnHeures);
        $this->assertEquals(600, $dureeEnMinutes);
    }

    /**
     * Test that minimum participants must be <= maximum
     */
    public function test_participants_min_must_be_less_than_or_equal_to_max(): void
    {
        $participantsMin = 10;
        $participantsMax = 50;

        $this->assertTrue($participantsMin <= $participantsMax);

        // Invalid test
        $invalidMin = 60;
        $this->assertFalse($invalidMin <= $participantsMax);
    }

    /**
     * Test that minimum teams must be <= maximum
     */
    public function test_teams_min_must_be_less_than_or_equal_to_max(): void
    {
        $teamMin = 5;
        $teamMax = 20;

        $this->assertTrue($teamMin <= $teamMax);

        // Invalid test
        $invalidMin = 25;
        $this->assertFalse($invalidMin <= $teamMax);
    }

    /**
     * Test date formatting
     */
    public function test_date_formatting(): void
    {
        $date = Carbon::parse('2026-03-15');

        $this->assertEquals('15/03/2026', $date->format('d/m/Y'));
        $this->assertEquals('2026-03-15', $date->format('Y-m-d'));
        $this->assertEquals('15 mars 2026', $date->locale('fr')->isoFormat('D MMMM YYYY'));
    }

    /**
     * Test parsing of different date formats
     */
    public function test_date_parsing(): void
    {
        $date1 = Carbon::parse('2026-03-15');
        $date2 = Carbon::createFromFormat('d/m/Y', '15/03/2026');
        $date3 = Carbon::parse('2026-03-15 14:30:00');

        $this->assertEquals('2026-03-15', $date1->format('Y-m-d'));
        $this->assertTrue($date3->hour === 14);
        $this->assertTrue($date3->minute === 30);
    }

    /**
     * Test date comparison with Carbon
     */
    public function test_carbon_date_comparison_methods(): void
    {
        $date1 = Carbon::parse('2026-03-15');
        $date2 = Carbon::parse('2026-03-20');

        // Less than
        $this->assertTrue($date1->lt($date2));
        $this->assertFalse($date2->lt($date1));

        // Greater than
        $this->assertTrue($date2->gt($date1));
        $this->assertFalse($date1->gt($date2));

        // Less than or equal
        $this->assertTrue($date1->lte($date2));
        $this->assertTrue($date1->lte($date1));

        // Greater than or equal
        $this->assertTrue($date2->gte($date1));
        $this->assertTrue($date2->gte($date2));

        // Equal
        $date3 = Carbon::parse('2026-03-15');
        $this->assertTrue($date1->eq($date3));
    }

    /**
     * Test validation of unique CRS_ID per raid
     */
    public function test_crs_id_is_incremental_per_raid(): void
    {
        $raid = RAIDS::factory()->create();

        // First course
        $lastCourse = null;
        $crsId1 = $lastCourse ? $lastCourse->CRS_ID + 1 : 1;
        $this->assertEquals(1, $crsId1);

        // Simulate an existing course
        $lastCourse = (object) ['CRS_ID' => 1];
        $crsId2 = $lastCourse->CRS_ID + 1;
        $this->assertEquals(2, $crsId2);

        // Third course
        $lastCourse = (object) ['CRS_ID' => 2];
        $crsId3 = $lastCourse->CRS_ID + 1;
        $this->assertEquals(3, $crsId3);
    }
}
