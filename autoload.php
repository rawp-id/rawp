<?php
// autoload.php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/helpers.php';
spl_autoload_register(function ($class) {
    // Namespace prefix to base directory mappings
    $prefixes = [
        'Core\\' => __DIR__ . '/src/',
        'App\\' => __DIR__ . '/app/',
    ];

    // Loop through the prefixes array to find a match
    foreach ($prefixes as $prefix => $base_dir) {
        $len = strlen($prefix);
        // Check if the class uses the prefix
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        // Get the relative class name
        $relative_class = substr($class, $len);

        // Replace the namespace prefix with the base directory,
        // replace namespace separators with directory separators
        // in the relative class name, append with .php
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        // If the file exists, require it
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
