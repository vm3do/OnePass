<?php

namespace App\Http\Controllers;

use App\Models\Ip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\LoginAttemptWarningMail;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;
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
        $key='user_email:'.$request->email;
       if (RateLimiter::tooManyAttempts($key,3)) {
        if (!Cache::has('alert_sent_by'.$request->email)) {
       Mail::to($request->email)->send( new  LoginAttemptWarningMail());
       Cache::put('alert_sent_by' . $request->email, true, 3600);
        }

   


   return response()->json(['message' => 'Trop de tentatives, veuillez réessayer plus tard.'], 429);
}

        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            RateLimiter::increment($key);
            return response()->json([
                'error' => 'incorrect credentials'
            ], 401);
        };
        RateLimiter::hit($key,180);

        RateLimiter::clear($key);
        $token = $user->createToken($user->email);

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 201);

    }
    
    public function logout(){

    }
    
}
