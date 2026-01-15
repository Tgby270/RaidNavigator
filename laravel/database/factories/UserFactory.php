<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'USE_NOM' => fake()->lastName(),
            'USE_PRENOM' => fake()->firstName(),
            'USE_MAIL' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'USE_MDP' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'USE_DATE_NAISSANCE' => fake()->date(),
            'USE_NUM_LICENCIE' => null,
            'USE_NUM_PPS' => null,
            'USE_ADRESSE' => fake()->streetAddress(),
            'USE_TELEPHONE' => fake()->numerify('0#########'),
            'USE_CODE_POSTAL' => fake()->postcode(),
            'USE_VILLE' => fake()->city(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
