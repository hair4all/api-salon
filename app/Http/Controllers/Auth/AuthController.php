<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Client;
use App\Models\Worker;
use App\Helpers\JwtHelper;

trait AuthClientTrait
{
    public function clientLogin(Request $request)
    {
        try {
            $request->validate([
                'phone'    => 'required',
                'password' => 'required|string',
            ]);

            $client = Client::where('phone', '=', $request->phone)
                ->where('is_deleted', '=', 0)
                ->first();

            if (!$client || !Hash::check($request->password, $client->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $data  = $client->toArray();
            $token = JwtHelper::generateToken($data, 19200);

            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function clientRegister(Request $request)
    {
        try {
            $request->validate([
                'phone'          => 'required|unique:clients,phone',
                // 'password'       => 'required|string|min:8|confirmed',
                'password'       => 'required|string',
                'name'    => 'required|string|max:255',
                'address' => 'nullable',
                'email'   => 'required|email|unique:clients,email',
            ]);

            $client = new Client();
            $client->name      = $request->input('name');
            $client->email     = $request->input('address');
            $client->phone     = $request->phone;
            $client->password  = Hash::make($request->password);
            $client->address   = $request->input('address') ?? null;
            $client->saldo     = 0;
            $client->points    = 0;
            $client->coins     = 0;
            $client->is_deleted = 0;
            $client->save();

            $payload = [
                'client_id' => $client->id,
                'email'     => $client->email,
                'name'      => $client->name,
            ];

            return response()->json([
                'message' => 'Client registered successfully',
                'payload' => $payload
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error during registration',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}

trait AuthWorkerTrait
{
    public function workerLogin(Request $request)
    {
        try {
            $request->validate([
                'username'    => 'required',
                'password' => 'required|string',
            ]);

            $worker = Worker::where('username', '=', $request->username)
                ->where('is_deleted', '=', 0)
                ->first();

            if (!$worker || !Hash::check($request->password, $worker->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $data  = $worker->toArray();
            $token = JwtHelper::generateToken($data, 19200);

            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function workerRegister(Request $request)
    {
        try {
            $request->validate([
                'username'  => 'required|unique:workers,username',
                'password'  => 'required|string',
                'name'      => 'required|string|max:255',
                'phone'     => 'nullable',
                'branch_id' => 'required|integer',
                'position_id' => 'required|integer',
                'salary'  => 'nullable|numeric',
                'status'  => 'nullable|integer',
                'address' => 'nullable',
            ]);

            // Check if branch_id and position_id exist in the database
            $branchExists = Branch::where('id', $request->input('branch_id'))->exists();
            $positionExists = Position::where('id', $request->input('position_id'))->exists();

            if (!$branchExists || !$positionExists) {
                if (!$branchExists) {
                    $request->merge(['branch_id' => null]);
                }
                if (!$positionExists) {
                    $request->merge(['position_id' => null]);
                }
            }

            $worker = new Worker();
            $worker->username   = $request->input('username');
            $worker->name       = $request->input('name');
            $worker->email      = $request->email;
            $worker->password   = Hash::make($request->password);
            $worker->branch_id  = $request->input('branch_id');
            $worker->position_id = $request->input('position_id');
            $worker->phone      = $request->input('phone') ?? null;
            $worker->address    = $request->input('address') ?? null;
            $worker->salary     = $request->input('salary') ?? null;
            $worker->status     = $request->input('status') ?? 0;
            $worker->is_deleted = 0;
            $worker->save();

            return response()->json([
                'message' => 'Worker registered successfully',
                'payload' => [
                    'worker_id' => $worker->id,
                    'username'  => $worker->username,
                    'password'  => $worker->password,
                    'email'     => $worker->email,
                    'name'      => $worker->name,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error during registration',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}

class AuthController extends Controller
{
    use AuthClientTrait, AuthWorkerTrait;

    public function loginClientWithGoogle(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
            ]);

            $client = new \Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($request->token);

            if ($payload) {
                $email = $payload['email'];
                $client = Client::where('email', '=', $email)->first();

                if (!$client) {
                    $client = new Client();
                    $client->email    = $email;
                    $client->name     = $payload['name'];
                    $client->password = Hash::make(Str::random(16));
                    $client->save();
                }

                $data  = $client->toArray();
                $token = JwtHelper::generateToken($data, 19200);

                return response()->json(['token' => $token]);
            } else {
                return response()->json(['message' => 'Invalid Google token'], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
