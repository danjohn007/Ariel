<?php
/**
 * Authentication Controller
 */
class AuthController extends Controller
{
    private $userModel;
    
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->userModel = new User();
    }
    
    /**
     * Login page
     */
    public function login()
    {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
        } else {
            $this->view->set([
                'title' => 'Iniciar Sesión',
                'csrf_token' => $this->generateCSRF()
            ]);
            
            $this->view->render('auth/login');
        }
    }
    
    /**
     * Process login form
     */
    private function processLogin()
    {
        $email = $this->cleanInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $csrfToken = $_POST['csrf_token'] ?? '';
        
        // Validate CSRF token
        if (!$this->validateCSRF($csrfToken)) {
            $this->setErrorMessage('Token de seguridad inválido');
            $this->redirect('/login');
        }
        
        // Validate input
        if (empty($email) || empty($password)) {
            $this->setErrorMessage('Por favor, complete todos los campos');
            $this->redirect('/login');
        }
        
        // Verify credentials
        $user = $this->userModel->verifyPassword($email, $password);
        
        if ($user) {
            // Check if user is active
            if (!$user['is_active']) {
                $this->setErrorMessage('Su cuenta está desactivada. Contacte al administrador.');
                $this->redirect('/login');
            }
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_role'] = $user['role'];
            
            // Log activity
            $this->logActivity($user['id'], 'login', 'user', $user['id'], 'Usuario inició sesión');
            
            $this->setSuccessMessage('¡Bienvenido/a de vuelta!');
            
            // Redirect based on role
            switch ($user['role']) {
                case 'admin':
                    $this->redirect('/admin/dashboard');
                    break;
                case 'coordinator':
                    $this->redirect('/services/manage');
                    break;
                case 'mechanic':
                    $this->redirect('/mechanic/dashboard');
                    break;
                case 'client':
                    $this->redirect('/client/dashboard');
                    break;
                default:
                    $this->redirect('/dashboard');
            }
        } else {
            $this->setErrorMessage('Email o contraseña incorrectos');
            $this->redirect('/login');
        }
    }
    
    /**
     * Logout
     */
    public function logout()
    {
        if ($this->isLoggedIn()) {
            $this->logActivity($_SESSION['user_id'], 'logout', 'user', $_SESSION['user_id'], 'Usuario cerró sesión');
        }
        
        session_destroy();
        $this->setSuccessMessage('Sesión cerrada correctamente');
        $this->redirect('/');
    }
    
    /**
     * Register page
     */
    public function register()
    {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processRegister();
        } else {
            $this->view->set([
                'title' => 'Registrarse',
                'csrf_token' => $this->generateCSRF()
            ]);
            
            $this->view->render('auth/register');
        }
    }
    
    /**
     * Process registration form
     */
    private function processRegister()
    {
        $data = $this->cleanInput($_POST);
        $csrfToken = $_POST['csrf_token'] ?? '';
        
        // Validate CSRF token
        if (!$this->validateCSRF($csrfToken)) {
            $this->setErrorMessage('Token de seguridad inválido');
            $this->redirect('/register');
        }
        
        // Set default role as client
        $data['role'] = 'client';
        
        // Create user
        $result = $this->userModel->createUser($data);
        
        if ($result['success']) {
            $this->setSuccessMessage('¡Registro exitoso! Ya puede iniciar sesión.');
            $this->redirect('/login');
        } else {
            $errors = $result['errors'];
            $errorMessage = 'Error en el registro: ';
            foreach ($errors as $field => $fieldErrors) {
                $errorMessage .= implode(', ', $fieldErrors) . ' ';
            }
            $this->setErrorMessage($errorMessage);
            $this->redirect('/register');
        }
    }
    
    /**
     * Log user activity
     */
    private function logActivity($userId, $action, $entityType, $entityId, $description)
    {
        try {
            $db = Database::getInstance();
            $db->execute(
                "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    $userId,
                    $action,
                    $entityType,
                    $entityId,
                    $description,
                    $_SERVER['REMOTE_ADDR'] ?? '',
                    $_SERVER['HTTP_USER_AGENT'] ?? ''
                ]
            );
        } catch (Exception $e) {
            // Log error but don't break the flow
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }
}