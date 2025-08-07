<?php

namespace pms\extend\kits;

class Installation{

    protected static function getNamespace(string $name, string $file): string|false{
        $name = "$name/$file";
        $name = str_replace(".", "\\", $name);
        $name = str_replace("//", "\\", $name);
        $pathinfo = str_replace("/", "\\", $name);
        $name = trim($pathinfo, "\\");
        $namespace = "\\kits\\" . $name;
        if (!class_exists($namespace)) {
            return false;
        }
        return $namespace;
    }

    protected static function runHook(string $namespace, string $hookName): void{
        $class = new $namespace();
        if (method_exists($class, $hookName)) {
            $class->$hookName();
        }
    }


    protected static function runSetup(string $name,string $hook): bool{
        $namespace = static::getNamespace($name, "/Setup");
        if(!$namespace){
            return false;
        }
        static::runHook($namespace,$hook);
        return true;
    }

    public static function install($name): void{
        // download
        // unpack

        // setup
        static::runSetup($name,'install');
    }

}