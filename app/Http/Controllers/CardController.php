<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CardService;
use Exception;

class CardController extends Controller
{
    protected $cardService;

    public function __construct(CardService $cardService)
    {
        $this->cardService = $cardService;
    }

    public function registerCard(Request $request)
    {
        $request->validate([
            'card_type' => 'required|string|in:Visa,AMEX',
            'bank_name' => 'required|string',
            'card_number' => 'required|string|size:8|unique:cards,card_number',
            'available_limit' => 'required|numeric|min:0',
            'holder_dni' => 'required|string',
            'holder_first_name' => 'required|string',
            'holder_last_name' => 'required|string',
        ]);

        try {
            $card = $this->cardService->registerCard($request->all());
            return response()->json($card, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
