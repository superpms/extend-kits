<?php

namespace pms\extend\kits;

use pms\contract\LifecycleInterface;
use pms\facade\Path;

class Setup implements LifecycleInterface
{

    protected static string $rootPath;

    public static function start(string $rootPath, \pms\program\boot\Options $bootOptions)
    {

        static::$rootPath = $rootPath;
        static::init($bootOptions);
    }

    protected static function init(\pms\program\boot\Options $bootOptions): void
    {
        $kitsDir = path_join(static::$rootPath, $bootOptions->extend?->kits ?? '/kits');
        Path::mount('kitsRoot', $kitsDir);
    }

}