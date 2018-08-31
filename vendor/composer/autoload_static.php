<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit14d867af41b80ad05a9ea22d69e7fe7f
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Predis\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Predis\\' => 
        array (
            0 => __DIR__ . '/..' . '/predis/predis/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit14d867af41b80ad05a9ea22d69e7fe7f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit14d867af41b80ad05a9ea22d69e7fe7f::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
