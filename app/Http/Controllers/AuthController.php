<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginUserRequest $request)
    {
        $request->validated();

        if (!Auth::attempt($request->only('email', 'password')))
        {
            return response()->json(["message" => "Invalid credentials"], 401);
        }

        $user = User::where('email', $request->email)->first();

        $data = [
            'user' => $user,
            'token' => $user->createToken('API token')->plainTextToken,
        ];

        return response()->json($data);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete(); //összes a belépett user-hez tartozó tokent törli
        //Auth::user()->currentAccessToken()->delete(); // csak azt a tokent törli amivel azonosította magát

        return response()->json(["message" => "Logged out"], 200);
    }

    public function register(StoreUserRequest $request)
    {
        $request->validated();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $data = [
            'user' => $user,
            'token' => $user->createToken('API token')->plainTextToken,
        ];

        return response()->json($data, 201);
    }
}
