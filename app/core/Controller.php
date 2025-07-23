<?php
/**
 * Base Controller class
 */
class Controller
{
    protected $params = [];
    protected $view;
    
    public function __construct($params = [])
    {
        $this->params = $params;
        $this->view = new View();
    }
    
    /**
     * Check if user is logged in
     */
    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get current user
     */
    protected function getCurrentUser()
    {
        if ($this->isLoggedIn()) {
            $userModel = new User();
            return $userModel->findById($_SESSION['user_id']);
        }
        return null;
    }
    
    /**
     * Check if user has specific role
     */
    protected function hasRole($role)
    {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === $role;
    }
    
    /**
     * Require login
     */
    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Require specific role
     */
    protected function requireRole($role)
    {
        $this->requireLogin();
        if (!$this->hasRole($role)) {
            header('Location: /dashboard');
            exit;
        }
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }
    
    /**
     * Send JSON response
     */
    protected function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Validate CSRF token
     */
    protected function validateCSRF($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Generate CSRF token
     */
    protected function generateCSRF()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Clean input data
     */
    protected function cleanInput($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'cleanInput'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Show success message
     */
    protected function setSuccessMessage($message)
    {
        $_SESSION['success_message'] = $message;
    }
    
    /**
     * Show error message
     */
    protected function setErrorMessage($message)
    {
        $_SESSION['error_message'] = $message;
    }
}