<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
        ]);

        $user->assignRole('patient');

        return response()->json([
            'status' => true,
            'message' => 'user register successfully!',
            'user' => $user,
        ]);
    }

    public function login(Request $request)
    {
        $validatorUser = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validatorUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'error' => $validatorUser->errors()->all(),
            ], 422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authUser = Auth::user();

            return response()->json([
                'status' => true,
                'message' => 'user logged in successfully!',
                'user' => $authUser,
                'role' => $authUser->getRoleNames()->first(), // role bhi bhej dein
                'token' => $authUser->createToken('api token')->plainTextToken,
                'token_type' => 'bearer',
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'authentication failed!',
        ], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'user' => $user,
            'message' => 'user logged out successfully!',
        ], 200);
    }
}
