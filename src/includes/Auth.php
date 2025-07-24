<?php
/**
 * Clase para manejo de autenticación y sesiones
 * Sistema de Análisis de Precios y Programa de Obra
 */

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->initSession();
    }
    
    /**
     * Inicializar sesión con nombre personalizado
     */
    private function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path' => '/',
                'domain' => '',
                'secure' => false, // Cambiar a true en HTTPS
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            session_start();
        }
    }
    
    /**
     * Autenticar usuario
     */
    public function login($email, $password) {
        try {
            $sql = "SELECT id, nombre, apellidos, email, password, rol, activo 
                    FROM usuarios 
                    WHERE email = ? AND activo = 1";
            
            $user = $this->db->fetchOne($sql, [$email]);
            
            if ($user && password_verify($password, $user['password'])) {
                // Actualizar último acceso
                $this->updateLastAccess($user['id']);
                
                // Establecer datos de sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellidos'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['rol'];
                $_SESSION['login_time'] = time();
                $_SESSION['ip_address'] = $this->getClientIP();
                
                // Regenerar ID de sesión por seguridad
                session_regenerate_id(true);
                
                // Registrar login en logs de auditoría
                $this->logAction('login', 'usuarios', $user['id'], null, [
                    'email' => $email,
                    'ip' => $_SESSION['ip_address']
                ]);
                
                return [
                    'success' => true,
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['nombre'] . ' ' . $user['apellidos'],
                        'email' => $user['email'],
                        'role' => $user['rol']
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Credenciales inválidas'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error en el sistema de autenticación'
            ];
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        if ($this->isLoggedIn()) {
            $this->logAction('logout', 'usuarios', $_SESSION['user_id']);
        }
        
        // Limpiar variables de sesión
        $_SESSION = [];
        
        // Destruir cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destruir sesión
        session_destroy();
        
        return true;
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Obtener datos del usuario actual
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }
    
    /**
     * Verificar permisos por rol
     */
    public function hasRole($role) {
        return $this->isLoggedIn() && $_SESSION['user_role'] === $role;
    }
    
    /**
     * Verificar si el usuario tiene uno de los roles especificados
     */
    public function hasAnyRole($roles) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        return in_array($_SESSION['user_role'], $roles);
    }
    
    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin() {
        return $this->hasRole(ROLE_ADMIN);
    }
    
    /**
     * Verificar si el usuario puede escribir (admin o analista)
     */
    public function canWrite() {
        return $this->hasAnyRole([ROLE_ADMIN, ROLE_ANALYST]);
    }
    
    /**
     * Verificar si el usuario puede solo leer
     */
    public function canOnlyRead() {
        return $this->hasRole(ROLE_VISITOR);
    }
    
    /**
     * Actualizar último acceso del usuario
     */
    private function updateLastAccess($userId) {
        $sql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
        $this->db->execute($sql, [$userId]);
    }
    
    /**
     * Obtener IP del cliente
     */
    private function getClientIP() {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    
                    if (filter_var($ip, FILTER_VALIDATE_IP, 
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Registrar acción en logs de auditoría
     */
    public function logAction($action, $table = null, $recordId = null, $oldData = null, $newData = null) {
        try {
            $data = [
                'usuario_id' => $_SESSION['user_id'] ?? null,
                'accion' => $action,
                'tabla_afectada' => $table,
                'registro_id' => $recordId,
                'datos_anteriores' => $oldData ? json_encode($oldData) : null,
                'datos_nuevos' => $newData ? json_encode($newData) : null,
                'ip_address' => $this->getClientIP(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ];
            
            $this->db->insert('logs_auditoria', $data);
        } catch (Exception $e) {
            // Log error but don't stop execution
            if (APP_DEBUG) {
                error_log("Error logging action: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Hashear contraseña
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verificar contraseña
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Requerir autenticación (redirige si no está autenticado)
     */
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: /');
            exit;
        }
    }
    
    /**
     * Requerir rol específico
     */
    public function requireRole($role) {
        $this->requireAuth();
        
        if (!$this->hasRole($role)) {
            header('HTTP/1.1 403 Forbidden');
            header('Location: /dashboard.php?error=access_denied');
            exit;
        }
    }
    
    /**
     * Requerir cualquiera de los roles especificados
     */
    public function requireAnyRole($roles) {
        $this->requireAuth();
        
        if (!$this->hasAnyRole($roles)) {
            header('HTTP/1.1 403 Forbidden');
            header('Location: /dashboard.php?error=access_denied');
            exit;
        }
    }
    
    /**
     * Obtener URL del dashboard según el rol del usuario
     */
    public function getDashboardUrl($role = null) {
        $userRole = $role ?? ($_SESSION['user_role'] ?? null);
        
        switch ($userRole) {
            case ROLE_ADMIN:
                return '/admin/dashboard.php';
            case ROLE_ANALYST:
                return '/analista/dashboard.php';
            case ROLE_VISITOR:
                return '/visitante/dashboard.php';
            default:
                return '/dashboard.php';
        }
    }
    
    /**
     * Registrar nuevo usuario (solo para analista y visitante)
     */
    public function register($nombre, $apellidos, $email, $password, $rol = ROLE_VISITOR) {
        try {
            // Validar que no se pueda registrar como admin
            if ($rol === ROLE_ADMIN) {
                return [
                    'success' => false,
                    'message' => 'No se permite registrar usuarios administradores desde el sistema web.'
                ];
            }
            
            // Validar que el rol sea válido para registro web
            if (!in_array($rol, [ROLE_ANALYST, ROLE_VISITOR])) {
                return [
                    'success' => false,
                    'message' => 'Rol no válido para registro.'
                ];
            }
            
            // Verificar si el email ya existe
            $sql = "SELECT id FROM usuarios WHERE email = ?";
            $existingUser = $this->db->fetchOne($sql, [$email]);
            
            if ($existingUser) {
                return [
                    'success' => false,
                    'message' => 'Ya existe un usuario registrado con este correo electrónico.'
                ];
            }
            
            // Hashear contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertar nuevo usuario
            $userData = [
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'email' => $email,
                'password' => $hashedPassword,
                'rol' => $rol,
                'activo' => 1
            ];
            
            $userId = $this->db->insert('usuarios', $userData);
            
            // Registrar en logs de auditoría
            $this->logAction('register', 'usuarios', $userId, null, [
                'email' => $email,
                'rol' => $rol,
                'ip' => $this->getClientIP()
            ]);
            
            return [
                'success' => true,
                'message' => 'Usuario registrado exitosamente.',
                'user_id' => $userId
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al registrar el usuario: ' . $e->getMessage()
            ];
        }
    }
}