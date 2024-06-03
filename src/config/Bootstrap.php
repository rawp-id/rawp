<?php
require_once __DIR__ . '/../../autoload.php';

use Core\Database;
use Core\Request;
use Core\Route;
use Core\View;

new Database();

$request = new Request();
require_once __DIR__ . '/../../routes/web.php';

Route::dispatch($request->method, $request->path);
