<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'service_type' => $this->faker->randomElement(['rfid_car', 'rfid_truck', 'oil_change', 'maintenance']),
            'base_price' => $this->faker->randomFloat(2, 50, 500),
            'vat_percentage' => 15,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 