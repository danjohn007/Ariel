<?php
/**
 * Router class for handling URL routing
 */
class Router 
{
    private $routes = [];
    
    /**
     * Add a route
     */
    public function add($route, $params = [])
    {
        // Convert route pattern to regex
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[^/]+)', $route);
        $route = '#^' . $route . '$#i';
        
        $this->routes[$route] = $params;
    }
    
    /**
     * Match a URL to a route
     */
    public function match($url)
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                return $params;
            }
        }
        return false;
    }
    
    /**
     * Dispatch the request
     */
    public function dispatch($url)
    {
        $params = $this->match($url);
        
        if ($params === false) {
            // No route found - 404
            http_response_code(404);
            include APP_PATH . '/views/errors/404.php';
            return;
        }
        
        $controller = $params['controller'];
        $action = $params['action'];
        
        $controller = $this->convertToStudlyCaps($controller);
        $action = $this->convertToCamelCase($action);
        
        $controllerClass = $controller . 'Controller';
        $controllerFile = APP_PATH . '/controllers/' . $controllerClass . '.php';
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            
            if (class_exists($controllerClass)) {
                $controllerObject = new $controllerClass($params);
                
                if (method_exists($controllerObject, $action)) {
                    $controllerObject->$action();
                } else {
                    throw new Exception("Method $action not found in controller $controller");
                }
            } else {
                throw new Exception("Controller class $controllerClass not found");
            }
        } else {
            throw new Exception("Controller file $controllerFile not found");
        }
    }
    
    /**
     * Convert string to StudlyCaps (PascalCase)
     */
    private function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }
    
    /**
     * Convert string to camelCase
     */
    private function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }
}