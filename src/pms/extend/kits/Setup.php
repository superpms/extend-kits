<?php

namespace pms\extend\kits;

use pms\contract\LifecycleInterface;
use pms\facade\BootOptions;
use pms\facade\Path;
use pms\helper\kits\KitsRegistryCenter;

class Setup implements LifecycleInterface
{

    protected static string $rootPath;

    public static function entry(string $rootPath): void
	{
        static::$rootPath = $rootPath;
        static::init();

        /**
         * 加载插件autoload文件
         */
        static::initPluginAutoloadFile();
    }

    protected static function init(): void
    {
        $kitsDir = path_join(static::$rootPath, BootOptions::get_extend('kits','/kits'));
        Path::mount('kitsRoot', $kitsDir);
    }


    protected static function initPluginAutoloadFile(): void{
        $kitRoot = KitsRegistryCenter::useLocalKitFile('kit.json');
        if($kitRoot !== null){
            foreach ($kitRoot->require as $name => $version){
                $defineFile = Path::getKitsRoot($name,'define.php');
                if (is_file($defineFile)) {
                    include_once $defineFile;
                }
                $autoloadFile = Path::getKitsRoot($name,'autoload.php');
                if (is_file($autoloadFile)) {
                    include_once $autoloadFile;
                }
            }
        }

    }
}
