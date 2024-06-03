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
        $handler = null;
    
        // Pisahkan jalur dan parameter (jika ada)
        $pathParts = explode('?', $path);
        $pathWithoutParams = $pathParts[0]; // Ambil jalur tanpa parameter
    
        // Cari rute yang sesuai dengan metode dan jalur tanpa parameter yang diberikan
        foreach (self::$routes as $route) {
            $routeParts = explode('?', $route['path']);
            $routePathWithoutParams = $routeParts[0]; // Ambil jalur rute tanpa parameter
            if ($route['method'] === $method && $routePathWithoutParams === $pathWithoutParams) {
                $handler = $route['handler'];
                break;
            }
        }
    
        // Jika handler tidak ditemukan, tanggapi dengan 404 Not Found
        if ($handler === null) {
            http_response_code(404);
            echo "404 Not Found";
            exit;
        }
    
        // Jalankan middleware sebelum mengeksekusi handler
        foreach (self::$middleware as $middleware) {
            $middlewareClass = new $middleware();
            $handler = $middlewareClass->handle($handler);
        }
    
        // Buat objek Request
        $request = new Request();
    
        // Cek apakah handler dalam format yang diharapkan
        if (is_array($handler) && count($handler) == 2 && is_string($handler[0]) && is_string($handler[1])) {
            // Buat instance dari kelas controller dan panggil method-nya secara dinamis
            $class = $handler[0];
            $method = $handler[1];
            $controller = new $class();
            return call_user_func_array([$controller, $method], [$request]);
        } elseif (is_callable($handler)) {
            // Panggil handler jika callable
            return $handler($request);
        }
    
        // Jika handler tidak sesuai dengan format yang diharapkan, tanggapi dengan 500 Internal Server Error
        http_response_code(500);
        echo "500 Internal Server Error";
        exit;
    }    

}
