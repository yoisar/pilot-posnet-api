<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Payment;

class PosnetController extends Controller
{
    public function registerCard(Request $request)
    {
        $request->validate([
            'card_type' => 'required|in:Visa,AMEX',
            'bank_name' => 'required|string',
            'card_number' => 'required|string|size:8|unique:cards,card_number',
            'available_limit' => 'required|numeric',
            'holder_dni' => 'required|string',
            'holder_first_name' => 'required|string',
            'holder_last_name' => 'required|string',
        ]);

        $card = Card::create($request->all());

        return response()->json($card, 201);
    }

    public function doPayment(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string|size:8|exists:cards,card_number',
            'amount' => 'required|numeric',
            'installments' => 'required|integer|min:1|max:6',
        ]);

        $card = Card::where('card_number', $request->card_number)->first();
        $amount = $request->amount;
        $installments = $request->installments;
        $total_amount = $installments > 1 ? $amount * (1 + 0.03 * ($installments - 1)) : $amount;

        if ($card->available_limit < $total_amount) {
            return response()->json(['error' => 'Insufficient limit'], 400);
        }

        $installment_amount = $total_amount / $installments;
        $card->available_limit -= $total_amount;
        $card->save();

        $payment = Payment::create([
            'card_id' => $card->id,
            'amount' => $amount,
            'installments' => $installments,
            'total_amount' => $total_amount,
            'installment_amount' => $installment_amount,
        ]);

        return response()->json([
            'holder_first_name' => $card->holder_first_name,
            'holder_last_name' => $card->holder_last_name,
            'total_amount' => $total_amount,
            'installment_amount' => $installment_amount,
        ], 201);
    }
}
