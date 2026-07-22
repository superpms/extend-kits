# 能力声明读取与动作分发边界

`kit.json.capabilities` 是标准能力静态声明。`extend-kits` 只提供读取能力，不定义所有能力字段的业务语义，也不承担业务调度。

action 页面和 provider 声明是框架接入能力，不是独立套件类型。实际业务套件应该如何组织 service、business、model、hrb、workflow、command 等目录，读 `server/docs/specification/`。

## 读取能力声明

`KitFileSource::getCapabilities(?string $value = null): array` 从当前 kit 的 `capabilities` 中读取声明：

- 不传 value 时返回全部 `capabilities`。
- 传 value 时返回 `capabilities[].value` 匹配的声明列表。

## capability 与旧 service 迁移

`services` 是旧服务声明字段。已迁移 kit 应把 provider、channel、adapter key、入口信息写入 `capabilities`，再删除 `services`。

已确认迁移关系：

- 文件存储读取 `FILE_SYSTEM_ABILITY` 下的 provider 声明。
- 打印纸张读取 `PRINT_PAPER_ABILITY` 下的 provider 声明，`transport`、`formats`、`device_types` 保留在同一声明项内。
- AI 对话读取 `AI_CHAT_ABILITY` 下的 channel 声明。
- 站外消息读取 `POST_MESSAGE_IN_EXTERNAL_ABILITY` 下的 channel 声明。
- 支付读取 `PAYMENT_ABILITY` 下的 provider 声明。

## Action 分发

配置页面的动作分发由 server 完成。当前 server 通过 `server/core/adapter/kits/KitsAdapter.php` 调用 AdapterApp：

```php
KitsAdapter::runAction($name, $customerUUID, $page, $action, $actionDatum, ...$args);
```

kit 内 action service 通常声明：

```php
public static string $lifecycle = ADAPTER_KITS_ACTION;
public static false|string|array $adapter = 'vendor/name';
public static string $hookClass = KitsAdapter::class;
```

## 框架边界

- `capabilities` 是静态声明，不是自动服务注册表。
- `services` 是待删除旧字段，完成迁移的 kit 不应继续声明。
- `autoload.php` 中的 `Service::register()` 是另一条框架服务注册链。
- connector 与 adapter 只读取自己约定的字段，新增服务类型时必须同步实现消费方。
- 本包不保证未知 capability 可被 server 调用；server 是否调用取决于对应 connector、adapter、registry 的实现状态。
