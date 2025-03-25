<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Passwords;


class PasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'site_name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        if($validation->fails()){
            return response()->json($validation->errors(), 400);
        }

        $password = new Passwords();

        $password->user_id = Auth::id();
        $password->site_name = $request->site_name;
        $password->password = $request->password;
        $password->save();

        return response()->json(['message' => 'Password created successfully', 'data' => $password], 201);


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $password = Auth::user()->passwords()->find($id);
        // dd($password);
        // if (!$password) {
        //     return response()->json(['message' => 'Password not found'], 404);
        // }

        // return response()->json($password);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
