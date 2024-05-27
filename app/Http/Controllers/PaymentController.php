<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PosnetService;
use Exception;

class PaymentController extends Controller
{
    protected $posnetService;

    public function __construct(PosnetService $posnetService)
    {
        $this->posnetService = $posnetService;
    }

    public function doPayment(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string|size:8|exists:cards,card_number',
            'amount' => 'required|numeric|min:0.01',
            'installments' => 'required|integer|min:1|max:6',
        ]);

        try {
            $result = $this->posnetService->doPayment(
                $request->card_number,
                $request->amount,
                $request->installments
            );
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
