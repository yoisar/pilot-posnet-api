<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisterCardEndpoint()
    {
        $data = [
            'card_type' => 'Visa',
            'bank_name' => 'Bank A',
            'card_number' => '12345678',
            'available_limit' => 1000,
            'holder_dni' => '12345678',
            'holder_first_name' => 'John',
            'holder_last_name' => 'Doe',
        ];

        $response = $this->postJson('/api/register-card', $data);

        $response->assertStatus(201)
            ->assertJson([
                'card_type' => 'Visa',
                'bank_name' => 'Bank A',
                'card_number' => '12345678',
                'available_limit' => 1000,
                'holder_dni' => '12345678',
                'holder_first_name' => 'John',
                'holder_last_name' => 'Doe',
            ]);

        $this->assertDatabaseHas('cards', [
            'card_number' => '12345678'
        ]);
    }

    public function testRegisterCardEndpointFailsWithDuplicateCardNumber()
    {
        $data = [
            'card_type' => 'Visa',
            'bank_name' => 'Bank A',
            'card_number' => '12345678',
            'available_limit' => 1000,
            'holder_dni' => '12345678',
            'holder_first_name' => 'John',
            'holder_last_name' => 'Doe',
        ];

        Card::create($data);

        $response = $this->postJson('/api/register-card', $data);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Failed to register card: SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry'
            ]);
    }
}
