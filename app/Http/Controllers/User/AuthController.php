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

    /**
     * Register a new user and return a token.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request->validated());
        return self::success($data, 'Registered successfully!', 201);
    }

    /**
     * Authenticate a user and return a token.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $response = $this->authService->login($request->validated());
        return self::success($response, 'Logged in successfully', 200);
    }

    /**
     * Log out the authenticated user.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();
        return self::success(null, 'Logged out successfully');
    }

    /**
     * Refresh the user's authentication token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        $user = Auth::user();
        return self::success([
            'user' => new UserResource($user),
            'role' => $user->getRoleNames()->first(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * Redirect to the OAuth provider for login
     *
     * @param string $provider
     * @return mixed
     * @throws \Exception
     */
    public function redirectToProvider(string $provider): mixed
    {
        return $this->authService->redirectToProvider($provider);
    }

    /**
     * Handle the callback from the OAuth provider
     *
     * @param string $provider
     * @return JsonResponse
     * @throws \Exception
     */
    public function handleProviderCallback(string $provider): JsonResponse
    {
        $data = $this->authService->handleProviderCallback($provider);
        return self::success($data);
    }
}
