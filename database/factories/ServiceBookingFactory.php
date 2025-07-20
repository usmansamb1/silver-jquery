<?php

namespace Database\Factories;

use App\Models\ServiceBooking;
use App\Models\User;
use App\Models\Service;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceBookingFactory extends Factory
{
    protected $model = ServiceBooking::class;

    public function definition()
    {
        return [
            'reference_number' => 'SB-' . $this->faker->unique()->randomNumber(8),
            'user_id' => User::factory(),
            'order_id' => Order::factory(),
            'service_id' => Service::factory(),
            'service_type' => $this->faker->randomElement(['rfid_car', 'rfid_truck', 'oil_change']),
            'vehicle_make' => $this->faker->company(),
            'vehicle_manufacturer' => $this->faker->company(),
            'vehicle_model' => $this->faker->word(),
            'vehicle_year' => $this->faker->year(),
            'plate_number' => $this->faker->regexify('[A-Z]{3}[0-9]{3}'),
            'refule_amount' => $this->faker->randomFloat(2, 0, 200),
            'base_price' => $this->faker->randomFloat(2, 50, 500),
            'vat_amount' => $this->faker->randomFloat(2, 5, 50),
            'total_amount' => $this->faker->randomFloat(2, 100, 800),
            'payment_status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'payment_method' => $this->faker->randomElement(['wallet', 'credit_card']),
            'status' => $this->faker->randomElement(['pending', 'approved', 'completed', 'cancelled']),
            'delivery_status' => $this->faker->randomElement(['pending', 'assigned', 'in_progress', 'completed']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 