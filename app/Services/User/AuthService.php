<?php

namespace App\Services\User;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Mockery\Exception;

class AuthService
{
    public function register($data)
    {
        $user = User::create($data);
        $token = Auth::login($user);
        return [
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]];
    }

    public function login(array $credentials)
    {
        $token = Auth::attempt($credentials);
        if (!$token) {
            throw new HttpResponseException(Controller::error(null, 'Unauthorized', 401));
        }
        $user = Auth::user();
        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     *   Redirect the user to the Provider authentication page.
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
     * Obtain the user information from Provider.
     * @param string $provider
     * @return array
     * @throws \Exception
     */
    public function handleProviderCallback(string $provider)
    {
        $this->validateProvider($provider);
        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            throw new Exception('Invalid credentials provided.', 422);
        }
        $name = explode(" ", $user->name);
        $userCreated = User::firstOrCreate(['email' => $user->email],
            [
                'first_name' => $name[0],
                'last_name' => $name[count($name) - 1],
                'email' => $user->email
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
            'user' => $userCreated,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]];

    }


    /**
     * @param $provider
     * @return void
     * @throws \Exception
     */
    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['google', 'github'])) {
            throw new \Exception('Please login using google', 422);
        }
    }
}
