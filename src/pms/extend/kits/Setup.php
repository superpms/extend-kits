<?php

namespace pms\extend\kits;

use pms\contract\LifecycleInterface;
use pms\core\boot\Options;
use pms\facade\Path;

class Setup implements LifecycleInterface
{

    protected static string $rootPath;

    public static function start(string $rootPath, \pms\core\boot\Options $bootOptions)
    {

        static::$rootPath = $rootPath;
        static::init($bootOptions);
    }

    protected static function init(Options $bootOptions): void
    {
        $kitsDir = path_join(static::$rootPath, $bootOptions->extend?->kits ?? '/kits');
        Path::mount('kitsRoot', $kitsDir);
    }

}