<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = User::all();
        if ($accounts->count() > 0) {
            return response()->json([
                'status' => 200,
                'UserAccounts' => $accounts
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'Message' => 'No Records Found'
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), 
        [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email',
            'password' => 'required|string|min:8|max:191',
            'userType' => 'required|string|max:50'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->messages()
            ], 400);
        } 

        try {
            User::create([
                'name'=> $request->name,
                'email'=> $request->email,
                'password'=> Hash::make($request->password),
                'userType'=> $request->userType
            ]);

            return response()->json([
                'status' => 200,
                'message' => "Account Created Successfully"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'status' => 200,
                'user' => $user
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => "No user found."
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = Validator::make($request->all(), 
        [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8|max:191',
            'userType' => 'required|string|max:50'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->messages()
            ], 400);
        } 

        try {
            $account = User::findOrFail($id);

            $updateData = [
                'name'=> $request->name,
                'email'=> $request->email,
                'userType'=> $request->userType
            ];

            if ($request->has('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $account->update($updateData);

            return response()->json([
                'status' => 200,
                'message' => "Account updated Successfully"
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => "No valid account found to update."
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json([
                'status' => 200,
                'message' => "Account deleted successfully"
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => "No valid account to delete."
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);
        }
    }
}
