<?php
namespace App\Services;

use App\Models\Card;
use App\Models\Payment;

class Posnet
{
    public function doPayment($cardNumber, $amount, $installments)
    {
        $card = Card::where('card_number', $cardNumber)->first();
        if (!$card) {
            throw new \Exception('Card not found');
        }

        $totalAmount = $installments > 1 ? $amount * (1 + 0.03 * ($installments - 1)) : $amount;

        if ($card->available_limit < $totalAmount) {
            throw new \Exception('Insufficient limit');
        }

        $installmentAmount = $totalAmount / $installments;
        $card->available_limit -= $totalAmount;
        $card->save();

        $payment = Payment::create([
            'card_id' => $card->id,
            'amount' => $amount,
            'installments' => $installments,
            'total_amount' => $totalAmount,
            'installment_amount' => $installmentAmount,
        ]);

        return [
            'holder_first_name' => $card->holder_first_name,
            'holder_last_name' => $card->holder_last_name,
            'total_amount' => $totalAmount,
            'installment_amount' => $installmentAmount,
        ];
    }
}
