<?php
// src/Core/Response.php
namespace Core;

class Response
{
    public static function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public static function html($html, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: text/html');
        echo $html;
    }

    public static function text($text, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: text/plain');
        echo $text;
    }
}

class RedirectResponse
{
    protected $path;
    protected $sessionData = [];

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function with($key, $value)
    {
        $this->sessionData[$key] = $value;
        return $this;
    }

    public function send()
    {
        // Lakukan redirect ke path yang ditentukan
        header("Location: {$this->path}");

        // Simpan data sesi jika ada
        foreach ($this->sessionData as $key => $value) {
            $_SESSION[$key] = $value;
        }

        exit;
    }
}