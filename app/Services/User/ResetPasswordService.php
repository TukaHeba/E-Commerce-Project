<?php

namespace App\Services\User;

use App\Models\User\PasswordResetToken;
use App\Models\User\User;
use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordService
{
    /**
     * Send a reset password link to the user's email.
     *
     * @param array $data
     * @return void
     * @throws \Exception
     */
    public function sendResetLink(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            throw new \Exception('User with this email does not exist.', 404);
        }

        DB::transaction(function () use (
            $data,
            $user
        ) {
            PasswordResetToken::where('email', $data['email'])->delete();
            $token = Str::random(8);

            PasswordResetToken::create([
                'email' => $data['email'],
                'token' => $token
            ]);
            $user->notify(new ResetPasswordNotification($token));
        });
    }

    /**
     * Reset the user's password using the provided token and new password.
     *
     * @param array $data
     * @return void
     * @throws \Exception If the token is invalid or has expired.
     */
    public function resetPassword(array $data)
    {
        $reset = PasswordResetToken::where('email', $data['email'])->first();
        // Check if the token exists and is valid.
        if (!$reset || !Hash::check($data['token'], $reset->token)) {
            throw new \Exception('Token is invalid.', 400);
        }

        // Ensure the token has not expired (valid for 10 minutes).
        if (Carbon::parse($reset->created_at)->addMinutes(10)->isPast()) {
            throw new \Exception('Token has expired.', 400);
        }

        DB::transaction(function () use ($data) {
            $user = User::where('email', $data['email'])->first();
            $user->password = Hash::make($data['password']);
            $user->save();

            PasswordResetToken::where('email', $data['email'])->delete();
        });
    }
}
