# 读取配置页面与安装声明

本文说明 `extend-kits` 支持读取哪些配置页面与安装声明，以及当前 server 哪些接口会消费这些声明。它不是配置页业务开发规范；页面内字段职责、业务校验、写入边界和套件层级规范，读 `server/docs/specification/`。

## 可读取的管理端安装声明

在 `manage` 节点声明：

```json
{
  "manage": {
    "autoinstall": true,
    "cfg_sql": "/resource/sql/manage.sql",
    "cfg_db": "/resource/db/database.json",
    "cfg_file": "/config/public.php",
    "cfg_pages": "/resource/pages/manage.json"
  }
}
```

当前 server 平台 install 接口行为：

1. 读取 `cfg_sql` 并逐条执行 SQL。
2. 读取 `cfg_db` 并恢复到平台配置 registry，同时保留已有 key 的值。
3. 调用 `$kit->install()` 和 `$kit->save()` 写入 `kit.extra.json.installed=true`。

注意：当前 install 接口不执行 `manage.install` 动作，也不调用 kit 内 `Setup::install()`。

## server 管理端配置读写消费方式

平台端配置读取：

- `getManageCfgDb()` 决定允许读取的 DB 配置 key。
- `getManageCfgFile()` 读取文件配置。

平台端配置保存：

- `origin=file` 时只允许写入 `getManageCfgFilePath()` 指向的文件中已存在的 key。
- `origin=db` 时只允许保存 `getManageCfgDb()` 声明过的 key。

## 可读取的租户端配置声明

在 `customer` 节点声明：

```json
{
  "customer": {
    "autoinstall": true,
    "cfg_db": "/resource/db/customer.json",
    "cfg_pages": "/resource/pages/customer.json"
  }
}
```

当前 server 租户 install 接口行为：

1. 检查 `kit.extra.json.scope` 是否允许当前租户类型。
2. 读取 `customer.cfg_db`。
3. 根据当前租户类型写入租户 registry 或平台 registry。
4. 创建 `SystemKits` 记录。

租户配置保存只支持 `origin=db`。

## 页面 action 分发入口

页面配置需要在 `pages` 下声明页面，并让该页面的 `origin` 为 `action`。平台端和租户端 action 接口都会先校验页面是否存在，再分发到 `KitsAdapter::runAction()`。

`extend-kits` 只负责让页面声明可被读取。action 内部如何校验、写入、调用第三方和返回页面数据，由实际业务套件规范约束。

## 图标路径

server 列表接口会把相对图标转成：

```text
/static/kits/<kit-name>/<icon>
```

如果 `icon` 已经是完整路径，则保持原值。
