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
        $localPart = Str::lower(Str::slug(fake()->unique()->userName()));
        $email     = $localPart . '@usep.edu.ph';

        return [
            'student_id'        => null,
            'first_name'        => fake()->firstName(),
            'middle_name'       => fake()->optional()->randomLetter() . '.',
            'last_name'         => fake()->lastName(),
            'contact_number'    => fake()->optional()->regexify('09\d{9}'),
            'email'             => $email,
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
            'profile_completed' => false,
            'first_login_completed' => false,
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

    /**
     * Attach a random default role after creating the user.
     */
    public function withRole(): static
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            $this->ensureRoleExists();
            $roleId = Role::whereIn('name', $this->defaultRoles())
                ->inRandomOrder()
                ->value('id');

            if ($roleId) {
                $user->roles()->syncWithoutDetaching([$roleId]);
            }
        });
    }

    /**
     * Create user without automatically attaching a role (default behavior).
     */
    public function withoutRoles(): static
    {
        return $this;
    }

    /**
     * Create user with Administrator role.
     */
    public function asAdministrator(): static
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            $role = Role::firstOrCreate(['name' => 'Administrator'], ['description' => 'Administrator']);
            $user->roles()->sync([$role->id]);
        });
    }

    /**
     * Create user with Faculty role.
     */
    public function asFaculty(): static
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            $role = Role::firstOrCreate(['name' => 'Faculty'], ['description' => 'Faculty']);
            $user->roles()->sync([$role->id]);
        });
    }

    /**
     * Create user with Student role.
     */
    public function asStudent(): static
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            $role = Role::firstOrCreate(['name' => 'Student'], ['description' => 'Student']);
            $user->roles()->sync([$role->id]);
        })->state(fn (array $attributes) => [
            'student_id' => fake()->numberBetween(2015, (int) now()->year) . '-' . str_pad((string) fake()->numberBetween(0, 99999), 5, '0', STR_PAD_LEFT),
        ]);
    }

    /**
     * Create user with MCIIS Staff role.
     */
    public function asMCIISStaff(): static
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            $role = Role::firstOrCreate(['name' => 'MCIIS Staff'], ['description' => 'MCIIS Staff']);
            $user->roles()->sync([$role->id]);
        });
    }

    private function defaultRoles(): array
    {
        return ['Administrator', 'MCIIS Staff', 'Faculty', 'Student'];
    }

    private function ensureRoleExists(): Role
    {
        $roles = $this->defaultRoles();

        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name], ['description' => $name]);
        }

        return Role::whereIn('name', $roles)->inRandomOrder()->first();
    }
}