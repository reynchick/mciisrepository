<?php

namespace Database\Factories;

use App\Models\Role;
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
        $role = Role::query()->inRandomOrder()->firstOrFail();

        $localPart = Str::lower(Str::slug(fake()->unique()->userName()));
        $email     = $localPart . '@usep.edu.ph';

        $studentId = $role->name === 'Student'
            ? fake()->numberBetween(2015, (int) now()->year) . '-' . str_pad((string) fake()->numberBetween(0, 99999), 5, '0', STR_PAD_LEFT)
            : null;

        return [
            'student_id'        => $studentId,
            'first_name'        => fake()->firstName(),
            'middle_name'       => fake()->optional()->randomLetter() . '.',
            'last_name'         => fake()->lastName(),
            'contact_number'    => fake()->optional()->regexify('09\d{9}'),
            'email'             => $email,
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            $roleId = Role::query()->inRandomOrder()->value('id');
            if ($roleId) {
                $user->roles()->syncWithoutDetaching([$roleId]);
            }
        });
    }
}