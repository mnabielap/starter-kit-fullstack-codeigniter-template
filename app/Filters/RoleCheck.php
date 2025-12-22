<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Auth;
use App\Models\UserModel;

class RoleCheck implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('api_response'); 

        // 1. Check if user is authenticated (JwtAuth should have run first)
        if (!isset($request->user) || !isset($request->user->id)) {
            return responseError(401, 'Please authenticate');
        }

        // 2. Fetch full user to get Role
        $userModel = new UserModel();
        $user = $userModel->find($request->user->id);

        if (!$user) {
            return responseError(401, 'User not found');
        }

        // Attach full user entity to request
        $request->user = $user;

        // 3. Check Permissions
        if (empty($arguments)) {
            return; // No specific permission required
        }

        $requiredRight = $arguments[0];
        $config = new Auth();
        $userRights = $config->roleRights[$user->role] ?? [];

        if (!in_array($requiredRight, $userRights)) {
            // Special Case: Users can manage themselves if no specific right is required,
            // but RoleCheck is usually strictly for admin actions in this starter kit.
            return responseError(403, 'Forbidden');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}