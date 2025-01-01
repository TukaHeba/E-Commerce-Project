<?php

namespace App\Services\User;

use App\Http\Resources\UserResource;
use App\Models\Cart\Cart;
use App\Models\User\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AuthService
{
    /**
     * User registration and shopping cart creation with register process
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function register(array $data): array
    {
        try {
            $user = DB::transaction(function () use ($data) {
                $user = User::create($data);
                $user->assignRole('customer');
                $cart = Cart::firstOrCreate(['user_id' => $user->id]);
                return $user;
            });
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

    /**
     * login user
     * @param array $credentials
     * @return array
     * @throws AuthenticationException
     */
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

    /**
     * Contact the server using Socialite package
     * @param string $provider
     * @return mixed
     * @throws \Exception
     */

    public function redirectToProvider(string $provider)
    {
        $this->validateProvider($provider);
        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * callback function in socialite
     * @param string $provider
     * @return array
     * @throws \Exception
     */
    public function handleProviderCallback(string $provider): array
    {
        $this->validateProvider($provider);

        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            throw new \Exception('Invalid credentials provided.', 422);
        }

        $userCreated = DB::transaction(function () use ($user, $provider) {
            $name = explode(' ', $user->name);
            $userCreated = User::firstOrCreate(
                ['email' => $user->email],
                [
                    'first_name' => $name[0] ?? null,
                    'last_name' => $name[1] ?? null,
                    'email' => $user->email,
                ]
            );
            $userCreated->assignRole('customer');
            Cart::firstOrCreate(['user_id' => $userCreated->id]);
            $userCreated->providers()->updateOrCreate(
                [
                    'provider' => $provider,
                    'provider_id' => $user->getId(),
                ]
            );
            return $userCreated;
        });

        $token = Auth::login($userCreated);
        return [
            'user' => new UserResource($userCreated),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ];
    }

    /**
     * Check driver
     * @param string $provider
     * @return void
     * @throws \Exception
     */
    protected function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'github'])) {
            throw new \Exception('Please login using Google or GitHub.', 422);
        }
    }
}
