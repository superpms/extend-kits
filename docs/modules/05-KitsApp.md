# KitsApp

源码：`composer/pms/extend-kits/src/pms/app/KitsApp.php`

`pms\app\KitsApp` 是给 kit 内类使用的 trait，帮助类根据命名空间定位自身 kit 目录和配置。

## `path()`

```php
final protected static function path(...$suffix): string
```

返回当前 kit 根目录下的路径：

```php
Path::getKitsRoot(static::getName(), ...$suffix)
```

## `getName()`

`getName()` 从调用类命名空间推导 kit 名称：

```php
$class = get_called_class();
$name = explode("\\", $class);
$name = array_slice($name, 1, 2);
return join('/', $name);
```

例如 `kits\superpms\czip\business\CZIPBusiness` 会得到 `superpms/czip`。

## `config()`

`config()` 读取当前 kit 根目录下的 `config.php`，并按 kit 名称缓存。

- 不传参数时返回整份配置。
- 传入 `$name` 时通过 `array_chain()` 读取嵌套配置。
- 配置文件不存在时缓存空数组。

当前 server 样例：`server/kits/superpms/czip/business/CZIPBusiness.php` 使用 `KitsApp` 读取 `config.php` 和定位资源文件。

## 使用约定

使用 `KitsApp` 的类命名空间应以 `kits\<vendor>\<name>\...` 组织，否则 `getName()` 推导出的 kit 名称会不正确。
