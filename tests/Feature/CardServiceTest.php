<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CardService;
use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Exception;

class CardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $cardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cardService = new CardService();
    }

    public function testRegisterCard()
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

        $card = $this->cardService->registerCard($data);

        $this->assertDatabaseHas('cards', [
            'card_number' => '12345678'
        ]);
    }

    public function testRegisterCardFailsWithDuplicateCardNumber()
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

        $this->cardService->registerCard($data);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to register card');

        $this->cardService->registerCard($data);
    }
}
