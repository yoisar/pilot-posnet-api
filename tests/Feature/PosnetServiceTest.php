<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PosnetService;
use App\Services\CardService;
use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Exception;

class PosnetServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $posnetService;
    protected $cardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->posnetService = new PosnetService();
        $this->cardService = new CardService();
    }

    public function testDoPayment()
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

        $this->cardService->registerCard($cardData);

        $result = $this->posnetService->doPayment('12345678', 100, 3);

        $this->assertEquals('John', $result['holder_first_name']);
        $this->assertEquals('Doe', $result['holder_last_name']);
        $this->assertEquals(106, $result['total_amount']); // 100 + 9% increment
        $this->assertEquals(35.33, round($result['installment_amount'], 2));
    }

    public function testDoPaymentFailsWithNonExistentCard()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Card not found');

        $this->posnetService->doPayment('99999999', 100, 3);
    }

    public function testDoPaymentFailsWithInsufficientFunds()
    {
        $cardData = [
            'card_type' => 'Visa',
            'bank_name' => 'Bank A',
            'card_number' => '12345678',
            'available_limit' => 100,
            'holder_dni' => '12345678',
            'holder_first_name' => 'John',
            'holder_last_name' => 'Doe',
        ];

        $this->cardService->registerCard($cardData);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient funds');

        $this->posnetService->doPayment('12345678', 1000, 3);
    }
}
