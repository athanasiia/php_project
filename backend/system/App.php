<?php

namespace system;

class App
{
    private $routes;
    public function setRoutes($routes)
    {
        $this->routes = $routes;
    }

    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = preg_replace('/\{(\w+)}/', '([^/]+)', $route);
            $pattern = "#^{$pattern}$#";

            if (preg_match($pattern, $uri, $matches)) {
                $params = array_slice($matches, 1);
                return $this->callHandler($handler, $params);
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    private function callHandler($handler, $params = []) {
        $controllerClass = $handler[0];
        $action = $handler[1];

        $controllerInstance = new $controllerClass();
        return call_user_func_array([$controllerInstance, $action], $params);
    }
}