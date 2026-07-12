<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $attributes = [
            'name' => Str::slug(fake()->unique()->words(2, true)),
            'guard_name' => 'web',
        ];

        if (config('permission.teams')) {
            $attributes[config('permission.column_names.team_foreign_key', 'team_id')] = null;
        }

        return $attributes;
    }
}
