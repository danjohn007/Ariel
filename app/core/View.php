<?php
/**
 * View class for rendering templates
 */
class View
{
    private $data = [];
    
    /**
     * Set data for the view
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
    }
    
    /**
     * Render a view template
     */
    public function render($template, $layout = 'default')
    {
        $templateFile = APP_PATH . '/views/' . $template . '.php';
        $layoutFile = APP_PATH . '/views/layouts/' . $layout . '.php';
        
        if (!file_exists($templateFile)) {
            throw new Exception("Template file not found: $templateFile");
        }
        
        // Extract data to variables
        extract($this->data);
        
        // Start output buffering
        ob_start();
        
        // Include the template
        include $templateFile;
        
        // Get template content
        $content = ob_get_clean();
        
        // If layout is specified and exists, use it
        if ($layout && file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            echo $content;
        }
    }
    
    /**
     * Render template without layout
     */
    public function renderPartial($template)
    {
        $templateFile = APP_PATH . '/views/' . $template . '.php';
        
        if (!file_exists($templateFile)) {
            throw new Exception("Template file not found: $templateFile");
        }
        
        // Extract data to variables
        extract($this->data);
        
        // Include the template
        include $templateFile;
    }
    
    /**
     * Escape HTML entities
     */
    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generate URL
     */
    public function url($path = '')
    {
        return APP_URL . '/' . ltrim($path, '/');
    }
    
    /**
     * Include asset (CSS/JS)
     */
    public function asset($path)
    {
        return $this->url('public/' . ltrim($path, '/'));
    }
    
    /**
     * Display flash messages
     */
    public function flashMessages()
    {
        $html = '';
        
        if (isset($_SESSION['success_message'])) {
            $html .= '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            $html .= $this->escape($_SESSION['success_message']);
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            $html .= '</div>';
            unset($_SESSION['success_message']);
        }
        
        if (isset($_SESSION['error_message'])) {
            $html .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            $html .= $this->escape($_SESSION['error_message']);
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            $html .= '</div>';
            unset($_SESSION['error_message']);
        }
        
        return $html;
    }
    
    /**
     * CSRF token field
     */
    public function csrfField()
    {
        $token = $_SESSION['csrf_token'] ?? '';
        return '<input type="hidden" name="csrf_token" value="' . $this->escape($token) . '">';
    }
}