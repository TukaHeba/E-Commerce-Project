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
     * register user
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
     * login for user
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
     * logout user
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        return self::success(null, 'Logged out successfully');
    }

    /**
     * refresh token
     * @return JsonResponse
     */
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

    /**
     * register user by Oauth
     * @param string $provider
     * @return mixed
     * @throws \Exception
     */
    public function redirectToProvider(string $provider)
    {
        return $this->authService->redirectToProvider($provider);
    }

    /**
     * callback api in Oauth
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
