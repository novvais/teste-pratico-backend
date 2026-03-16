<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Transaction;
use App\Models\TransactionAttempt;
use App\Models\Client;
use App\Models\Card;
use App\Models\Product;
use App\Models\Gateway;
use App\Http\Requests\StoreCheckoutRequest;
use App\Http\Resources\CheckoutResource;

class CheckoutController extends Controller
{
    public function store(StoreCheckoutRequest $request)
    {
        $validatedData = $request->validated();

        return DB::transaction(function () use ($validatedData) {
            $client = Client::firstOrCreate(
                ['email' => $validatedData['client_email']],
                ['name' => $validatedData['client_name']]
            );

            $card = Card::firstOrCreate([
                'client_id' => $client->id,
                'last_four' => $validatedData['card']['last_four'],
                'expiration_month' => $validatedData['card']['expiration_month'],
                'expiration_year' => $validatedData['card']['expiration_year']
            ]);

            $ids = array_column($validatedData['products'], 'id');
            $products = Product::find($ids);

            $totalValue = 0;
            $pivotData = [];

            foreach ($validatedData['products'] as $item) {
                $productModel = $products->firstWhere('id', $item['id']);
                
                $totalValue += $productModel->amount * $item['quantity'];

                $pivotData[$item['id']] = [
                    'quantity' => $item['quantity'],
                    'unit_amount' => $productModel->amount
                ];
            }

            $checkout = Transaction::create([
                'client_id' => $client->id,
                'amount' => $totalValue,
                'status' => 'pending'
            ]);

            $checkout->products()->attach($pivotData);

            $gateways = Gateway::where('is_active', true)->orderBy('priority')->get();

            foreach ($gateways as $gateway) {
                $response = null;

                if ($gateway->name === 'Gateway 1') {
                    $response = Http::withToken('FEC9BB078BF338F464F96B48089EB498')
                        ->post('http://localhost:3001/transactions', [
                            'amount' => $totalValue,
                            'name' => $client->name,
                            'email' => $client->email,
                            'cardNumber' => '556900000000' . $validatedData['card']['last_four'],
                            'cvv' => $validatedData['card']['cvv']
                        ]);
                }

                if ($gateway->name === 'Gateway 2') {
                    $response = Http::withHeaders([
                        'Gateway-Auth-Token' => 'tk_f2198cc671b5289fa856',
                        'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f'
                    ])->post('http://localhost:3002/transacoes', [
                        'valor' => $totalValue,
                        'nome' => $client->name,
                        'email' => $client->email,
                        'numeroCartao' => '556900000000' . $validatedData['card']['last_four'],
                        'cvv' => $validatedData['card']['cvv']
                    ]);
                }

                if ($response && $response->successful()) {
                    $checkout->update(['status' => 'paid']);

                    TransactionAttempt::create([
                        'transaction_id' => $checkout->id,
                        'gateway_id' => $gateway->id,
                        'card_id' => $card->id,
                        'status' => 'paid',
                        'external_id' => $response->json('id') ?? 'ext_api_id',
                        'gateway_res' => $response->json()
                    ]);

                    break;
                }
            }

            return response()->json(new CheckoutResource($checkout), 201);
        });
    }
}