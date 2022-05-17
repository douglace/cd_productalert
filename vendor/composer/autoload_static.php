<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcabf8277f4f9e9cade149c57723fd71c
{
    public static $prefixLengthsPsr4 = array (
        'V' => 
        array (
            'Vex6\\CdProductalert\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Vex6\\CdProductalert\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcabf8277f4f9e9cade149c57723fd71c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcabf8277f4f9e9cade149c57723fd71c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitcabf8277f4f9e9cade149c57723fd71c::$classMap;

        }, null, ClassLoader::class);
    }
}
