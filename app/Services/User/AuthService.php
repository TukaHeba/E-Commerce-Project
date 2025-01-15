<?php

namespace App\Services\User;

use App\Http\Resources\UserResource;
use App\Models\Cart\Cart;
use App\Models\Photo\Photo;
use App\Models\User\User;
use App\Services\Photo\PhotoService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class AuthService
{

    protected PhotoService $photoService;

    public function __construct(PhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    /**
     * Register a new user, create a cart, and store the avatar if provided.
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        $user = DB::transaction(function () use ($data) {
            $user = User::create($data);
            $user->assignRole('customer');
            Cart::create(['user_id' => $user->id]);
            if (isset($data['avatar'])) {
                $result = $this->photoService->storePhoto($data['avatar'], $user, 'avatars');
            } else {
                $this->photoService->addDefaultAvatar($user);
            }
            return $user;
        });
        $token = Auth::login($user);
        return [
            'user' => new UserResource($user),
            'role' => $user->getRoleNames()->first(),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ];
    }

    /**
     * Authenticate a user and return a token.
     *
     * @param array $credentials
     * @return array
     * @throws AuthenticationException
     */
    public function login(array $credentials): array
    {
        $token = Auth::attempt($credentials);

        if (!$token) {
            throw new \Exception('Invalid credentials provided.');
        }
        $user = Auth::user();
        return [
            'user' => new UserResource($user),
            'role' => $user->getRoleNames()->first(),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ];
    }

    /**
     * Redirect the user to the OAuth provider.
     *
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
     * Handle the OAuth provider callback and authenticate the user.
     *
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
            'role' => $user->getRoleNames()->first(),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ];
    }

    /**
     * Validate the OAuth provider.
     *
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
