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
        $route = self::$routes[$method][$uri] ?? null;

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
            call_user_func($action);
            return;
        }

        [$controller, $method] = $action;

        $controller = new $controller();

        $controller->$method();
    }
}
