<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ResetPassword\ResetPasswordRequest;
use App\Http\Requests\User\ResetPassword\sendLinkRequest;
use App\Services\User\ResetPasswordService;

class PasswordResetController extends Controller
{
    /**
     * @var ResetPasswordService
     */
    protected $passwordResetService;


    /**
     * PasswordResetController constructor
     *
     * @param ResetPasswordService $passwordResetService
     */
    public function __construct(ResetPasswordService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * send link in email for forget password
     *
     * @param sendLinkRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLink(sendLinkRequest $request)
    {
        $data = $request->validationData();
        $this->passwordResetService->sendResetLink($data);
        return self::success(null, 'Reset password link sent.');
    }

    /**
     * reset password with verification code
     *
     * @param ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validationData();
        $this->passwordResetService->resetPassword($data);
        return self::success('Password has been reset.');
    }

}
