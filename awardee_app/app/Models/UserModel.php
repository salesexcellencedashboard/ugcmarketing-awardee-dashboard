<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'fullname',
        'username',
        'email',
        'password_hash',
        'profile_pic',
        'role',
        'status',
        'created_at',
        'updated_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'fullname'      => 'required|min_length[3]|max_length[150]',
        'username'      => 'required|min_length[3]|max_length[50]',
        'email'         => 'required|valid_email|max_length[120]',
        'password_hash' => 'required|max_length[255]',
        'role'          => 'required|in_list[admin,management]',
        'status'        => 'required|in_list[active,inactive]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    public function findByUsernameOrEmail(string $identity): ?array
    {
        return $this->groupStart()
            ->where('username', $identity)
            ->orWhere('email', $identity)
            ->groupEnd()
            ->where('status', 'active')
            ->first();
    }
}
