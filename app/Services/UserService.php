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
     * Matches logic: Filter by exact Role, Fuzzy search by Name.
     */
    public function queryUsers(array $params): array
    {
        $builder = $this->userModel->builder();

        // 1. Search Logic (Fuzzy Match)
        if (isset($params['search']) && !empty($params['search'])) {
            $search = $params['search'];
            $scope = $params['scope'] ?? 'all';

            // Based on PHP Native Logic
            if ($scope === 'all') {
                $builder->groupStart()
                        ->like('name', $search)
                        ->orLike('email', $search)
                        ->orLike('role', $search)
                        ->orLike('id', $search)
                        ->groupEnd();
            } else {
                $validColumns = ['name', 'email', 'role', 'id'];
                if (in_array($scope, $validColumns)) {
                    $builder->like($scope, $search);
                } else {
                    $builder->like('name', $search);
                }
            }
        }
        
        // 1.5 Compatibility for 'name' param if search is not used (Backward compatibility)
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
        $dir = $parts[1] ?? 'desc';
        
        // Sanitize field
        $allowedSorts = ['id', 'name', 'email', 'role', 'created_at'];
        if (!in_array($field, $allowedSorts)) {
            $field = 'created_at';
        }
        
        $builder->orderBy($field, $dir);

        // 4. Pagination
        $page = (int)($params['page'] ?? 1);
        $limit = (int)($params['limit'] ?? 10);
        
        // Handle "All" limit
        if ($limit === -1 || $params['limit'] === 'all') {
            $limit = 0; // CI4 paginate uses 0 for no limit? No, we must handle manually.
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
            $results = $query->getResult(User::class); // Map to Entity
            $totalPages = 1;
            $limit = 'all';
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