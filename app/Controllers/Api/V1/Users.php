<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\UserService;
use Exception;
use Config\Services;

class Users extends BaseController
{
    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function create()
    {
        $rules = [
            'name'     => 'required',
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[8]|validate_password_strength',
            'role'     => 'required|in_list[user,admin]',
        ];

        if (!$this->validate($rules)) {
            return responseError(400, 'Validation Error', $this->validator->getErrors());
        }

        try {
            $body = $this->request->getJSON(true);
            $user = $this->userService->createUser($body);
            return responseSuccess($user->toArray(), 201);
        } catch (Exception $e) {
            return responseError($e->getCode() ?: 500, $e->getMessage());
        }
    }

    public function index()
    {
        $params = $this->request->getGet();
        
        try {
            $result = $this->userService->queryUsers($params);
            
            // Format results with entities
            $result['results'] = array_map(function($user) {
                return $user->toArray();
            }, $result['results']);

            return responseSuccess($result);
        } catch (Exception $e) {
            return responseError($e->getCode() ?: 500, $e->getMessage());
        }
    }

    public function show($userId = null)
    {
        try {
            $user = $this->userService->getUserById($userId);
            
            if (!$user) {
                return responseError(404, 'User not found');
            }

            $currentUser = $this->getAuthUser();
            
            // Strict check: Regular users can only see THEMSELVES.
            if ($userId != $currentUser->id && $currentUser->role !== 'admin') {
                return responseError(403, 'Forbidden');
            }

            return responseSuccess($user->toArray());
        } catch (Exception $e) {
            return responseError($e->getCode() ?: 500, $e->getMessage());
        }
    }

    public function update($userId = null)
    {
        $body = $this->request->getJSON(true);

        $allRules = [
            'email'    => 'valid_email',
            'password' => 'min_length[8]|validate_password_strength',
            'name'     => 'min_length[1]',
            'role'     => 'in_list[user,admin]',
        ];

        $rules = array_intersect_key($allRules, $body);

        if (!empty($rules)) {
            $this->validator = Services::validation();
            
            if (!$this->validator->setRules($rules)->run($body)) {
                 return responseError(400, 'Validation Error', $this->validator->getErrors());
            }
        }

        try {
            $currentUser = $this->getAuthUser();
            
            if ($userId != $currentUser->id && $currentUser->role !== 'admin') {
                 return responseError(403, 'Forbidden');
            }

            $user = $this->userService->updateUserById($userId, $body);
            return responseSuccess($user->toArray());
        } catch (Exception $e) {
            return responseError($e->getCode() ?: 500, $e->getMessage());
        }
    }

    public function delete($userId = null)
    {
        try {
             $currentUser = $this->getAuthUser();

             if ($userId != $currentUser->id && $currentUser->role !== 'admin') {
                  return responseError(403, 'Forbidden');
             }

            $this->userService->deleteUserById($userId);
            return responseSuccess(null, 204);
        } catch (Exception $e) {
            return responseError($e->getCode() ?: 500, $e->getMessage());
        }
    }
}