<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tasks>
 */
class TasksFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
            'deadlin' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['Pending', 'In Progress', 'Completed']),
            'description' => $this->faker->paragraph,
            'created_by' => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}