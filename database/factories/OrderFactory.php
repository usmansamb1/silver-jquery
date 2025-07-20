<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'reference_number' => 'ORD-' . $this->faker->unique()->randomNumber(8),

            'total_amount' => $this->faker->randomFloat(2, 50, 1000),
            'subtotal' => $this->faker->randomFloat(2, 40, 800),
            'vat' => $this->faker->randomFloat(2, 10, 200),
            'pickup_location' => $this->faker->address(),
            'payment_method' => $this->faker->randomElement(['wallet', 'credit_card']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'transaction_id' => null,
            'payment_reference' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 