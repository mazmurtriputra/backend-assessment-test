<?php

namespace Database\Factories;

use App\Models\DebitCard;
use App\Models\DebitCardTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class DebitCardTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DebitCardTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'debit_card_id' => DebitCard::factory(),
            'amount' => $this->faker->randomFloat(2, 1, 10000),
            'currency_code' => $this->faker->currencyCode,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
