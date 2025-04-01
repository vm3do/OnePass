<?php

namespace App\Http\Controllers;

use App\Models\Ip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IpController extends Controller
{

    public function index(){
        $user_ips = Ip::where('user_id', auth()->id())->get();

        return response()->json([
            'user_ips' => $user_ips
        ], 200);
    }

    public function blacklist(Request $request)
    {

        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = Validator::make($request->all(), [
            'ip' => 'required|ip',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'error' => 'Unable to blacklist the IP. Please ensure the IP is valid and try again.'
            ], 422);
        }

        $ip = Ip::updateOrCreate(
            ['ip' => $request->ip, 'user_id' => auth()->id()],
            ['status' => 'black',]
        );

        return response()->json([
            'message' => 'IP got blacklisted successfully',
            'ip' => $ip,
        ], 200);
    }

    public function whitelist(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'ip' => 'required|ip'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'error' => 'Unable to whitelist the IP. Please ensure the IP is valid and try again.'
            ], 422);
        }

        $ip = Ip::updateOrCreate([
            ['ip' => $request->ip, 'user_id' => auth()->id()],
            ['status' => 'white'],
        ]);

        return response()->json([
            'message' => 'IP got blacklisted successfully',
            'ip' => $ip,
        ], 200);
    }


    public function removeBlacklisted($id){

        $user = auth()->user();

        if($user->role !== 'admin'){
            return response()->json([
                'error' => 'Unauthorized'
            ], 403);
        }

        $ip = $user->ips()->where('id', $id)->where('status', 'black')->first();

        if (!$ip) {
            return response()->json([
                'error' => 'IP not found in your whitelist'
            ], 404);
        }

        $ip->delete();

        return response()->json([
            'message' => 'IP removed from your blacklist successfully',
        ], 200);

    }


    public function removeWhitelisted($id){

        $user = auth()->user();

        $ip = $user->ips()->where('id', $id)->where('status', 'white')->first();

        if (!$ip) {
            return response()->json([
                'error' => 'IP not found in your whitelist'
            ], 404);
        }

        $ip->delete();

        return response()->json([
            'message' => 'IP removed from your whitelist successfully',
        ], 200);

    }

}
