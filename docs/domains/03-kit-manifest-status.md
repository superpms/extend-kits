# 套件清单与状态

`extend-kits` 把套件信息分成三类文件：根清单、单 kit 清单、运行态 extra。

## 根 `kit.json`

根清单位于 `Path::getKitsRoot('kit.json')`。在当前 server 中是 `server/kits/kit.json`。

根清单的核心字段：

- `name`: 通常是 `kits`
- `root`: 是否是根清单
- `registry`: 套件发布注册中心地址
- `registry_env`: 注册中心环境，当前样例是 `project`
- `require`: 已启用 kit 的 `name => version` 映射

`Setup` 只根据根清单 `require` 加载子 kit 的启动文件。`KitsRegistryCenter::localList()` 也根据这个列表返回本地 kit 对象。

## 单 kit `kit.json`

单 kit 清单位于 `server/kits/<vendor>/<name>/kit.json`。包内 `KitFileSource` 会白名单化读取主字段：

- 基础信息：`name`、`icon`、`description`、`remarks`、`version`、`author`
- 依赖与服务：`require`、`services`
- 端配置：`manage`、`customer`
- 安装与发布属性：`system`、`private`
- 根清单属性：`root`、`registry`、`registry_env`

未知字段不会自动成为 `KitFileSource` 的数据属性，除非后续代码显式写入。

## `kit.extra.json`

`kit.extra.json` 是运行态可写状态文件，位于 `server/kits/<vendor>/<name>/kit.extra.json`。

当前包内明确语义化的字段：

- `installed`: `KitFileSource::isInstall()` 读取它，`install()` 把它设为 `true`。

当前 server 侧还消费：

- `scope`: 平台、租户列表、workflow trigger、filesystem、print 等调用点用它判断租户类型可用性。

`getExtra()` 返回的是 `KitFile` 对象或指定 key 的值；数组值会被包装成 `KitFile`，因此 server 代码常见 `$scope->toArray()`。

## 保存行为

`KitFileSource::save()` 会保存当前已挂载的 extra，并在管理端配置文件被加载且修改后保存配置文件。

注意：

- `save()` 只保存已经被 `mountExtra()` 或管理配置相关方法加载过的数据。
- 管理配置文件保存只支持 `json` 和 `php`；读取支持 `json`、`php`、`ini`。
- `setManageCfgFile()` 只更新配置文件中已经存在的 key。
