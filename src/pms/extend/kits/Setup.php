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
        $kitsDir = path_join(static::$rootPath, $bootOptions->extend?->kits ?? '/kitsroot');
        Path::mount('kitsRoot', $kitsDir);

        $autoloadPackFile = Path::getKitsRoot('autoload.php');
        if (is_file($autoloadPackFile)) {
            $pluginsConfig = include $autoloadPackFile;
            foreach ($pluginsConfig as $item) {
                $autoloadFile = Path::getPluginsRoot($item, "/autoload.php");
                if (is_file($autoloadFile)) {
                    include_once $autoloadFile;
                }
            }
        } else {
            file_create($autoloadPackFile, "<?php\r\n // 需要加载 autoload.php 文件的插件名称集合 \r\n return [];");
        }
    }

}