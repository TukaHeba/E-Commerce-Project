<?php

namespace App\Services\User;

use App\Models\User\PasswordReset;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ResetPasswordService
{
    public function sendResetLink(array $data)
    {
        // حذف التوكنات القديمة
        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();
        // إنشاء توكن جديد
        $token = Str::random(60);

        // إضافة التوكن في الجدول

        DB::table('password_reset_tokens')->insert([
            'email' => $data['email'],
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        // إرسال البريد الإلكتروني
        Mail::send('emails.password_reset', ['token' => $token], function ($message) use ($data) {
            $message->to($data['email']);
            $message->subject('Reset Password Notification');
        });
    }

    public function resetPassword(array $data)
    {

        // تحقق من صحة التوكن
        $reset = DB::table('password_reset_tokens')->where([
                'email' => $data['email'],
                'token' => $data['token']
            ])->first();

        if (empty($reset)) {
            throw new \Exception('Invalid token or email', 400);
        }

        // تحديث كلمة المرور
        $user = User::where('email', $data['email'])->first();
        $user->password = Hash::make($data['password']);
        $user->save();

        // حذف التوكن بعد الاستخدام
        DB::table('password_resets')->where(['email'=> $data['email']])->delete();
    }
}
