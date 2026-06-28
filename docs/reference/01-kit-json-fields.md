# kit.json 字段参考

本页按当前 `KitFileSource` 源码列出字段。字段是否被业务使用，还取决于 server 调用点。

## 根清单字段

| 字段 | 类型 | 说明 |
| --- | --- | --- |
| `name` | string|null | 根清单通常是 `kits`。 |
| `root` | bool | 是否根清单。 |
| `registry` | string|null | 套件注册中心地址。 |
| `registry_env` | string | 注册环境，默认 `project`。 |
| `require` | object | kit 名称到版本的映射。 |

## 单 kit 基础字段

| 字段 | 类型 | 说明 |
| --- | --- | --- |
| `name` | string|null | kit 名称，例如 `print/feieyun`。 |
| `icon` | string|null | 图标路径。 |
| `description` | string|null | 简短描述。 |
| `remarks` | string|null | 备注说明。 |
| `version` | string|null | kit 版本。 |
| `author` | string|null | 作者。 |
| `require` | object | kit 依赖声明。 |
| `services` | object | 服务 provider 元数据。 |
| `system` | bool | 系统套件标记；当前租户卸载接口禁止卸载系统套件。 |
| `private` | bool | 私有套件标记。 |

## `manage` 字段

| 字段 | 类型 | 说明 |
| --- | --- | --- |
| `autoinstall` | bool | 管理端自动安装标记。当前包只读取，不执行安装。 |
| `install` | array | 声明式安装动作列表；当前 `getManageInstall()` 只标准化返回。 |
| `cfg_sql` | string | 管理端 SQL 文件路径。 |
| `cfg_db` | string | 管理端 DB 配置声明文件路径。 |
| `cfg_file` | string | 管理端文件配置路径。 |
| `cfg_pages` | string | 管理端页面配置路径。 |

## `customer` 字段

| 字段 | 类型 | 说明 |
| --- | --- | --- |
| `autoinstall` | bool | 租户端自动启用标记。 |
| `cfg_db` | string | 租户端 DB 配置声明文件路径。 |
| `cfg_pages` | string | 租户端页面配置路径。 |

## `kit.extra.json` 字段

| 字段 | 类型 | 说明 |
| --- | --- | --- |
| `installed` | bool | 平台安装态，`isInstall()` 读取，`install()` 写入 true。 |
| `scope` | array | 当前 server 用来限制租户类型可用性。 |

## 路径约定

`cfg_sql`、`cfg_db`、`cfg_file`、`cfg_pages` 都按 kit 根目录解析。当前 server 样例普遍使用以 `/` 开头的 kit 内路径，例如 `/resource/pages/manage.json`。

## 类型归一

`KitFileSource` 对输入做保守归一：

- string 字段不是 string 时变成 `null` 或默认空字符串。
- bool 字段只有真实 bool 才保留，否则为 `false`。
- array 字段不是 array 时变成空数组。
