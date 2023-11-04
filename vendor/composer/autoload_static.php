<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc0b3ad3ca608aef6291ffd02aa36676a
{
    public static $prefixLengthsPsr4 = array (
        'H' => 
        array (
            'Hybridauth\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Hybridauth\\' => 
        array (
            0 => __DIR__ . '/..' . '/hybridauth/hybridauth/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'PHPSQLParser\\' => 
            array (
                0 => __DIR__ . '/..' . '/greenlion/php-sql-parser/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc0b3ad3ca608aef6291ffd02aa36676a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc0b3ad3ca608aef6291ffd02aa36676a::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitc0b3ad3ca608aef6291ffd02aa36676a::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitc0b3ad3ca608aef6291ffd02aa36676a::$classMap;

        }, null, ClassLoader::class);
    }
}
