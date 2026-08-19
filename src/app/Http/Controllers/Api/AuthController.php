<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginReqest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginReqest $request): JsonResponse
    {
        $request->validated();
        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid credential provided.',
            ], 401);
        }
        $token = $user->createToken('api_token')->plainTextToken;

        return new JsonResponse([
            'success' => true,
            'token_type' => 'Bearer',
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return new JsonResponse([
            'success' => true,
            'message' => 'Success logged out and token revoked.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'data' => new UserResource($request->user()),
        ]);
    }
}
