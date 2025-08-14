<?php
session_start();
require_once '../config.php';

spl_autoload_register(function ($class_name) {
    $paths = ['../core/', '../app/controllers/', '../app/models/'];
    foreach ($paths as $path) {
        $file = $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once '../core/Router.php';
$router = new Router();
$router->dispatch();
?>