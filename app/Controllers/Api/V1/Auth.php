<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\AuthService;
use App\Services\TokenService;
use App\Services\UserService;
use App\Services\EmailService;
use Exception;
use Config\Services;

class Auth extends BaseController
{
    protected $authService;
    protected $tokenService;
    protected $userService;
    protected $emailService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->tokenService = new TokenService();
        $this->userService = new UserService();
        $this->emailService = new EmailService();
    }

    public function register()
    {
        $rules = [
            'name'     => 'required',
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[8]|validate_password_strength',
        ];

        if (!$this->validate($rules)) {
            return responseError(400, 'Validation Error', $this->validator->getErrors());
        }

        try {
            $body = $this->request->getJSON(true);
            $user = $this->userService->createUser($body);
            $tokens = $this->tokenService->generateAuthTokens($user);

            return responseSuccess([
                'user' => $user->toArray(),
                'tokens' => $tokens
            ], 201);
        } catch (Exception $e) {
            return responseError($e->getCode() ?: 500, $e->getMessage());
        }
    }

    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return responseError(400, 'Validation Error', $this->validator->getErrors());
        }

        try {
            $body = $this->request->getJSON(true);
            $user = $this->authService->loginUserWithEmailAndPassword($body['email'], $body['password']);
            $tokens = $this->tokenService->generateAuthTokens($user);

            return responseSuccess([
                'user' => $user->toArray(),
                'tokens' => $tokens
            ]);
        } catch (Exception $e) {
            return responseError($e->getCode() ?: 500, $e->getMessage());
        }
    }

    public function logout()
    {
        $rules = ['refreshToken' => 'required'];

        if (!$this->validate($rules)) {
            return responseError(400, 'Validation Error', $this->validator->getErrors());
        }

        try {
            $body = $this->request->getJSON(true);
            $this->authService->logout($body['refreshToken']);
            return responseSuccess(null, 204);
        } catch (Exception $e) {
            return responseError($e->getCode() ?: 500, $e->getMessage());
        }
    }

    public function refreshTokens()
    {
        $rules = ['refreshToken' => 'required'];

        if (!$this->validate($rules)) {
            return responseError(400, 'Validation Error', $this->validator->getErrors());
        }

        try {
            $body = $this->request->getJSON(true);
            $tokens = $this->authService->refreshAuth($body['refreshToken']);
            return responseSuccess($tokens);
        } catch (Exception $e) {
            return responseError($e->getCode() ?: 500, $e->getMessage());
        }
    }

    public function forgotPassword()
    {
        $rules = ['email' => 'required|valid_email'];

        if (!$this->validate($rules)) {
            return responseError(400, 'Validation Error', $this->validator->getErrors());
        }

        try {
            $body = $this->request->getJSON(true);
            $resetToken = $this->tokenService->generateResetPasswordToken($body['email']);
            $this->emailService->sendResetPasswordEmail($body['email'], $resetToken);
            return responseSuccess(null, 204);
        } catch (Exception $e) {
            if ($e->getCode() === 404) {
                 return responseSuccess(null, 204);
            }
            return responseError($e->getCode() ?: 500, $e->getMessage());
        }
    }

    public function resetPassword()
    {
        $rules = [
            'token'    => 'required',
            'password' => 'required|min_length[8]|validate_password_strength',
        ];

        $input = $this->request->getJSON(true) ?? [];
        $query = $this->request->getGet() ?? [];
        $data = array_merge($query, $input);

        $this->validator = Services::validation();
        if (!$this->validator->setRules($rules)->run($data)) {
             return responseError(400, 'Validation Error', $this->validator->getErrors());
        }

        try {
            $this->authService->resetPassword($data['token'], $data['password']);
            return responseSuccess(null, 204);
        } catch (Exception $e) {
            return responseError($e->getCode() ?: 500, $e->getMessage());
        }
    }

    public function sendVerificationEmail()
    {
        try {
            $userObj = $this->getAuthUser();
            
            if (!$userObj) {
                return responseError(401, 'User not authenticated');
            }

            // Fetch full user entity to ensure we have the email
            $fullUser = $this->userService->getUserById($userObj->id);

            $verifyToken = $this->tokenService->generateVerifyEmailToken($fullUser);
            $this->emailService->sendVerificationEmail($fullUser->email, $verifyToken);
            return responseSuccess(null, 204);
        } catch (Exception $e) {
            return responseError($e->getCode() ?: 500, $e->getMessage());
        }
    }

    public function verifyEmail()
    {
        $rules = ['token' => 'required'];

        $input = $this->request->getJSON(true) ?? [];
        $query = $this->request->getGet() ?? [];
        $data = array_merge($query, $input);

        $this->validator = Services::validation();
        if (!$this->validator->setRules($rules)->run($data)) {
            return responseError(400, 'Validation Error', $this->validator->getErrors());
        }

        try {
            $this->authService->verifyEmail($data['token']);
            return responseSuccess(null, 204);
        } catch (Exception $e) {
            return responseError($e->getCode() ?: 500, $e->getMessage());
        }
    }
}