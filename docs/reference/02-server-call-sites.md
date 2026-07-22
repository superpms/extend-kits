# server 调用点索引

本页列出当前 server 中直接消费 `extend-kits` API 的关键调用点，供修改包行为前回查影响面。

## 平台端 kits 接口

- `server/app/system/platform/http/kits/GetListByManage.php`
  - 读取 `KitsRegistryCenter::localList()`。
  - 使用 `isInstall()`、`getManageCfgPages()`、`getExtra()`。
- `server/app/system/platform/http/kits/GerRegistryInfo.php`
  - 读取 `KitsRegistryCenter::gerRegistry()`。
- `server/app/system/platform/http/kits/extra/Save.php`
  - 写入 `setExtra()` 并 `save()`。
  - `scope` 只能写给存在 `customer` 节点的 kit。
- `server/app/system/platform/http/kits/manage/Install.php`
  - 使用 `getManageCfgSqlPath()`、`getManageCfgDbPath()`、`getManageCfgDb()`、`install()`、`save()`。
- `server/app/system/platform/http/kits/manage/config/GetByKit.php`
  - 使用 `getManageCfgDb()`、`getManageCfgFile()`.
- `server/app/system/platform/http/kits/manage/config/SaveByKit.php`
  - 使用 `getManageCfgFilePath()`、`setManageCfgFile()`、`getManageCfgDbPath()`、`getManageCfgDb()`。
- `server/app/system/platform/http/kits/manage/Action.php`
  - 使用 `getManageCfgPages()`。
  - 通过 `KitsAdapter::runAction()` 分发 action。

## 租户端 kits 接口

- `server/app/system/tenant/http/kits/GetListByCustomer.php`
  - 使用 `localList()`、`isInstall()`、`getExtra('scope')`、`getCustomerCfgPages()`、`customer.autoinstall`。
- `server/app/system/tenant/http/kits/customer/Install.php`
  - 使用 `getExtra('scope')`、`getCustomerCfgDbPath()`、`getCustomerCfgDb()`。
- `server/app/system/tenant/http/kits/customer/Uninstall.php`
  - 使用 `useLocalKit()`、`system`、`localList()`、`getCustomerCfgDb()`。
- `server/app/system/tenant/http/kits/customer/config/GetByKit.php`
  - 使用 `customer`、`getCustomerCfgDb()`。
- `server/app/system/tenant/http/kits/customer/config/SaveByKit.php`
  - 使用 `getExtra('scope')`、`getCustomerCfgDbPath()`、`getCustomerCfgDb()`。
- `server/app/system/tenant/http/kits/customer/Action.php`
  - 使用 `getExtra('scope')`、`getCustomerCfgPages()`。
  - 通过 `KitsAdapter::runAction()` 分发 action。

## Connector 与查询

- `server/app/workflow/basic/query/TriggerReadQuery.php`
  - 使用 `localList()`、`isInstall()`、`getExtra('scope')`、`customer.autoinstall`、`SystemKits` 判断 kit trigger 是否可用于租户。
- `server/app/system/tenant/basic/connector/print/client/PrintServiceClient.php`
  - 读取 `services.print`，并结合 installed、scope、customer install 状态判断 provider 可用性。
- `server/app/system/tenant/basic/connector/filesystem/client/SystemFileServiceClient.php`
  - 读取 `services.filesystem`，并结合 installed、scope、customer install 状态判断 provider 可用性。

## Action Adapter

- `server/core/adapter/kits/KitsAdapter.php`
  - 声明 `ADAPTER_KITS_ACTION` 和 `ADAPTER_KITS_MESSAGE` 容器。
  - `runAction()` 调用 `AdapterApp::run()`。

## 代表性 kit manifest

- `server/kits/kit.json`: 根清单。
- `server/kits/print/feieyun/kit.json`: `services.print`、`manage`、`customer`。
- `server/kits/superpms/localfilesystem/kit.json`: `services.filesystem`、管理配置。
- `server/kits/cloudflare/r2/kit.json`: filesystem provider 和配置检查 action。
- `server/kits/superpms/mailer/kit.json`: `services.trigger.triggers`。
- `server/kits/superpms/login/kit.json`: `manage.install` 与 `cfg_file` 样例。
