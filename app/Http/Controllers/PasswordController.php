<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Passwords;
use Dotenv\Parser\Value;

class PasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $passwords = Auth::user()->passwords;
        return response()->json($passwords);
    }


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
        $password = Auth::user()->passwords()->find($id);
        // dd($password);
        if (!$password) {
            return response()->json(['message' => 'Password not found'], 404);
        }

        return response()->json($password);
    }

    /**
     * Update the specified resource in storage.
     */
        public function update(Request $request, $id)
        {
            $password = Auth::user()->passwords()->find($id);

            if (!$password) {
                return response()->json(['message' => 'Mot de passe non trouvé'], 404);
            }

            $validator = Validator::make($request->all(), [
                'site_name' => 'required|string|max:255',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            
            $password->site_name = $request->site_name;
            $password->password = $request->password;
            $password->save();

            return response()->json(['message' => 'Mot de passe mis à jour avec succès', 'data' => $password]);
        }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {
        $password = Auth::user()->passwords()->find($id);
        if (!$password) {
            return response()->json(['message' => 'Mot de passe non trouvé'], 404);
        }
        $password->delete();
        return response()->json(['message' => 'Mot de passe supprimé avec succès']);
    }
}
