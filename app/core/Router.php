<?php
class Router {
    protected $routes = [];

    public function add($method, $uri, $controller, $action, $middleware = []) {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    public function dispatch($uri, $method) {
        $urlPath = parse_url($uri, PHP_URL_PATH);

        // Base folder (/jwt_api) remove panna logic
        $baseFolder = '/jwt_api'; 
        if (strpos($urlPath, $baseFolder) === 0) {
            $urlPath = substr($urlPath, strlen($baseFolder));
        }

        foreach ($this->routes as $route) {
            // 1. Convert route URI to a regex pattern

            $pattern = "@^" . preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $route['uri']) . "$@";

            // 2. Match Method and URI pattern 
            if (preg_match($pattern, $urlPath, $matches) && $route['method'] === $method) {
                
                // Remove the first element (the full string match)
                array_shift($matches);

                // 3. Run Middlewares (Json, Auth) 
                foreach ($route['middleware'] as $middleware) {
                    $m = new $middleware();
                    $m->handle();
                }

                $controllerName = $route['controller'];
                $action = $route['action'];

                // 4. Initialize controller matum pass matches as arguments 
                $controller = new $controllerName();
                
                // Pass the dynamic ID (e.g., $id) directly to the controller method
                return call_user_func_array([$controller, $action], $matches);
            }
        }

        // 5. Handle 404 errors
        http_response_code(404);
        echo json_encode(["message" => "Route not found", "requested_path" => $urlPath]); 
    }
}