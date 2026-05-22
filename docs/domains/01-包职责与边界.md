# 包职责与边界

`superpms/extend-kits` 是套件装载基础包。它负责让宿主项目中的 `kits` 目录进入框架运行时，并提供读取、保存、查询 kit 元数据的 API。

本包文档只说明框架层能支持什么、能读取什么、会把哪些元数据暴露给宿主 server。实际业务型套件应该采用什么目录结构、每层代码职责是什么、server 如何调用它、它应返回什么，属于 `server/docs/specification/` 的范围。

## 包内职责

- 在 Composer autoload 阶段注册启动生命周期。
- 在框架 boot 阶段挂载 `kitsRoot`。
- 读取根 `kit.json`，根据 `require` 列表加载每个 kit 的 `define.php` 和 `autoload.php`。
- 读取单 kit 的 `kit.json`、`kit.extra.json`、管理端配置、租户端配置、页面配置和服务声明。
- 提供 `KitsApp` trait，帮助 kit 内类按命名空间解析自身 kit 目录和 `config.php`。

## 包外职责

- HTTP 接口、平台权限、租户权限、租户安装态由 `server/app/system/.../kits` 负责。
- action 分发由 `server/core/adapter/kits/KitsAdapter.php` 与 kit 自己注册的 `ServiceApp` 负责。
- filesystem、print、workflow trigger 等 provider 的业务消费由各自 server connector 负责。
- `extra.pms` 的安装期投影由终端解释器中的 vendor install hook 执行，不在本包内执行。

## 重要边界

- 本包不扫描所有目录；它只信任根 `kit.json` 的 `require`。
- 本包不直接判断平台用户、租户用户、租户类型或 OpenAPI 鉴权。
- 本包不执行 `manage.install` 动作；当前源码只把动作标准化返回。
- 本包不约束 `services` 的所有字段语义；不同 connector 只读取自己关心的服务节点。
- 本包不把业务配置写进数据库；它只解析配置文件路径，server 端 install/config 接口决定写入哪个 registry。
- 本包不定义 print、filesystem、workflow、AI Router、queue worker 等业务套件的内部目录规范。
