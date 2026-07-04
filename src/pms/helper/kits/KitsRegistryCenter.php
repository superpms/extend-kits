<?php

namespace pms\helper\kits;

use pms\facade\BootOptions;
use pms\facade\Path;
use pms\program\kits\KitFileSource;

class KitsRegistryCenter
{
    /**
     * 确保套件根目录已挂载。
     * @return void
     */
    protected static function ensureKitsRoot(): void
    {
        $path = Path::getKitsRoot('kit.json');
        if (is_string($path) && is_file($path)) {
            return;
        }
        $root = Path::getRoot();
        if (!is_string($root) || $root === '') {
            return;
        }
        Path::mount('kitsRoot', path_join($root, BootOptions::get_extend('kits', '/kits')));
    }

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
        static::ensureKitsRoot();
        $path = Path::getKitsRoot('kit.json');
        if (!is_string($path) || !is_file($path)) {
            return false;
        }
        $content = file_get_contents($path);
        if ($content === false) {
            return false;
        }
        $info = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($info)) {
            return false;
        }
        $info['registry'] = $registry;
        return save_json_config($path, $info);
    }


    public static function useLocalKitFile(...$paths): ?KitFileSource{
        static::ensureKitsRoot();
        $path = Path::getKitsRoot(...$paths);
        if (!is_string($path) || !is_file($path)) {
            return null;
        }
        $content = file_get_contents($path);
        if ($content === false) {
            return null;
        }
        $info = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($info)) {
            return null;
        }
        return static::useKitsFile($info,$path);
    }

    public static function useLocalKit(string $name): ?KitFileSource
    {
        return static::useLocalKitFile($name,'kit.json');
    }

    public static function getExtra(string $name, ?string $key = null): mixed
    {
        $kit = static::useLocalKit($name);
        if ($kit === null) {
            return null;
        }
        if ($key === null) {
            return $kit->getExtra();
        }
        return $kit->getExtra($key);
    }


    public static function localListInfo(): array{
        $root = static::useLocalKitFile('kit.json');
        $list = [];
        if($root === null){
            return $list;
        }
        if (!is_iterable($root->require)) {
            return $list;
        }
        foreach ($root->require as $name => $version) {
            $kit = static::useLocalKit($name);
            if($kit === null){
                continue;
            }
            $view = null;
            if (!empty($kit->view)) {
                $viewPath = Path::getKitsRoot($name, $kit->view);
                if (is_file($viewPath)) {
                    $view = load_json_config($viewPath);
                }
            }
            $list[] = [
                ...$kit->toArray(),
                'icon' => $kit->icon !== null ? '/kits/' . $name . '/' . $kit->icon : null,
                'view' => $view,
            ];
        }
        return $list;
    }

    /**
     * @return KitFileSource[]
     */
    public static function localList(): array
    {
        $root = static::useLocalKitFile('kit.json');
        $kits = [];
        if($root === null){
            return $kits;
        }
        if (!is_iterable($root->require)) {
            return $kits;
        }
        foreach ($root->require as $name => $version){
            $kit = static::useLocalKit($name);
            if($kit !== null){
                $kits[] = $kit;
            }
        }
        return $kits;
    }


    public static function useKitsFile(array $info, string $path = ''): KitFileSource
    {
        return new KitFileSource($info, $path);
    }

}
