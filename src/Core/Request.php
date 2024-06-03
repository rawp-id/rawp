<?php
// src/Core/Request.php
namespace Core;

class Request {
    public $method;
    public $path;
    public $params;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = $_SERVER['REQUEST_URI'];
        $this->params = $_REQUEST;
    }
}

