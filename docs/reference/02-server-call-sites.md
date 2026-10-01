# server 调用点索引

套件管理入口位于 app/system/tenant/http/kits：GetManifest 枚举根清单登记的本地套件，GetConfig、SaveConfig 读取和保存对应模式的配置，GetAssets 交付白名单 UI 资源。平台授权范围由 app/system/platform/http/kits/SaveExtra.php 写入 extra.scope。

core/system/kits/ClientKitConfig.php 聚合 services.client_config 声明的公开配置，并按租户范围和配置完整性输出状态。

core/system/abilities/AbilityAction.php 返回标准能力和设备匹配的套件渠道；GetListByAbility 输出配置就绪状态。各 Adapter 和 Provider 使用本地声明、业务开关、授权范围和配置校验完成调度。

管理、配置与访问边界见 APM server《套件声明、配置与访问边界》，能力消费见《标准能力、Provider 与运行时调度》。
