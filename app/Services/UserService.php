<?php

namespace App\Services;

use App\Models\UserModel;
use App\Entities\User;
use Exception;

class UserService
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function createUser(array $userBody): User
    {
        if ($this->userModel->isEmailTaken($userBody['email'])) {
            throw new Exception('Email already taken', 400);
        }

        $user = new User($userBody);
        // Entity handles hashing automatically
        
        if (!$this->userModel->save($user)) {
            $errors = implode(', ', $this->userModel->errors());
            throw new Exception($errors, 400);
        }

        return $this->userModel->find($this->userModel->getInsertID());
    }

    public function getUserById($id): ?User
    {
        return $this->userModel->find($id);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userModel->where('email', $email)->first();
    }

    public function updateUserById($userId, array $updateBody): User
    {
        $user = $this->getUserById($userId);
        if (!$user) {
            throw new Exception('User not found', 404);
        }

        if (isset($updateBody['email']) && $this->userModel->isEmailTaken($updateBody['email'], $userId)) {
            throw new Exception('Email already taken', 400);
        }

        $user->fill($updateBody);
        
        if ($user->hasChanged()) {
            if (!$this->userModel->save($user)) {
                 $errors = implode(', ', $this->userModel->errors());
                 throw new Exception($errors, 400);
            }
        }

        return $user;
    }

    public function deleteUserById($userId): void
    {
        $user = $this->getUserById($userId);
        if (!$user) {
            throw new Exception('User not found', 404);
        }
        $this->userModel->delete($userId);
    }

    /**
     * Query users with pagination and sorting.
     */
    public function queryUsers(array $params): array
    {
        $builder = $this->userModel->builder();

        // 1. Search Logic
        if (isset($params['search']) && !empty($params['search'])) {
            $search = $params['search'];
            $scope = $params['scope'] ?? 'all';

            if ($scope === 'all') {
                $builder->groupStart()
                        ->like('name', $search)
                        ->orLike('email', $search)
                        ->orLike('role', $search)
                        ->orWhere('id', $search)
                        ->groupEnd();
            } elseif ($scope === 'id') {
                $builder->where('id', $search);
            } else {
                $validColumns = ['name', 'email', 'role'];
                if (in_array($scope, $validColumns)) {
                    $builder->like($scope, $search);
                } else {
                    $builder->like('name', $search);
                }
            }
        }
        
        // 1.5 Backward compatibility
        if (isset($params['name']) && !empty($params['name'])) {
            $builder->like('name', $params['name']);
        }

        // 2. Strict Filter (Role)
        if (isset($params['role']) && !empty($params['role'])) {
            if (in_array($params['role'], ['user', 'admin'])) {
                $builder->where('role', $params['role']);
            }
        }

        // 3. Sorting
        $sort = $params['sortBy'] ?? 'created_at:desc';
        $parts = explode(':', $sort);
        $field = $parts[0];
        $dir = strtolower($parts[1] ?? 'desc');
        
        $allowedSorts = ['id', 'name', 'email', 'role', 'created_at'];
        if (!in_array($field, $allowedSorts)) {
            $field = 'created_at';
        }
        
        $builder->orderBy($field, $dir);

        // 4. Pagination
        $page = (int)($params['page'] ?? 1);
        $limit = (int)($params['limit'] ?? 10);
        
        // Handle "All" limit or invalid inputs
        if ($limit === -1 || (isset($params['limit']) && $params['limit'] === 'all')) {
            $limit = 0; 
        }

        // Clone builder for counting
        $countBuilder = clone $builder;
        $totalResults = $countBuilder->countAllResults();

        // Fetch Data
        if ($limit > 0) {
            $results = $this->userModel->paginate($limit, 'default', $page);
            $totalPages = ceil($totalResults / $limit);
        } else {
            // Fetch all
            $query = $builder->get();
            $results = $query->getResult(User::class); 
            $totalPages = 1;
            $limit = $totalResults;
        }

        return [
            'results'      => $results,
            'page'         => $page,
            'limit'        => $limit,
            'totalPages'   => $totalPages,
            'totalResults' => $totalResults,
        ];
    }
}