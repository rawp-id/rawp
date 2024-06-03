<?php
// src/helpers.php

use Core\RedirectResponse;
use Core\View;

function view($viewName, $data = [])
{
    $viewPath = __DIR__ . '/../views';
    $view = new View($viewPath);
    $view->render($viewName, $data);
}

function MsgPage($errorMessage, $code = 500)
{
    http_response_code($code); // Atur status kode untuk kesalahan server internal
    include __DIR__ . '/core/template/error.php'; // Ganti dengan lokasi template error yang sesuai
}


if (!function_exists('redirect')) {
    function redirect($path)
    {
        return new RedirectResponse($path);
    }
}