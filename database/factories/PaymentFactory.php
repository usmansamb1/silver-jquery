<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'amount' => $this->faker->randomFloat(2, 10, 10000),
            'payment_type' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'bank_guarantee', 'bank_lc']),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'notes' => $this->faker->paragraph(),
            'files' => null,
            'transaction_id' => $this->faker->uuid,
        ];
    }

    /**
     * Configure the factory to create a pending payment.
     *
     * @return $this
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    /**
     * Configure the factory to create an approved payment.
     *
     * @return $this
     */
    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
            ];
        });
    }

    /**
     * Configure the factory to create a rejected payment.
     *
     * @return $this
     */
    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
            ];
        });
    }
} 