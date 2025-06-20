<?php

namespace App\Http\Controllers\Product;

use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $inventories = Inventory::query()->where('is_deleted', 0);
            if ($request->has('name')) {
                $inventories->where('name', 'like', '%' . $request->query('name') . '%');
            }
            return response()->json(
                $inventories->paginate($request->query('limit') ?? 10)
            );
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 500);
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
                'name' => 'required|string',
                'image' => 'nullable|file',
                'description' => 'nullable|string',
                'quantity' => 'required|integer',
                'branch_id' => 'nullable|integer',
                'category_id' => 'nullable|integer',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
            ]);
            $data = $request->all();
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/inventories'), $filename);
                $data['image'] = 'images/inventories/' . $filename;
            }
            $inventory = Inventory::create($data);
            return response()->json([
                'status' => true,
                'message' => 'Inventory created successfully',
                'data' => $inventory,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        try {
            $inventory = Inventory::query()->where('id', $id)->where('is_deleted', 0)->first();
            if ($inventory) {
                return response()->json([
                    'status' => true,
                    'message' => 'Inventory',
                    'data' => $inventory
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Inventory not found',
                ]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        // dd("AAAAAAAAAAAAA");
        try {
            // dd($request->file('image'));
            $request->validate([
                'name' => 'nullable|string',
                'image' => 'nullable|file',
                'description' => 'nullable|string',
                'quantity' => 'nullable|integer',
                'branch_id' => 'nullable|integer',
                'category_id' => 'nullable|integer',
                'price' => 'nullable|numeric',
                'stock' => 'nullable|integer',
            ]);
            $inventory = Inventory::query()->where('id', $id)->where('is_deleted', 0)->first();
            if ($inventory) {
                $data = $request->all();
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('images/inventories'), $filename);
                    $data['image'] = 'images/inventories/' . $filename;
                }
                $inventory->update($data);
                return response()->json([
                    'status' => true,
                    'message' => 'Inventory updated successfully',
                    'data' => $inventory,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Inventory not found',
                ]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * 
     * Remove the specified resource from storage.
     * 
     */
    public function destroy($id)
    {
        //
        try {
            $inventory = Inventory::query()->where('is_deleted', 0)->findOrFail($id);
            $inventory->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Inventory deleted successfully',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 400);
        }
    }
}
