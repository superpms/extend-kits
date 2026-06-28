# extend-kits 框架能力文档索引

本目录是 `superpms/extend-kits` composer 包的框架层文档入口。这里解释这个包支持什么能力、如何发现 kit、如何读取 `kit.json` / `kit.extra.json`、如何挂载 kitsRoot、如何把元数据交给 server 消费。

实际 server 业务型套件的目录结构、层级职责、内部功能、server 调用契约和返回规范，不写在这里；这些内容统一读 `server/docs/specification/`。

## 先读

1. [domains/01-responsibility-boundary.md](domains/01-responsibility-boundary.md)
2. [internals/01-bootstrap-install-projection.md](internals/01-bootstrap-install-projection.md)
3. [reference/01-kit-json-fields.md](reference/01-kit-json-fields.md)

## 按问题读

- 理解启动、目录挂载、autoload 链路：读 [domains/02-lifecycle-mount.md](domains/02-lifecycle-mount.md) 和 [modules/01-Setup.md](modules/01-Setup.md)。
- 理解根清单、单 kit 清单、extra 状态：读 [domains/03-kit-manifest-status.md](domains/03-kit-manifest-status.md)。
- 理解平台端和租户端配置入口如何消费这个包：读 [domains/04-admin-tenant-config.md](domains/04-admin-tenant-config.md)。
- 理解 `services` 与 action 分发的框架边界：读 [domains/05-service-action-dispatch.md](domains/05-service-action-dispatch.md)。
- 了解怎样让框架发现并加载 kit：读 [how-to/01-load-kit.md](how-to/01-load-kit.md)。
- 了解框架怎样读取配置页面和安装声明：读 [how-to/02-read-config-install.md](how-to/02-read-config-install.md)。
- 了解框架怎样读取服务声明并接入 action 分发：读 [how-to/03-read-service-action.md](how-to/03-read-service-action.md)。
- 查某个类的方法职责：读 `modules/` 下对应文档。
- 查 server 实际调用点：读 [reference/02-server-call-sites.md](reference/02-server-call-sites.md)。

## 目录结构

- `domains/`：按功能领域解释这个包解决什么问题。
- `modules/`：按包内源码模块解释公开入口和行为边界。
- `how-to/`：面向框架接入点的能力说明和最小示例。
- `internals/`：启动、投影、缓存、持久化等内部机制。
- `reference/`：字段、调用点、约定清单。

## 不在这里读

- 业务套件的目录规范、层级职责、业务功能、返回契约不写入本目录。
- 一次性排错记录不写入本目录。
- 插件装载文档不写入本目录。
