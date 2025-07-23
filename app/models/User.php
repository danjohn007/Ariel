<?php
/**
 * User Model
 */
class User extends Model
{
    protected $table = 'users';
    
    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        return $this->findOne('email = ?', [$email]);
    }
    
    /**
     * Create new user
     */
    public function createUser($data)
    {
        // Validate data
        $errors = $this->validateUser($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Generate email verification token
        $data['email_verification_token'] = bin2hex(random_bytes(32));
        
        try {
            $userId = $this->create($data);
            return ['success' => true, 'user_id' => $userId];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['general' => 'Error al crear usuario']];
        }
    }
    
    /**
     * Verify user password
     */
    public function verifyPassword($email, $password)
    {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    /**
     * Update password
     */
    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, [
            'password' => $hashedPassword,
            'password_reset_token' => null,
            'password_reset_expires' => null
        ]);
    }
    
    /**
     * Generate password reset token
     */
    public function generatePasswordResetToken($email)
    {
        $user = $this->findByEmail($email);
        if (!$user) {
            return false;
        }
        
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->update($user['id'], [
            'password_reset_token' => $token,
            'password_reset_expires' => $expires
        ]);
        
        return $token;
    }
    
    /**
     * Verify password reset token
     */
    public function verifyPasswordResetToken($token)
    {
        $user = $this->findOne(
            'password_reset_token = ? AND password_reset_expires > NOW()',
            [$token]
        );
        return $user;
    }
    
    /**
     * Get users by role
     */
    public function findByRole($role)
    {
        return $this->findAll('role = ? AND is_active = 1', [$role]);
    }
    
    /**
     * Get available mechanics
     */
    public function getAvailableMechanics()
    {
        // This is a simplified version - in reality you'd check current assignments
        return $this->findByRole('mechanic');
    }
    
    /**
     * Validate user data
     */
    private function validateUser($data, $isUpdate = false)
    {
        $rules = [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email'],
            'role' => ['required']
        ];
        
        if (!$isUpdate) {
            $rules['password'] = ['required'];
        }
        
        $errors = $this->validate($data, $rules);
        
        // Check for duplicate email
        if (!empty($data['email'])) {
            $existingUser = $this->findByEmail($data['email']);
            if ($existingUser && (!$isUpdate || $existingUser['id'] != $data['id'])) {
                $errors['email'][] = 'Este email ya está registrado';
            }
        }
        
        // Validate role
        $validRoles = ['admin', 'coordinator', 'mechanic', 'client'];
        if (!empty($data['role']) && !in_array($data['role'], $validRoles)) {
            $errors['role'][] = 'Rol no válido';
        }
        
        return $errors;
    }
    
    /**
     * Get user's full name
     */
    public function getFullName($user)
    {
        return $user['first_name'] . ' ' . $user['last_name'];
    }
    
    /**
     * Get role display name
     */
    public function getRoleDisplayName($role)
    {
        $roles = [
            'admin' => 'Administrador',
            'coordinator' => 'Coordinador',
            'mechanic' => 'Mecánico',
            'client' => 'Cliente'
        ];
        return $roles[$role] ?? $role;
    }
}