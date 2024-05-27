<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testDoPaymentEndpoint()
    {
        $cardData = [
            'card_type' => 'Visa',
            'bank_name' => 'Bank A',
            'card_number' => '12345678',
            'available_limit' => 1000,
            'holder_dni' => '12345678',
            'holder_first_name' => 'John',
            'holder_last_name' => 'Doe',
        ];

        Card::create($cardData);

        $paymentData = [
            'card_number' => '12345678',
            'amount' => 100,
            'installments' => 3
        ];

        $response = $this->postJson('/api/do-payment', $paymentData);

        $response->assertStatus(200)
            ->assertJson([
                'holder_first_name' => 'John',
                'holder_last_name' => 'Doe',
                'total_amount' => 109, // 100 + 9% increment
                'installment_amount' => 36.33
            ]);
    }

    public function testDoPaymentEndpointFailsWithNonExistentCard()
    {
        $paymentData = [
            'card_number' => '99999999',
            'amount' => 100,
            'installments' => 3
        ];

        $response = $this->postJson('/api/do-payment', $paymentData);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Card not found'
            ]);
    }

    public function testDoPaymentEndpointFailsWithInsufficientFunds()
    {
        $cardData = [
            'card_type' => 'Visa',
            'bank_name' => 'Bank A',
            'card_number' => '12345678',
            'available_limit' => 50,
            'holder_dni' => '12345678',
            'holder_first_name' => 'John',
            'holder_last_name' => 'Doe',
        ];

        Card::create($cardData);

        $paymentData = [
            'card_number' => '12345678',
            'amount' => 100,
            'installments' => 3
        ];

        $response = $this->postJson('/api/do-payment', $paymentData);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Insufficient funds'
            ]);
    }
}
