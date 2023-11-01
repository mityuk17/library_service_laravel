<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{

    public function login(Request $request)
    {
        $email = strtolower($request->input('email'));
        $password = $request->input('password');

        $credentials = [
            'email' => $email,
            'password' => $password
        ];
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token', [$user->role])->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ],200);
    }


    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Succesfully Logged out'
        ], 200);
    }
}
