<?php

namespace App\Http\Controllers\User;

use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $members = Member::query()->where('is_deleted', 0);
            if ($request->has('name')) {
                $members->where('name', 'like', '%' . $request->query('name') . '%');
            }
            return response()->json($members->paginate($request->query('limit') ?? 10));
        } catch (\Throwable $th) {
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
                'username' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:members,email',
                'password' => 'nullable|string|min:8',
            ]);
            $member = Member::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Member created successfully',
                'data' => $member,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        try {
            $member = Member::query()->where('is_deleted', 0)->findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Member retrieved successfully',
                'data' => $member,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 500);
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
                'username' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:members,email,' . $id,
                'password' => 'nullable|string|min:8',
            ]);
            $member = Member::findOrFail($id);
            $member->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Member updated successfully',
                'data' => $member,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        try {
            $member = Member::findOrFail($id);
            $member->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Member deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 500);
        }
    }
}
