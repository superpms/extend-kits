<?php

namespace pms\extend\kits;

use pms\contract\LifecycleInterface;
use pms\facade\Path;
use pms\program\boot\Options;

class Setup implements LifecycleInterface
{

    protected static string $rootPath;

    public static function entry(string $rootPath, Options $bootOptions): void
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