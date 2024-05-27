<?php
namespace App\Services;

use App\Models\Card;
use Illuminate\Support\Facades\DB;
use Exception;

class CardService
{
    public function registerCard($data)
    {
        DB::beginTransaction();
        try {
            $card = Card::create($data);
            DB::commit();
            return $card;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to register card: ' . $e->getMessage());
        }
    }
}
