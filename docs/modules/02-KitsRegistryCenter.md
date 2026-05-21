# KitsRegistryCenter

源码：`composer/pms/extend-kits/src/pms/helper/kits/KitsRegistryCenter.php`

`pms\helper\kits\KitsRegistryCenter` 是 kit 清单读取和本地列表查询入口。

## 方法概览

- `gerRegistry(): ?string`
- `setRegistry(string $registry): bool|int`
- `useLocalKitFile(...$paths): ?KitFileSource`
- `useLocalKit(string $name): ?KitFileSource`
- `getExtra(string $name, ?string $key = null): mixed`
- `localListInfo(): array`
- `localList(): array`
- `useKitsFile(array $info, string $path = ''): KitFileSource`

## 路径规则

所有本地读取都通过 `Path::getKitsRoot(...)` 解析。调用这个类之前，`Setup` 必须已经在启动期挂载过 `kitsRoot`。

## 根清单读取

`useLocalKitFile('kit.json')` 读取根 `kit.json`：

- 路径不存在返回 `null`。
- 文件不可读返回 `null`。
- JSON 非法或不是数组返回 `null`。
- 成功时返回 `KitFileSource`。

## 子 kit 读取

`useLocalKit($name)` 等价于：

```php
useLocalKitFile($name, 'kit.json')
```

`localList()` 读取根清单 `require`，逐个返回存在且合法的子 kit `KitFileSource`。

## Registry

`gerRegistry()` 从根清单读取 `registry`。方法名当前源码拼写是 `gerRegistry`。

`setRegistry()` 写回根 `kit.json` 的 `registry` 字段。

## `localListInfo()`

`localListInfo()` 会把每个 kit 转成数组，并补充：

- `icon`: `/kits/<name>/<icon>`
- `view`: 尝试读取 `$kit->view` 指向的 JSON

当前 server 主要使用 `localList()`，不是 `localListInfo()`。
