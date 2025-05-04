<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Member;
use App\Models\Worker;
use App\Models\Client;
use App\Helpers\JwtHelper;

trait AuthClientTrait{

    public function clientLogin(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);

            $client = Client::where('email', '=',$request->email)
            ->where('is_deleted','=',0)->first();

            if (!$client) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $member = Member::where('id','=', $client->member_id)->where('is_deleted','=',0)->first();

            if (!$member || !Hash::check($request->password, $member->password)) {
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
                'email'          => 'required|email|unique:members,email',
                'user'           => 'array',
                'user.username'  => 'nullable|string|max:255',
                'user.password'  => 'required|string|min:8|confirmed',
                'client'         => 'array',
                'client.name'    => 'required|string|max:255',
                'client.phone'   => 'nullable',
                'client.address' => 'nullable',
            ]);

            

            $user = new Member();
            $user->username     = $request->user['username'];
            $user->email    = $request->email;
            $user->password = Hash::make($request->user['password']);
            $user->save();

            $client = new Client();
            $client->member_id = $user->id;
            $client->name      = $request->client['name'];
            $client->email     = $request->email;
            $client->phone     = $request->client['phone'];
            $client->address   = $request->client['address'];
            $client->saldo     = 0;
            $client->points    = 0;
            $client->is_deleted= 0;
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

trait AuthWorkerTrait{
    public function workerLogin(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);

            $worker = Worker::where('email','=',$request->email)->where('is_deleted','=',0)->first();
            
            if (!$worker) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            
            $member = Member::where('id','=', $worker->member_id)->where('is_deleted','=',0)->first();

            if (!$member || !Hash::check($request->password, $member->password)) {
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

    public function workerRegister(Request $request){
        try {
            $request->validate([
                'email'          => 'required|email',
                'user'           => '*.array',
                'user.username'  => 'nullable|string|max:255',
                'user.password'  => 'required|string|min:8|confirmed',
                'worker'         => '*.array',
                'worker.name'    => 'required|string|max:255',
                'worker.phone'   => 'nullable',
                'worker.branch_id' => 'required|integer',
                'worker.position_id' => 'required|integer',
                'worker.salary' => 'required|numeric',
                'worker.status' => 'required|integer',
                'worker.address' => 'nullable',
            ]);

            // Check if branch_id and position_id exist in the database
            $branchExists = Branch::where('id', $request->worker['branch_id'])->exists();
            $positionExists = Position::where('id', $request->worker['position_id'])->exists();

            // If not exists, set into null
            if (!$branchExists || !$positionExists) {
                if (!$branchExists) {
                    $request->worker['branch_id'] = null;
                }
                if (!$positionExists) {
                    $request->worker['position_id'] = null;
                }
            }

            $user = new Member();
            $user->name     = $request->user['name'];
            $user->email    = $request->email;
            $user->password = Hash::make($request->user['password']);
            $user->save();

            $worker = new Worker();
            $worker->member_id = $user->id;
            $worker->name      = $request->worker['name'];
            $worker->email     = $request->email;
            $worker->phone     = $request->worker['phone'];
            $worker->address   = $request->worker['address'];
            $worker->is_deleted= 0;
            $worker->save();

            return response()->json([
                'message' => 'Worker registered successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error during registration',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
}

class AuthController extends Controller{
    use AuthClientTrait, AuthWorkerTrait;

    public function loginClientWithGoogle(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
            ]);

            // Verify the Google token and get user info
            $client = new \Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($request->token);

            if ($payload) {
                $email = $payload['email'];
                $member = Member::where('email', '=', $email)->first();
                if (!$member) {
                    // If not, create a new member
                    $member = new Member();
                    $member->email    = $email;
                    $member->password = Hash::make(Str::random(16)); // Generate a random password
                    $member->save();
                }
                // Check if the client already exists
                $client = Client::where('email', '=', $email)->first();

                if (!$client) {
                    // If not, create a new client
                    $client = new Client();
                    $client->email = $email;
                    $client->name  = $payload['name'];
                    $client->save();
                }

                // Generate JWT token
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