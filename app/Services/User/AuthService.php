<?php

namespace App\Services\User;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

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
}
