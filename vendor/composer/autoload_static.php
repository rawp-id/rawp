<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd14fcd126d108ad1c1c6a9a27a769258
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'C' => 
        array (
            'Core\\' => 5,
            'CLI\\' => 4,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Core',
        ),
        'CLI\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/CLI',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd14fcd126d108ad1c1c6a9a27a769258::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd14fcd126d108ad1c1c6a9a27a769258::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd14fcd126d108ad1c1c6a9a27a769258::$classMap;

        }, null, ClassLoader::class);
    }
}
