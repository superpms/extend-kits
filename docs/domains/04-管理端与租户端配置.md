# 管理端与租户端配置

`extend-kits` 只解析配置入口，实际安装、读取、保存发生在 server 端 HTTP 接口中。

## 管理端节点

单 kit `kit.json.manage` 当前支持：

- `autoinstall`
- `install`
- `cfg_sql`
- `cfg_db`
- `cfg_file`
- `cfg_pages`

`KitFileSource` 对应方法：

- `getManageCfgSqlPath()`
- `getManageCfgDbPath()` / `getManageCfgDb()` / `getManageCfgDbVersion()`
- `getManageCfgFilePath()` / `getManageCfgFile()` / `setManageCfgFile()`
- `getManageCfgPagesPath()` / `getManageCfgPages()`
- `getManageInstall()`

当前 server 平台端 install 接口会执行 `cfg_sql` 中的 SQL，并用 `cfg_db` 恢复平台配置 registry，然后写入 `extra.installed=true`。它不会执行 `manage.install`。

## 租户端节点

单 kit `kit.json.customer` 当前支持：

- `autoinstall`
- `cfg_db`
- `cfg_pages`

`KitFileSource` 对应方法：

- `getCustomerCfgDbPath()` / `getCustomerCfgDb()` / `getCustomerCfgDbVersion()`
- `getCustomerCfgPagesPath()` / `getCustomerCfgPages()`

当前 server 租户端 install 接口会检查 `extra.scope`，再按租户类型把 `customer.cfg_db` 写入租户 registry 或平台 registry，并创建 `SystemKits` 安装记录。

## 页面配置

`cfg_pages` 指向 kit 内 JSON 页面配置。当前 server action 接口只允许 `pages[page].origin === 'action'` 的页面进入 `KitsAdapter::runAction()`。

管理端 action 使用平台 UUID：

```php
KitsAdapter::runAction($name, TENANT_PLATFORM_UUID, $page, $realAction, $datum, $id, $context);
```

租户端 action 使用当前租户 UUID：

```php
KitsAdapter::runAction($name, $this->userTenantUUID(), $page, $realAction, $datum, $id, $context);
```

## 安装态差异

- 平台安装态：`kit.extra.json.installed`。
- 租户安装态：server 数据表模型 `SystemKits`。
- 租户可见性：需要平台已安装、`extra.scope` 允许当前租户类型、存在 `customer.cfg_pages`，并结合 `customer.autoinstall` 或 `SystemKits` 判断 active。
