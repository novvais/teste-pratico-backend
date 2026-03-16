<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateGatewayRequest;
use App\Http\Resources\GatewayResource;
use App\Models\Gateway;

class GatewayController extends Controller
{
    public function index()
    {
        $gateways = Gateway::orderBy('priority')->get();

        return response()->json(GatewayResource::collection($gateways), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGatewayRequest $request, string $id)
    {
        $validatedData = $request->validated();

        $gateway = Gateway::findOrFail($id);
        
        $gateway->update($validatedData);

        return response()->json(new GatewayResource($gateway), 200);
    }
}
