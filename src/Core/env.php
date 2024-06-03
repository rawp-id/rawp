<?php
// src/Core/Env.php
namespace Core;

class Env {
    public static function get($key, $default = null) {
        $envFilePath = __DIR__ . '/../../.env';

        if (!file_exists($envFilePath)) {
            return $default;
        }

        $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false) {
                list($envKey, $envValue) = explode('=', $line, 2);
                if ($envKey === $key) {
                    return $envValue;
                }
            }
        }

        return $default;
    }
}
