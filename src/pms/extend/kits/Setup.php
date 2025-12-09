<?php

namespace pms\extend\kits;

use pms\contract\LifecycleInterface;
use pms\facade\BootOptions;
use pms\facade\Path;

class Setup implements LifecycleInterface
{

    protected static string $rootPath;

    public static function entry(string $rootPath): void
	{
        static::$rootPath = $rootPath;
        static::init();
    }

    protected static function init(): void
    {
        $kitsDir = path_join(static::$rootPath, BootOptions::get_extend('kts','/kits'));
        Path::mount('kitsRoot', $kitsDir);
    }

}