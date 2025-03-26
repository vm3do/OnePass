<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Passwords;
use Dotenv\Parser\Value;

use League\Csv\Reader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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



    /**
     * Import passwords from a CSV file.
     */
    public function importPasswords(Request $request)
    {
        //Check if the file is uploaded
        if (!$request->hasFile('file')) {
            return response()->json(['message' => 'No file uploaded.'], 400);
        }

        $file = $request->file('file');

        // Validate that the file is a CSV
        if ($file->getClientOriginalExtension() !== 'csv') {
            return response()->json(['message' => 'The file must be a .CSV format'], 400);
        }

        // Get the current logged user
        $user = $request->user()->id;

        // Read the CSV file using the League CSV package
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0); // Skip the header row

        // Loop through the rows of the CSV
        foreach ($csv as $row) {
            // Extract columns (column names: name, url, username, password, note)
            $serviceName = $row['name'];
            $password = $row['password'];

            // Check if the record already exists in the database
            $exists = DB::table('passwords')
                ->where('site_name', $serviceName)
                ->where('password', $password)
                ->exists();

            if ($exists) {
                continue;
            }

            // Insert into the passwords table
            DB::table('passwords')->insert([
                'site_name' => $serviceName,
                'password' => $password,
                'user_id' => $user,
            ]);
        }

        return response()->json(['message' => 'Passwords imported successfully.']);
    }
}
