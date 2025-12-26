<?php

namespace pms\app;

use pms\facade\Path;

trait KitsApp{

    protected static array $config = [];

    final protected static function path(...$suffix): string{
        return Path::getKitsRoot(static::getName(),...$suffix);
    }

    final protected static function getName(): string
    {
        $class = get_called_class();
        $name = explode("\\", $class);
        $name = array_slice($name, 1, 2);
        return join('/', $name);
    }

    final protected static function config(?string $name = null, $default = null){
        $pluginName = static::getName();
        if (isset(static::$config[$pluginName])) {
            $config = static::$config[$pluginName];
        } else {
            $configPath = Path::getKitsRoot($pluginName, 'config.php');
            if (!is_file($configPath)) {
                return static::$config[$pluginName] = [];
            }
            $config = static::$config[$pluginName] = include $configPath;
        }
        if ($name === null) {
            return $config;
        }
        $data = array_chain($config, $name);
        return $data !== null ? $data : $default;
    }

}