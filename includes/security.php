<?php
/**
 * Security Helper Functions
 * XSS protection, CSRF tokens, input validation
 */

class Security {
    
    /**
     * Escape HTML to prevent XSS attacks
     */
    public static function escape($data) {
        if (is_array($data)) {
            return array_map([self::class, 'escape'], $data);
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate email address
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate name (letters, spaces, accents)
     */
    public static function validateName($name) {
        return preg_match('/^[a-zA-ZÀ-ÿ\s]{2,50}$/u', $name);
    }
    
    /**
     * Validate password strength
     */
    public static function validatePassword($password) {
        // At least 8 characters, one uppercase, one lowercase, one number
        return strlen($password) >= 8 && 
               preg_match('/[A-Z]/', $password) && 
               preg_match('/[a-z]/', $password) && 
               preg_match('/[0-9]/', $password);
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token']) || 
            !isset($_SESSION['csrf_token_time']) || 
            time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_LIFETIME) {
            
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && 
               isset($_SESSION['csrf_token_time']) &&
               time() - $_SESSION['csrf_token_time'] <= CSRF_TOKEN_LIFETIME &&
               hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Get client IP address
     */
    public static function getClientIP() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Clean input data
     */
    public static function cleanInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'cleanInput'], $data);
        }
        return trim(stripslashes($data));
    }
    
    /**
     * Hash password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
    }
    
    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Rate limiting check
     */
    public static function checkRateLimit($key, $maxAttempts = 5, $timeWindow = 300) {
        if (!isset($_SESSION['rate_limit'])) {
            $_SESSION['rate_limit'] = [];
        }
        
        $now = time();
        $attempts = $_SESSION['rate_limit'][$key] ?? [];
        
        // Remove old attempts outside time window
        $attempts = array_filter($attempts, function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });
        
        $_SESSION['rate_limit'][$key] = $attempts;
        
        return count($attempts) < $maxAttempts;
    }
    
    /**
     * Add rate limit attempt
     */
    public static function addRateLimitAttempt($key) {
        if (!isset($_SESSION['rate_limit'])) {
            $_SESSION['rate_limit'] = [];
        }
        
        if (!isset($_SESSION['rate_limit'][$key])) {
            $_SESSION['rate_limit'][$key] = [];
        }
        
        $_SESSION['rate_limit'][$key][] = time();
    }
}