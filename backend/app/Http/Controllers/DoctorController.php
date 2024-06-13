<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     * Fetches all users with the 'doctor' userType and returns them as JSON.
     */
    public function index()
    {
        $doctors = User::where('userType', 'doctor')->get();
        if ($doctors->count() > 0) {
            return response()->json([
                'status' => 200,
                'DoctorAccounts' => $doctors
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'Message' => 'No Records Found'
            ], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     * This function is not implemented as it is typically used for returning a view in web applications.
     */
    public function create()
    {
        // Not implemented
    }

    /**
     * Store a newly created resource in storage.
     * Validates the request data and creates a new user with 'doctor' userType.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email',
            'password' => 'required|string|min:8|max:191'
        ]);

        // Return validation errors if any
        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->messages()
            ], 400);
        } 

        try {
            // Create a new user with the specified data
            User::create([
                'name'=> $request->name,
                'email'=> $request->email,
                'password'=> $request->password,
                'userType'=> 'doctor'
            ]);

            // Return success message
            return response()->json([
                'status' => 200,
                'message' => "Account Created Successfully"
            ], 200);

        } catch (\Exception $e) {
            // Return error message in case of an exception
            return response()->json([
                'status' => 500,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * Fetches and returns a specific user by ID if they are a doctor.
     */
    public function show(string $id)
    {
        // Find the user by ID
        $user = User::find($id);
    
        // Check if the user exists
        if ($user) {
            // Check if the userType is 'doctor'
            if ($user->userType === 'doctor') {
                return response()->json([
                    'status' => 200,
                    'doctor' => $user
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'The user with ID '.$id.' is not a doctor.'
                ], 404);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No user found with ID '.$id
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     * Validates the request data and updates the specified user's details.
     */
    public function update(Request $request, string $id)
    {
        // Validate the incoming request data
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191',
            'password' => 'string|min:8|max:191',
            'userType' => 'required'
        ]);

        // Return validation errors if any
        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->messages()
            ], 400);
        } 

        try {
            // Find the user by ID
            $account = User::find($id);

            // Update the user details
            $account->update([
                'name'=> $request->name,
                'email'=> $request->email,
                'password'=> $request->password,
                'userType'=> $request->userType
            ]);

            // Return success message
            return response()->json([
                'status' => 200,
                'message' => "Account updated Successfully"
            ], 200);

        } catch (\Exception $e) {
            // Return error message in case of an exception
            return response()->json([
                'status' => 500,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Deletes the specified user by ID.
     */
    public function destroy(string $id)
    {
        // Find the user by ID
        $user = User::find($id);

        // Check if the user exists
        if ($user) {
            // Delete the user
            $user->delete();
            return response()->json([
                'status' => 200,
                'message' => "Account deleted successfully"
            ], 200);
        } else {
            // Return error message if user does not exist
            return response()->json([
                'status' => 404,
                'message' => "No valid account to delete."
            ], 404);
        }
    }
}
