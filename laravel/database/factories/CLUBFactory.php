<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CLUB>
 */
class CLUBFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'USE_ID' => User::factory(),
            'CLU_NOM' => fake()->company(),
            'CLU_ADRESSE' => fake()->streetAddress(),
            'CLU_VILLE' => fake()->city(),
            'CLU_CODE_POSTAL' => fake()->postcode(),
            'CLU_CONTACT' => fake()->numerify('0#########'),
        ];
    }
}
