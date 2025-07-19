<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WalletFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Wallet::class;

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
            'balance' => $this->faker->randomFloat(2, 0, 10000),
        ];
    }

    /**
     * Configure the factory to create a wallet with zero balance.
     *
     * @return $this
     */
    public function empty()
    {
        return $this->state(function (array $attributes) {
            return [
                'balance' => 0,
            ];
        });
    }
} 