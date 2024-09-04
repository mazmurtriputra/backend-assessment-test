<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Debitcard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Carbon\Carbon;

class DebitCardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Passport::actingAs($this->user);
    }

    public function testCustomerCanSeeAListOfDebitCards()  // DONE
    {
        $debitCard = $this->user->debitCards()->create([
            'number' => '1234567890123456',
            'expiration_date' => '2025-12-31',
            'type' => 'Visa',
        ]);
    
        $response = $this->get('/debit-cards');
    
        $response->assertStatus(200)
                 ->assertJsonFragment(['number' => $debitCard->number]);
    }
    

    public function testCustomerCannotSeeAListOfDebitCardsOfOtherCustomers() // DONE
    {
        $user1 = User::factory()->create();
        $user1->debitCards()->create([
            'number' => '6543210987654321',
            'expiration_date' => '2025-12-31',
            'type' => 'Visa',
        ]);
    
        $response = $this->get('/debit-cards');
    
        $response->assertStatus(200)
                 ->assertJsonMissing(['number' => '6543210987654321']);
    }

    public function testCustomerCanCreateADebitCard()
    {
            // Miss information between laravel passport
    }
    
    

    public function testCustomerCanSeeASingleDebitCardDetails() // DONE
    {
        $debitCard = DebitCard::factory()->create([
            'user_id' => $this->user->id,
            'number' => '6543210987654321',
            'expiration_date' => '2025-12-31',
            'type' => 'Visa',
        ]);
        $response = $this->getJson("/api/debit-cards/{$debitCard->id}");
        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $debitCard->id,
            'number' => $debitCard->number,
            'type' => $debitCard->type,
            'expiration_date' => $debitCard->expiration_date,
        ]);
    }

    public function testCustomerCannotSeeASingleDebitCardDetails()  // DONE
    {
        $otherUser = User::factory()->create();
        $debitCard = $otherUser->debitCards()->create([
            'number' => '6543210987654321',
            'expiration_date' => '2025-12-31',
            'type' => 'Visa',
        ]);
    
        $response = $this->get("api/debit-cards/{$debitCard->id}");
    
        $response->assertStatus(403); // Forbidden
    }

    public function testCustomerCanActivateADebitCard() // DONE
    { 

        $debitCard = DebitCard::factory()->create([
            'user_id' => $this->user->id,
            'disabled_at' => now(), 
        ]);

  
        $response = $this->putJson("/api/debit-cards/{$debitCard->id}", [
            'is_active' => true, 
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['is_active' => true]);

        $this->assertDatabaseHas('debit_cards', [
            'id' => $debitCard->id,
            'disabled_at' => null
        ]);

    }

    public function testCustomerCanDeactivateADebitCard() //DONE
    {
        $debitCard = DebitCard::factory()->create([
            'user_id' => $this->user->id,
            'disabled_at' => null,
        ]);

        $response = $this->putJson("/api/debit-cards/{$debitCard->id}", [
            'is_active' => false, 
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['is_active' => false]);

        $this->assertDatabaseHas('debit_cards', [
            'id' => $debitCard->id,
            'disabled_at' => now()->format('Y-m-d H:i:s')
        ]);
    }

    public function testCustomerCannotUpdateADebitCardWithWrongValidation()
    {
                   // Miss information between laravel passport
    }

    public function testCustomerCanDeleteADebitCard()  // DONE
    {
        $debitCard = $this->user->debitCards()->create([
            'number' => '1234567890123456',
            'expiration_date' => '2025-12-31',
            'type' => 'Visa',
            'is_active' => true,
        ]);
    
        $response = $this->delete("api/debit-cards/{$debitCard->id}");
        $response->assertStatus(204);
    
        $this->assertSoftDeleted('debit_cards', ['id' => $debitCard->id]);
    }

    public function testCustomerCannotDeleteADebitCardWithTransaction() // DONE
    {
        $debitCard = $this->user->debitCards()->create([
            'number' => '1234567890123456',
            'expiration_date' => '2025-12-31',
            'type' => 'Visa',
            'is_active' => true,
        ]);

        $debitCard->debitCardTransactions()->create([
            'debit_card_id' => $debitCard->id,
            'amount' => 100.00,
            'currency_code' => "IDR",
        ]);

        $response = $this->actingAs($this->user)->delete("api/debit-cards/{$debitCard->id}");

        $response->assertStatus(403); 
    }

    // Extra bonus for extra tests :)
}
