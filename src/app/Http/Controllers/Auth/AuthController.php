<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        if (!$token = $request->getToken()) {
            return response()->json(['error' => 'Incorrect email or password'], 401);
        }
        
        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        event(new Registered($user));

        $token = auth('api')->tokenById($user->getKey());
        auth('api')->login($user);
        
        return $this->createNewToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();
        return response()->json(['message' => 'User successfully signed out'], 200);
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        return $this->createNewToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     */
    private function createNewToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()
        ], 201);
    }
}
