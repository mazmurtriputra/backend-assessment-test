<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\User;
use App\Models\DebitCardTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Loan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => fn () => User::factory()->create(),
            'amount' => $this->faker->randomNumber(),
            'terms' => $this->faker->randomElement([6, 3]),
            'outstanding_amount' => $this->faker->randomNumber(),
            'currency_code' => $this->faker->randomElement(DebitCardTransaction::CURRENCIES),
            'processed_at' => now(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),

        ];
    }
}
