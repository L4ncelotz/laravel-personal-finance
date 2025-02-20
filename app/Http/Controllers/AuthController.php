<?php 

namespace App\Http\Controllers; 

use App\Models\User; 

use Illuminate\Http\Request; 

use Illuminate\Support\Facades\Auth; 

use Illuminate\Support\Facades\Hash; 

use Illuminate\Validation\ValidationException; 

class AuthController extends Controller 

{ 

    // Register 

    public function register(Request $request) 

    { 

        $validated = $request->validate([ 

            'name' => 'required|string|max:255', 

            'email' => 'required|string|email|max:255|unique:user', 

            'password' => 'required|string|min:8|confirmed', 

        ]); 

 

        $user = User::create([ 

            'name' => $validated['name'], 

            'email' => $validated['email'], 

            'password' => Hash::make($validated['password']), 

        ]); 

 

        $token = $user->createToken('auth_token')->plainTextToken; 

 

        return response()->json([ 

            'user' => $user, 

            'access_token' => $token, 

            'token_type' => 'Bearer', 

        ], 201); 

    } 

 

    //login function 

    public function login(Request $request) 

    { 

        $validated = $request->validate([ 

            'email' => 'required|email', 

            'password' => 'required', 

        ]); 

 

        if (!Auth::attempt($validated)) { 

            throw ValidationException::withMessages([ 

                'email' => ['The provided credentials are incorrect.'], 

            ]); 

        } 

 

        $user = User::where('email', $validated['email'])->first(); 

        $token = $user->createToken('auth_token')->plainTextToken; 

 

        return response()->json([ 

            'user' => $user, 

            'access_token' => $token, 

            'token_type' => 'Bearer', 

        ]); 

    } 

 

    //logout function 

    public function logout(Request $request) 

    { 

        $request->user()->currentAccessToken()->delete(); 

        return response()->json(['message' => 'Logged out successfully']); 

    } 

 

    public function user(Request $request) 

    { 

        return response()->json($request->user()); 

    } 

} 