<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

    /**
     * Import passwords from a CSV file.
     */
    public function importPasswords(Request $request)
    {
        //Check if the file is provided
        if (!$request->hasFile('file')) {
            return response()->json(['message' => 'No file uploaded.'], 400);
        }

        $file = $request->file('file');

        // Validate that the file is a CSV
        if ($file->getClientOriginalExtension() !== 'csv') {
            return response()->json(['message' => 'The file must be a .CSV format'], 400);
        }

        // Get the currently authenticated user
        // $user = auth()->user();

        // Read the CSV file using the League CSV package
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0); // Skip the header row

        // Loop through the rows of the CSV
        foreach ($csv as $row) {
            // Extract columns (column names: name, url, username, password, note)
            $serviceName = $row['name'];
            $password = $row['password'];

            // Insert into the passwords table
            DB::table('passwords')->insert([
                'site_name' => $serviceName,
                'password' => $password,
                // 'user_id' => $user->id(),
                'user_id' => 1,
            ]);
        }

        return response()->json(['message' => 'Passwords imported successfully.']);
    }
}


// public function importPasswords(Request $request)
// {

//     // Check if the file is provided
//     if (!$request->hasFile('file')) {
//         return response()->json(['message' => 'No file uploaded.'], 400);
//     }

//     $file = $request->file('file');

//     // Validate that the file is a CSV
//     if ($file->getClientOriginalExtension() !== 'csv') {
//         return response()->json(['message' => 'The file must be a .CSV format'], 400);
//     }

//     // Get the currently authenticated user
//     // $user = auth()->user();

//     // Read the CSV file using the League CSV package
//     $csv = Reader::createFromPath($file->getRealPath(), 'r');
//     $csv->setHeaderOffset(0); // Skip the header row

//     // Loop through the rows of the CSV
//     foreach ($csv as $row) {
//         // Extract columns (column names: name, url, username, password, note)
//         $serviceName = $row['name'];
//         $password = $row['password'];

//         // Insert into the passwords table
//         DB::table('passwords')->insert([
//             'site_name' => $serviceName,
//             'password' => $password,
//             // 'user_id' => $user->id(),
//             'user_id' => 1,
//         ]);
//     }

//     return response()->json(['message' => 'Passwords imported successfully.']);
// }
