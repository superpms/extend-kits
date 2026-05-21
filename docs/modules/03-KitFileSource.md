# KitFileSource

源码：`composer/pms/extend-kits/src/pms/program/kits/KitFileSource.php`

`KitFileSource` 是 `kit.json` 和 `kit.extra.json` 的主要访问对象。它继承 `KitFile`，而 `KitFile` 继承 `pms\OptionsAccess`，因此支持属性访问、数组访问、迭代、`toArray()` 和 JSON 序列化。

## 构造与字段

构造函数接收：

```php
public function __construct(array $info, string $path = '')
```

它只把当前源码认可的字段写入对象，包括基础信息、`require`、`services`、`manage`、`customer`、`system`、`private` 等。

数组字段通过 `KitFile::get()` 包装成 `KitFile`，所以可以写：

```php
$kit->customer?->autoinstall
$kit->manage?->cfg_pages
$kit->getExtra('scope', [])->toArray()
```

## Extra 状态

- `getExtra()` 懒加载 `<kit>/kit.extra.json`。
- `setExtra()` 修改内存中的 extra。
- `isInstall()` 读取 `extra.installed`。
- `install()` 设置 `extra.installed=true`。
- `save()` 写回已加载的 extra。

## 管理端配置

管理端路径与数据读取：

- SQL: `getManageCfgSqlPath()`
- DB 配置: `getManageCfgDbPath()`、`getManageCfgDb()`、`getManageCfgDbVersion()`
- 页面配置: `getManageCfgPagesPath()`、`getManageCfgPages()`
- 文件配置: `getManageCfgFilePath()`、`getManageCfgFile()`、`setManageCfgFile()`
- 声明式安装动作: `getManageInstall()`

`getManageInstall()` 只保留 `type` 为 `copy` 或 `move`，且 `from`、`to` 都是非空字符串的动作。

## 租户端配置

租户端路径与数据读取：

- DB 配置: `getCustomerCfgDbPath()`、`getCustomerCfgDb()`、`getCustomerCfgDbVersion()`
- 页面配置: `getCustomerCfgPagesPath()`、`getCustomerCfgPages()`

## 服务声明

`getService()` 返回 `services` 或指定 key 的服务节点。它只做数组读取，不做 provider 注册或业务校验。

## 持久化限制

- `save()` 保存管理配置文件时只支持 `json` 和 `php`。
- `getManageCfgFile()` 读取支持 `json`、`php`、`ini`。
- `setManageCfgFile()` 只允许改已有 key，不新增 key。
- 路径字段只有指向真实文件时才返回路径；否则对应 path 方法返回 `null`。
