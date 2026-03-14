<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Http\Resources\ClientResource;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();

        return response()->json(ClientResource::collection($clients));
    }

    public function show(String $id)
    {
        $client = Client::with('transactions')->findOrFail($id);

        return response()->json(ClientResource::collection($client));
    }
}
