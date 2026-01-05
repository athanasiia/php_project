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
            if($route === $uri) {
                return $this->callHandler($handler);
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    private function callHandler($handler) {
        $controllerClass = $handler[0];
        $action = $handler[1];

        $controllerInstance = new $controllerClass();
        return $controllerInstance->$action();
    }
}