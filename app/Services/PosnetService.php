<?php
namespace App\Services;

use App\Models\Card;
use Illuminate\Support\Facades\DB;
use Exception;

class PosnetService
{
    public function doPayment($cardNumber, $amount, $installments)
    {
        DB::beginTransaction();
        try {
            $card = Card::where('card_number', $cardNumber)->first();

            if (!$card) {
                throw new Exception('Card not found');
            }

            $totalAmount = $amount;
            if ($installments > 1) {
                $totalAmount += $amount * 0.03 * ($installments - 1);
            }

            if ($card->available_limit < $totalAmount) {
                throw new Exception('Insufficient funds');
            }

            $card->available_limit -= $totalAmount;
            $card->save();

            DB::commit();

            return [
                'holder_first_name' => $card->holder_first_name,
                'holder_last_name' => $card->holder_last_name,
                'total_amount' => $totalAmount,
                'installment_amount' => $totalAmount / $installments,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to process payment: ' . $e->getMessage());
        }
    }
}
