<?php

namespace Core;

class Router
{
    private static $routes = [];

    public static function get($uri, $action, $middlewares = [])
    {
        self::$routes['GET'][$uri] = [
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public static function post($uri, $action, $middlewares = [])
    {
        self::$routes['POST'][$uri] = [
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public static function resolve($uri, $method)
{
    // First try exact match
    $route = self::$routes[$method][$uri] ?? null;
    $params = [];

    // If no exact match, try pattern matching
    if (!$route) {
        foreach (self::$routes[$method] as $pattern => $registeredRoute) {
            // Convert {id} style params to regex
            $regex = preg_replace('/\{[a-z]+\}/', '([^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches); // remove full match
                $params = $matches;
                $route = $registeredRoute;
                break;
            }
        }
    }

    if (!$route) {
        http_response_code(404);
        echo "404 Not Found";
        return;
    }

    foreach ($route['middlewares'] as $middleware) {
        $middlewareClass = "App\\Middlewares\\" . $middleware;
        (new $middlewareClass)->handle();
    }

    $action = $route['action'];

    if (is_callable($action)) {
        call_user_func_array($action, $params);
        return;
    }

    [$controller, $method] = $action;
    $controller = new $controller();
    $controller->$method(...$params);
}
}
