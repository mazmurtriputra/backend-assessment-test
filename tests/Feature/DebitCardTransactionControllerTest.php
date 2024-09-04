<?php

namespace Tests\Feature;

use App\Models\DebitCard;
use App\Models\User;
use Database\Factories\DebitCardTransactionFactory;
use App\Models\DebitCardTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DebitCardTransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected DebitCard $debitCard;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->debitCard = DebitCard::factory()->create([
            'user_id' => $this->user->id
        ]);
        Passport::actingAs($this->user);
    }

    public function testCustomerCanSeeAListOfDebitCardTransactions()
    {
        $customer = User::factory()->create();
        $debitCard = DebitCard::factory()->create(['user_id' => $customer->id]);
        DebitCardTransactionFactory::factory()->count(3)->create([
            'debit_card_id' => $debitCard->id,
            'amount' => 1000,
            'currency_code' => 'USD',
        ]);
    
        $response = $this->actingAs($customer)->get('api/debit-card-transactions');
    
        $response->assertStatus(200)
                 ->assertJsonCount(3) // assuming JSON response with a count of 3 transactions
                 ->assertJsonFragment(['amount' => 1000, 'currency_code' => 'USD']);
    }

    public function testCustomerCannotSeeAListOfDebitCardTransactionsOfOtherCustomerDebitCard()
    {
        // get /debit-card-transactions
    }

    public function testCustomerCanCreateADebitCardTransaction()
    {
        // post /debit-card-transactions
    }

    public function testCustomerCannotCreateADebitCardTransactionToOtherCustomerDebitCard()
    {
        // post /debit-card-transactions
    }

    public function testCustomerCanSeeADebitCardTransaction()
    {
        // get /debit-card-transactions/{debitCardTransaction}
    }

    public function testCustomerCannotSeeADebitCardTransactionAttachedToOtherCustomerDebitCard()
    {
        // get /debit-card-transactions/{debitCardTransaction}
    }

    // Extra bonus for extra tests :)
}
