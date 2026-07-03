<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
        'email' => 'required|email|max:150',
        'name' => 'required|string|max:50',
        'password' => 'required|string|min:8|max:60'
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'name' => $validated['name'],
           
            'password' => Hash::make($validated['password']),
        ]); 
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'token'=> $token,
                'id' => $user->id,
                'email' => $user->email,
                'username' => $user->username,
                'created_at' => $user->created_at,
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'bio' => 'sometimes|string|max:500',
            'profile_picture' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        

        if ($request->has('name')) {
            $user->name = $validated['name'];
        }

        if ($request->has('email')) {
            $user->email = $validated['email'];
        }

        if ($request->has('password')) {
            $user->password = Hash::make($validated['password']);
        }

        if($user->save()){
            $user->refresh();
                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully',
                    'data' => [
                        'user' => $user,
                        'updated_fields' => array_keys($validated)
                    ]
                ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function login(Request $request){

        $validated = $request->validate([
        'email' => 'required|email|max:150',
        'password' => 'required|string|max:60'
        ]);


        $user = User::where('email',$validated['email'])->first();
        if(isset($user)){
            if(Hash::check($validated['password'], $user->password)){
                $token = $user->createToken('auth_token')->plainTextToken;
        
                return response()->json([
                    'success' => true,
                    'message' => 'User registered successfully',
                    'data' => [
                        'token'=> $token,
                        'id' => $user->id,
                        'email' => $user->email,
                        'username' => $user->username,
                        'created_at' => $user->created_at,
                    ]
                ], 201);
            }
        }
    }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
    }
}
