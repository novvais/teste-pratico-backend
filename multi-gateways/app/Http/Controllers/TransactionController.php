<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Transaction;
use App\Models\TransactionAttempt;
use App\Http\Resources\TransactionResource;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::all();

        return response()->json(TransactionResource::collection($transactions), 200);
    }

    public function show(string $id)
    {
        $transaction = Transaction::with('products')->findOrFail($id);

        return response()->json(new TransactionResource($transaction), 200);
    }

    public function chargeback(string $id)
    {
        $transaction = Transaction::findOrFail($id);

        $attempt = TransactionAttempt::where('transaction_id', $transaction->id)
            ->where('status', 'paid')
            ->firstOrFail();

        $gateway = $attempt->gateway;

        $response = null;

        if ($gateway->name === 'Gateway 1') {
            $response = Http::withToken('FEC9BB078BF338F464F96B48089EB498')
                ->post(env('GATEWAY1_URL') . "/transactions/{$attempt->external_id}/charge_back");
        }

        if ($gateway->name === 'Gateway 2') {
            $response = Http::withHeaders([
                'Gateway-Auth-Token' => 'tk_f2198cc671b5289fa856',
                'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f'
            ])->post(env('GATEWAY2_URL') . '/transacoes/reembolso', [
                'id' => $attempt->external_id
            ]);
        }

        if ($response && $response->successful()) {
            $transaction->update(['status' => 'refunded']);
            $attempt->update(['status' => 'refunded']);

            return response()->json(['message' => 'Refund successful'], 200);
        }

        return response()->json(['message' => 'Refund failed at gateway'], 400);
    }
}