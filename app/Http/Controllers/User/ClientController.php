<?php

namespace App\Http\Controllers\User;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $clients = Client::query()->where('is_deleted', 0);
            if ($request->has('name')) {
                $clients->where('name', 'like', '%' . $request->query('name') . '%');
            }
            return response()->json($clients->paginate($request->query('limit') ?? 10)
            );
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $request->validate([
            'member_id' => 'nullable|integer',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:clients,email',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
            'saldo' => 'nullable|numeric|min:0',
            'points' => 'nullable|integer|min:0',
            ]);
            $client = Client::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Client created successfully',
                'data' => $client,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ]);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        try {
            $client = Client::find($id)->where('is_deleted', 0)->first();
            if (!$client) {
                return response()->json([
                    'status' => false,
                    'message' => 'Client not found',
                ], 404);
            }
            return response()->json([
                'status' => true,
                'data' => $client,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        try {
            $request->validate([
                'member_id' => 'nullable|integer',
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:clients,email,' . $id,
                'phone' => 'nullable|string|max:15',
                'address' => 'nullable|string|max:500',
                'saldo' => 'nullable|numeric|min:0',
                'points' => 'nullable|integer|min:0',
            ]);
            $client = Client::find($id);
            if (!$client) {
                return response()->json([
                    'status' => false,
                    'message' => 'Client not found',
                ], 404);
            }
            $client->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Client updated successfully',
                'data' => $client,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        //
        try {
            $client = Client::find($id);
            if (!$client) {
                return response()->json([
                    'status' => false,
                    'message' => 'Client not found',
                ], 404);
            }
            $client->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Client deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ]);
        }

    }
}
