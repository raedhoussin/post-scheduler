<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'action' => $this->faker->randomElement([
                'Created Post',
                'Updated Post',
                'Deleted Post',
                'Changed Password',
                'Logged In',
                'Logged Out'
            ]),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}