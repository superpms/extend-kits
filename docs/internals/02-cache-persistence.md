# 读取缓存与持久化

`KitFileSource` 对 extra 和配置文件采用懒加载，并在对象实例内缓存。

## Extra 懒加载

`getExtra()` 和 `setExtra()` 都会先调用 `mountExtra()`。`mountExtra()` 只在第一次访问时读取：

```text
<kitsRoot>/<kit-name>/kit.extra.json
```

如果文件不存在，extra 初始为空数组。

## 配置懒加载

管理端和租户端配置分别有独立缓存：

- `manage_cfg_db`
- `customer_cfg_db`
- `manage_cfg_pages`
- `customer_cfg_pages`
- `manage_cfg_file`

路径方法也有独立缓存。第一次解析时如果文件不存在，路径缓存为 `null`。

## 保存

`save()` 当前会处理两类持久化：

1. extra 已经加载且 kit 有 name 时，保存到 `<kit>/kit.extra.json`。
2. `manage_cfg_file` 已经加载且路径存在时，按扩展名保存管理端文件配置。

`save()` 不会保存：

- 根 `kit.json` 的 `require`。
- 单 kit `kit.json`。
- `cfg_db` 中声明的数据库配置值。
- `customer.cfg_db`。
- `manage.install` 动作。

这些写入由 server 端或其他工具负责。

## 返回值

`save()` 收集内部保存状态，只要没有 `false` 就返回 true。没有任何待保存项时也会返回 true。

## 风险点

- 同一个请求中如果先读取配置，再由外部修改文件，当前对象仍使用已缓存数据。
- `setManageCfgFile()` 不新增 key，只改已存在 key。
- `getManageCfgFile()` 可以读 `ini`，但 `save()` 不保存 `ini`。
