<?php
// src/Core/Route.php

namespace Core;

class Route
{
    protected static $routes = [];
    protected static $middleware = [];

    public static function get($path, $handler)
    {
        self::addRoute('GET', $path, $handler);
    }

    public static function post($path, $handler)
    {
        self::addRoute('POST', $path, $handler);
    }

    public static function put($path, $handler)
    {
        self::addRoute('PUT', $path, $handler);
    }

    public static function delete($path, $handler)
    {
        self::addRoute('DELETE', $path, $handler);
    }

    public static function middleware($middleware)
    {
        self::$middleware[] = $middleware;
        return new static;
    }

    protected static function addRoute($method, $path, $handler)
    {
        self::$routes[] = compact('method', 'path', 'handler');
    }


    public static function redirect($from, $to, $status = 302)
    {
        // Jika URL tidak dimulai dengan '/', tambahkan tanda '/' di depannya
        $from = strpos($from, '/') !== 0 ? '/' . $from : $from;
        $to = strpos($to, '/') !== 0 ? '/' . $to : $to;

        // Lakukan redirect dengan kode status HTTP yang ditentukan
        http_response_code($status);
        header("Location: $to");
        exit;
    }

    public static function dispatch($method, $path)
    {
        foreach (self::$routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                $handler = $route['handler'];

                // Jalankan middleware sebelum mengeksekusi handler
                foreach (self::$middleware as $middleware) {
                    $middlewareClass = new $middleware();
                    $handler = $middlewareClass->handle($handler);
                }

                if (is_array($handler) && count($handler) == 2 && is_string($handler[0]) && is_string($handler[1])) {
                    // Buat instance dari kelas controller dan panggil method-nya secara dinamis
                    $class = $handler[0];
                    $method = $handler[1];
                    $controller = new $class();
                    return call_user_func_array([$controller, $method], []);
                } elseif (is_callable($handler)) {
                    // Panggil handler jika callable
                    return $handler();
                }

                // Jika handler bukan dalam format yang diharapkan, tanggapi dengan 500 Internal Server Error
                MsgPage("Handler format is invalid: " . print_r($handler, true));
            }
        }

        // Jika tidak ada rute yang cocok, tanggapi dengan 404 Not Found
        MsgPage("Handler format is invalid: " . print_r($handler, true));
    }
}
