<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\User\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request->validated());
        return self::success($data, 'Registered successfully!', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $response = $this->authService->login($request->validated());
        return self::success($response, 'Logged in successfully', 200);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        return self::success(null, 'Logged out successfully');
    }

    public function refresh(): JsonResponse
    {
        return self::success([
            'user' => new UserResource(Auth::user()),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function redirectToProvider(string $provider)
    {
        return $this->authService->redirectToProvider($provider);
    }

    public function handleProviderCallback(string $provider): JsonResponse
    {
        $data = $this->authService->handleProviderCallback($provider);
        return self::success($data);
    }
}
