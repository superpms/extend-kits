# Setup

源码：`composer/pms/extend-kits/src/pms/extend/kits/Setup.php`

`pms\extend\kits\Setup` 是本包的生命周期入口，实现 `pms\contract\LifecycleInterface`。

## 入口

```php
public static function entry(string $rootPath): void
```

入口由 `bin/autoload.php` 挂到 `LIFECYCLE_BOOT`。`$rootPath` 是宿主项目根目录。

## `init()`

`init()` 计算并挂载 kits 根目录：

```php
$kitsDir = path_join(static::$rootPath, BootOptions::get_extend('kits','/kits'));
Path::mount('kitsRoot', $kitsDir);
```

宿主项目可以通过 `boot.json` 的 `extend.kits` 改变目录，默认是 `/kits`。

## `initPluginAutoloadFile()`

这个方法读取根 `kit.json`，遍历 `require`，按顺序尝试加载：

1. `<kit>/define.php`
2. `<kit>/autoload.php`

如果根清单读取失败，方法直接结束。单个加载文件不存在时跳过。

## 开发注意

- 这条链依赖 `KitsRegistryCenter::useLocalKitFile('kit.json')`。
- 根清单的 `require` 是启动加载的唯一依据。
- 不要把未写入根清单的 kit 期待成自动加载。
