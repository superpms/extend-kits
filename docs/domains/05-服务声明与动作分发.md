# 服务声明读取与动作分发边界

`kit.json.services` 是 provider 元数据声明。`extend-kits` 只提供读取能力，不定义所有服务类型的字段语义，也不定义业务套件内部应该怎么实现 provider。

action 页面和 provider 声明是框架接入能力，不是独立套件类型。实际业务套件应该如何组织 service、business、model、hrb、workflow、command 等目录，读 `server/docs/specification/`。

## 读取服务声明

`KitFileSource::getService(?string $key = null): array` 从当前 kit 的 `services` 中读取节点：

- 不传 key 时返回整个 `services`。
- 传 key 时返回对应服务节点；节点不存在或不是数组时返回空数组。

## 当前 server 消费的服务类型

当前 server 中已经读取的服务类型包括：

- `services.print`: `server/app/system/tenant/basic/connector/print/client/PrintServiceClient.php`
- `services.filesystem`: `server/app/system/tenant/basic/connector/filesystem/client/SystemFileServiceClient.php`
- `services.trigger`: `server/app/workflow/basic/query/TriggerReadQuery.php`

这些调用点都会从 `KitsRegistryCenter::localList()` 开始扫描已启用 kit，并结合 `isInstall()`、`scope`、`customer.autoinstall`、租户安装态等条件判断可用性。

## Action 分发

配置页面的动作分发不由 `extend-kits` 直接完成。当前 server 通过 `server/core/adapter/kits/KitsAdapter.php` 调用 AdapterApp：

```php
KitsAdapter::runAction($name, $customerUUID, $page, $action, $actionDatum, ...$args);
```

kit 内 action service 通常声明：

```php
public static string $lifecycle = ADAPTER_KITS_ACTION;
public static false|string|array $adapter = 'vendor/name';
public static string $hookClass = KitsAdapter::class;
```

代表性 server 样例：

- `server/kits/print/feieyun/service/PrintFeieyunActionService.php`
- `server/kits/cloudflare/r2/service/CloudflareR2ActionService.php`
- `server/kits/superpms/airouter/service/AiRouterActionService.php`

## 框架边界

- `services` 是元数据，不是自动服务注册表。
- `autoload.php` 中的 `Service::register()` 是另一条框架服务注册链。
- connector 或 adapter 只读取自己约定的字段，新增服务类型时必须同步实现消费方。
- 本包不保证未知 `services` key 可被 server 调用；server 是否调用取决于对应 connector、adapter 或 registry 是否已经实现。
