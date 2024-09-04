<?php

namespace Database\Factories;

use App\Models\ScheduledRepayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduledRepaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ScheduledRepayment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'loan_id' => DebitCard::factory(),
            'amount' => $this->faker->randomFloat(2, 1, 10000),
            'currency_code' => $this->faker->currencyCode,
            'due_date' => $this->faker->dateTimeBetween('+3 month', '+6 month'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
