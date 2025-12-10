<?php

namespace pms\helper\kits;

use pms\facade\Path;
use pms\program\kits\KitFileResource;

class KitsRegistryCenter
{

    public static function gerRegistry(): ?string
    {
        $root = static::useLocalKitFile('kit.json');
        if($root === null){
            return null;
        }
        return $root->registry === null ? null : $root->registry;
    }

    public static function setRegistry(string $registry): bool|int
    {
        $root = static::useLocalKitFile('kit.json');
        if($root === null){
            return false;
        }
        $root->registry = $registry;
        return $root->save();
    }


    public static function useLocalKitFile(...$paths): ?KitFileResource{
        $path = Path::getKitsRoot(...$paths);
        if (!file_exists($path)) {
            return null;
        }
        $info = json_decode(file_get_contents($path), true);
        return static::useKitsFile($info,$path);
    }

    public static function useLocalKit(string $name): ?KitFileResource
    {
        return static::useLocalKitFile($name,'kit.json');
    }

    public static function getExtra(string $name, ?string $key = null): ?array
    {
        $path = Path::getKitsRoot($name, 'kit.json');
        if (!file_exists($path)) {
            return null;
        }
        $root = static::useKitsFile($path);
        if (empty($root->extra) || !is_array($root->extra)) {
            return null;
        }
        if ($key === null) {
            return $root->extra;
        }
        return $root->extra[$name] ?? null;
    }


    public static function list(): array{
        $root = static::useLocalKitFile('kit.json');
        $list = [];
        foreach ($root->require as $name => $version) {
            $kit = static::useLocalKit($name);
            $view = null;
            if (!empty($kit->view)) {
                $viewPath = Path::getKitsRoot($name, $kit->view);
                $view = json_decode(file_get_contents($viewPath), true);
            }
            $list[] = [
                ...$kit->toArray(),
                'icon' => $kit->icon !== null ? '/kits/' . $name . '/' . $kit->icon : null,
                'view' => $view,
            ];
        }
        return $list;
    }

    public static function getDbConfig(string $kitName)
    {
        $kit = static::useLocalKit($kitName);
        if($kit === null){
            return [];
        }
        $dbFile = Path::getKitsRoot($kitName, $kit->config_db);
        if (!is_file($dbFile)) {
            return [];
        }
        return json_decode(file_get_contents($dbFile), true);
    }

    public static function useKitsFile(array $info, string $path = ''): KitFileResource
    {
        return new KitFileResource($info, $path);
    }

}