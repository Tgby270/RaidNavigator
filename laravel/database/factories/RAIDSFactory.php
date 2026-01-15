<?php

namespace Database\Factories;

use App\Models\CLUB;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RAIDS>
 */
class RAIDSFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::now()->addDays(10);
        $end = Carbon::now()->addDays(12);

        return [
            'USE_ID' => User::factory(),
            'CLU_ID' => CLUB::factory(),
            'raid_nom' => 'Raid ' . fake()->word(),
            'raid_date_debut' => $start,
            'raid_date_fin' => $end,
            'raid_contact' => fake()->numerify('0#########'),
            'raid_site_web' => fake()->url(),
            'raid_lieu' => fake()->city(),
            'raid_image' => null,
            'date_fin_inscription' => $start->copy()->subDays(2),
            'date_debut_inscription' => Carbon::now()->addDays(1),
            'nombre_de_courses' => 1,
        ];
    }
}
