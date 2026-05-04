<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Operator>
 */
class OperatorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->unique()->numerify('52175########'),
            'role' => $this->faker->randomElement(['admin', 'supervisor', 'operador']),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'max_concurrent_chats' => $this->faker->numberBetween(3, 10),
            'current_chats_count' => 0,
            'status' => $this->faker->randomElement(['available', 'busy', 'away', 'offline']),
            'last_activity_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
