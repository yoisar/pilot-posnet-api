<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Card;
use App\Services\Posnet;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PosnetTest extends TestCase
{
    use RefreshDatabase;

    public function testDoPayment()
    {
        $card = Card::create([
            'card_type' => 'Visa',
            'bank_name' => 'Bank A',
            'card_number' => '12345678',
            'available_limit' => 1000,
            'holder_dni' => '12345678',
            'holder_first_name' => 'John',
            'holder_last_name' => 'Doe',
        ]);

        $posnet = new Posnet();
        $result = $posnet->doPayment('12345678', 100, 3);

        $this->assertEquals('John', $result['holder_first_name']);
        $this->assertEquals('Doe', $result['holder_last_name']);
        $this->assertEquals(109, $result['total_amount']); // 100 + 9% increment
        $this->assertEquals(36.33, round($result['installment_amount'], 2));
    }
}
