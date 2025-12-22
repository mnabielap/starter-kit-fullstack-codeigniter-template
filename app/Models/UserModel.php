<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\User;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = User::class;
    protected $useSoftDeletes   = false; 
    protected $allowedFields    = ['name', 'email', 'password', 'role', 'is_email_verified'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Check if email is taken (excluding a specific user ID for updates)
     */
    public function isEmailTaken(string $email, ?int $excludeUserId = null): bool
    {
        $query = $this->where('email', $email);
        
        if ($excludeUserId) {
            $query->where('id !=', $excludeUserId);
        }

        return !is_null($query->first());
    }
}