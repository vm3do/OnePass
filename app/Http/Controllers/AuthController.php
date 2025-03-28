<?php

namespace App\Http\Controllers;

use App\Models\Ip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\LoginAttemptWarningMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Mail\NewDeviceNotification;


use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'error registering' => $validated->errors()
            ], 401);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user_ip = Ip::create([
            'ip' => $request->ip(),
            'user_id' => $user->id,
        ]);

        $token = $user->createToken($request->email);

        return response()->json([
            'user' => $user,
            'user_ip' => $user_ip,
            'token' => $token->plainTextToken,
        ], 201);
    }

    public function login(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'email' => 'required|exists:users',
            'password' => 'min:6|required',
        ]);

        if($validated->fails()) {
            return response()->json([
                'error login in' => $validated->errors()
            ], 401);
        }




        $key = 'user_email:' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 10)) {
            if (!Cache::has('alert_sent_by' . $request->email)) {
                Mail::to($request->email)->send(new  LoginAttemptWarningMail());
                Cache::put('alert_sent_by' . $request->email, true, 3600);
            }

            if (Cache::has($key . ':blocked')) {
                return response()->json(['message' => 'Vous êtes bloqué pendant une heure.'], 429);
            }
            Cache::put($key . ':blocked', true, 3600);


            return response()->json(['message' => 'Trop de tentatives, veuillez réessayer plus tard.'], 429);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::increment($key);
            return response()->json([
                'error' => 'incorrect credentials'
            ], 401);
        };
        // RateLimiter::hit($key, 180);


        /* ***************************
        ********* verification ip ****
        ******************************/
        $ip = $request->ip();
        // Check if this IP is registered
        $exists = Ip::where('user_id', $user->id)->where('ip', $userIp)->exists();

        if (!$exists) {
            $this->sendVerificationLink($user, $userIp);

            return response()->json([
                'message' => 'Un code de vérification a été envoyé à votre email. Veuillez le valider avant de vous connecter.',
                'ip' => $userIp
            ], 401);
        }

        RateLimiter::clear($key);
        $token = $user->createToken($user->email);

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 201);



    }

    public function sendVerificationLink($user, $newIp)
    {
        $exists = Ip::where('user_id', $user->id)->where('ip', $newIp)->exists();

        if (!$exists) {
            $verificationToken = Str::random(60);
            $expiresAt = now()->addMinutes(10);

            // Store verification details in cache
            $cacheKey = "verification_link_{$verificationToken}";
            Cache::put($cacheKey, [
                'user_id' => $user->id,
                'ip' => $newIp,
                'token' => $verificationToken
            ], $expiresAt);

            // Generate verification link
            $verificationLink = url('/api/verify-ip') . "?token={$verificationToken}&ip={$newIp}";

            Mail::to($user->email)->send(new NewDeviceNotification($verificationLink));

            return true;
        }

        return false;
    }



    public function verifyIp(Request $request)
    {
        $token = $request->query('token');
        $ip = $request->query('ip');

        if (!$token || !$ip) {
            return response()->json(['error' => 'Token ou IP manquant.'], 400);
        }

        // Get cache key
        $cacheKey = "verification_link_{$token}";
        $cachedData = Cache::get($cacheKey);

        if (!$cachedData) {
            return response()->json(['error' => 'Lien de vérification expiré ou invalide.'], 401);
        }

        if ($cachedData['token'] !== $token) {
            return response()->json(['error' => 'Token invalide.'], 401);
        }

        Ip::create([
            'ip' => $ip,
            'user_id' => $cachedData['user_id'],
            'status' => 'White'
        ]);

        // Remove cache entry after successful verification
        Cache::forget($cacheKey);

        return response()->json(['message' => 'IP validée avec succès. Vous pouvez maintenant vous connecter.'], 200);
    }




    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'you are logged out'
        ], 200);
    }
}
