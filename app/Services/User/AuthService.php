<?php

namespace App\Services\User;

use App\Models\User\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\AuthenticationException;
use GuzzleHttp\Exception\ClientException;

class AuthService
{
    public function register(array $data): array
    {
        try {
            $user = User::create($data);
            $token = Auth::login($user);

            return [
                'user' => new UserResource($user),
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Failed to register user: ' . $e->getMessage());
            throw $e;
        }
    }

    public function login(array $credentials): array
    {
        $token = Auth::attempt($credentials);

        if (!$token) {
            throw new AuthenticationException('Invalid credentials provided.');
        }

        $user = Auth::user();

        return [
            'user' => new UserResource($user),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ];
    }

    public function redirectToProvider(string $provider)
    {
        $this->validateProvider($provider);
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback(string $provider): array
    {
        $this->validateProvider($provider);

        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            throw new \Exception('Invalid credentials provided.', 422);
        }

        $name = explode(' ', $user->name);
        $userCreated = User::firstOrCreate(
            ['email' => $user->email],
            [
                'first_name' => $name[0] ?? null,
                'last_name' => $name[1] ?? null,
                'email' => $user->email,
            ]
        );

        $userCreated->providers()->updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $user->getId(),
            ]
        );

        $token = Auth::login($userCreated);

        return [
            'user' => new UserResource($userCreated),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ];
    }

    protected function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'github'])) {
            throw new \Exception('Please login using Google or GitHub.', 422);
        }
    }
}
