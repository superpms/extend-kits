# 生命周期与目录挂载

`extend-kits` 的装载发生在框架启动期，不发生在单个业务请求的中途。

## Composer 接入

`composer/pms/extend-kits/composer.json` 声明：

- `autoload.files`: `bin/autoload.php`
- `autoload.psr-4`: `pms\\` 到 `src/pms/`
- `extra.pms.dir`: `/kits`
- `extra.pms.copy`: `/kits/kit.json` 来自 `/resource/kit.json`

`bin/autoload.php` 在 `pms\hook\LifecycleHook` 存在时执行：

```php
LifecycleHook::mount(LIFECYCLE_BOOT, Setup::class);
```

## 启动期行为

`pms\extend\kits\Setup::entry(string $rootPath)` 做两件事：

1. 调用 `init()` 挂载 kits 根路径。
2. 调用 `initPluginAutoloadFile()` 读取根清单并加载 kit 文件。

`init()` 使用：

```php
$kitsDir = path_join($rootPath, BootOptions::get_extend('kits', '/kits'));
Path::mount('kitsRoot', $kitsDir);
```

当前 server 样例 `server/boot.json` 中 `extend.kits` 是 `/kits`，因此项目运行时的 kits 根目录是 `server/kits`。

## 加载顺序

`initPluginAutoloadFile()` 读取 `Path::getKitsRoot('kit.json')`，遍历根清单 `require`：

1. 若 `Path::getKitsRoot($name, 'define.php')` 存在，先 `include_once`。
2. 若 `Path::getKitsRoot($name, 'autoload.php')` 存在，再 `include_once`。

这里的方法名里仍使用 `Plugin` 字样，但当前代码实际加载的是 kit 的 `define.php` 和 `autoload.php`。

## 缺失处理

- 根 `kit.json` 缺失、不可读或 JSON 非法时，注册中心返回 `null`，启动阶段不会继续加载子 kit。
- 某个 kit 的 `define.php` 或 `autoload.php` 缺失时只跳过对应文件。
- 子 kit 没有写入根 `kit.json.require` 时不会自动参与启动加载。
