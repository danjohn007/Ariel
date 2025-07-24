<?php
/**
 * User Model
 * Handles user authentication, registration, and management
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/security.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Authenticate user login
     */
    public function login($email, $password) {
        $email = Security::cleanInput($email);
        $password = Security::cleanInput($password);
        
        // Validate input
        if (!Security::validateEmail($email)) {
            return ['success' => false, 'message' => 'Email inválido'];
        }
        
        // Check rate limiting
        $rateLimitKey = 'login_' . Security::getClientIP();
        if (!Security::checkRateLimit($rateLimitKey, MAX_LOGIN_ATTEMPTS, LOCKOUT_DURATION)) {
            return ['success' => false, 'message' => 'Demasiados intentos de login. Intente más tarde.'];
        }
        
        try {
            // Get user data
            $sql = "SELECT id, email, password_hash, nombre, rol, activo, intentos_login, bloqueado_hasta 
                    FROM usuarios WHERE email = ? AND activo = 1";
            $user = $this->db->fetch($sql, [$email]);
            
            if (!$user) {
                Security::addRateLimitAttempt($rateLimitKey);
                return ['success' => false, 'message' => 'Credenciales inválidas'];
            }
            
            // Check if user is locked out
            if ($user['bloqueado_hasta'] && strtotime($user['bloqueado_hasta']) > time()) {
                return ['success' => false, 'message' => 'Cuenta bloqueada temporalmente'];
            }
            
            // Verify password
            if (!Security::verifyPassword($password, $user['password_hash'])) {
                // Increment failed attempts
                $this->incrementFailedAttempts($user['id']);
                Security::addRateLimitAttempt($rateLimitKey);
                return ['success' => false, 'message' => 'Credenciales inválidas'];
            }
            
            // Reset failed attempts and update last access
            $this->resetFailedAttempts($user['id']);
            
            // Create session
            $this->createSession($user);
            
            // Log activity
            $this->logActivity($user['id'], 'login', 'Usuario inició sesión');
            
            return [
                'success' => true, 
                'message' => 'Login exitoso',
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'nombre' => $user['nombre'],
                    'rol' => $user['rol']
                ]
            ];
            
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del servidor'];
        }
    }
    
    /**
     * Register new user
     */
    public function register($email, $password, $nombre, $rol = 'visitante') {
        $email = Security::cleanInput($email);
        $password = Security::cleanInput($password);
        $nombre = Security::cleanInput($nombre);
        $rol = Security::cleanInput($rol);
        
        // Validate input
        if (!Security::validateEmail($email)) {
            return ['success' => false, 'message' => 'Email inválido'];
        }
        
        if (!Security::validateName($nombre)) {
            return ['success' => false, 'message' => 'Nombre inválido'];
        }
        
        if (!Security::validatePassword($password)) {
            return ['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número'];
        }
        
        // Validate role
        if (!in_array($rol, ['admin', 'analista', 'visitante'])) {
            $rol = 'visitante';
        }
        
        // Restrict admin creation
        if ($rol === 'admin') {
            if (!in_array($email, ADMIN_EMAILS)) {
                return ['success' => false, 'message' => 'No autorizado para crear usuarios administrador'];
            }
            
            // Check if admin already exists
            $adminCount = $this->db->fetch("SELECT COUNT(*) as count FROM usuarios WHERE rol = 'admin'");
            if ($adminCount['count'] > 0 && !in_array($email, ADMIN_EMAILS)) {
                return ['success' => false, 'message' => 'Ya existe un administrador en el sistema'];
            }
        }
        
        try {
            // Check if email already exists
            $existingUser = $this->db->fetch("SELECT id FROM usuarios WHERE email = ?", [$email]);
            if ($existingUser) {
                return ['success' => false, 'message' => 'El email ya está registrado'];
            }
            
            // Hash password
            $passwordHash = Security::hashPassword($password);
            
            // Insert user
            $sql = "INSERT INTO usuarios (email, password_hash, nombre, rol) VALUES (?, ?, ?, ?)";
            $this->db->execute($sql, [$email, $passwordHash, $nombre, $rol]);
            
            $userId = $this->db->lastInsertId();
            
            // Log activity
            $this->logActivity($userId, 'register', 'Usuario registrado');
            
            return [
                'success' => true, 
                'message' => 'Usuario registrado exitosamente',
                'user_id' => $userId
            ];
            
        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del servidor'];
        }
    }
    
    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $sql = "SELECT id, email, nombre, rol, activo, fecha_creacion, ultimo_acceso 
                FROM usuarios WHERE id = ? AND activo = 1";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Create user session
     */
    private function createSession($user) {
        // Start session with unique name for user role
        session_name('ariel_' . $user['rol'] . '_session');
        session_start();
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Store user data in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['ip_address'] = Security::getClientIP();
        
        // Store session in database
        $sessionId = session_id();
        $sql = "INSERT INTO user_sessions (id, usuario_id, ip_address, user_agent, expires_at) 
                VALUES (?, ?, ?, ?, ?)";
        $expiresAt = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $this->db->execute($sql, [$sessionId, $user['id'], Security::getClientIP(), $userAgent, $expiresAt]);
    }
    
    /**
     * Verify session
     */
    public function verifySession() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['last_activity'])) {
            return false;
        }
        
        // Check session timeout
        if (time() - $_SESSION['last_activity'] > SESSION_LIFETIME) {
            $this->logout();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        
        // Verify session in database
        $sessionId = session_id();
        $sql = "SELECT usuario_id FROM user_sessions WHERE id = ? AND expires_at > NOW()";
        $session = $this->db->fetch($sql, [$sessionId]);
        
        if (!$session || $session['usuario_id'] != $_SESSION['user_id']) {
            $this->logout();
            return false;
        }
        
        // Update session activity in database
        $sql = "UPDATE user_sessions SET last_activity = NOW() WHERE id = ?";
        $this->db->execute($sql, [$sessionId]);
        
        return true;
    }
    
    /**
     * Logout user
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            // Log activity
            $this->logActivity($_SESSION['user_id'], 'logout', 'Usuario cerró sesión');
            
            // Remove session from database
            $sessionId = session_id();
            $sql = "DELETE FROM user_sessions WHERE id = ?";
            $this->db->execute($sql, [$sessionId]);
        }
        
        // Clear session data
        session_unset();
        session_destroy();
        session_regenerate_id(true);
    }
    
    /**
     * Increment failed login attempts
     */
    private function incrementFailedAttempts($userId) {
        $sql = "UPDATE usuarios SET intentos_login = intentos_login + 1 WHERE id = ?";
        $this->db->execute($sql, [$userId]);
        
        // Lock account after max attempts
        $user = $this->db->fetch("SELECT intentos_login FROM usuarios WHERE id = ?", [$userId]);
        if ($user['intentos_login'] >= MAX_LOGIN_ATTEMPTS) {
            $lockoutTime = date('Y-m-d H:i:s', time() + LOCKOUT_DURATION);
            $sql = "UPDATE usuarios SET bloqueado_hasta = ? WHERE id = ?";
            $this->db->execute($sql, [$lockoutTime, $userId]);
        }
    }
    
    /**
     * Reset failed login attempts
     */
    private function resetFailedAttempts($userId) {
        $sql = "UPDATE usuarios SET intentos_login = 0, bloqueado_hasta = NULL, ultimo_acceso = NOW() WHERE id = ?";
        $this->db->execute($sql, [$userId]);
    }
    
    /**
     * Log user activity
     */
    public function logActivity($userId, $action, $description = null) {
        $sql = "INSERT INTO log_actividad (usuario_id, accion, descripcion, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?)";
        $params = [
            $userId,
            $action,
            $description,
            Security::getClientIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
        $this->db->execute($sql, $params);
    }
    
    /**
     * Clean expired sessions
     */
    public function cleanExpiredSessions() {
        $sql = "DELETE FROM user_sessions WHERE expires_at < NOW()";
        $this->db->execute($sql);
    }
}