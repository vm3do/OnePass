<?php

namespace App\Http\Controllers;

use App\Models\Ip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){

        $validated = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user_ip = Ip::create(['ip' => $request->ip()]);

        $token = $user->createToken($validated['email']);

        return response()->json([
            'user' => $user,
            'user_ip' => $user_ip,
            'token' => $token->plainTextToken,
        ], 201);

    }
    
    public function login(Request $request){

        $request->validate([
            'email' => 'required|exists:users',
            'password' => 'min:6|required',
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'error' => 'incorrect credentials'
            ], 401);
        };

        $token = $user->createToken($user->email);

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 201);

    }
    
    public function logout(){

    }
    
}
