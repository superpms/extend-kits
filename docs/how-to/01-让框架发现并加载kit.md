# 让框架发现并加载 kit

本文只说明 `extend-kits` 框架发现、加载 kit 所需的最小声明。它不是 server 业务套件开发规范；实际业务套件的目录职责、内部实现、server 调用契约和返回规范，读 `server/docs/specification/`。

## 目录

在宿主项目 kits 根目录下创建：

```text
server/kits/<vendor>/<name>/
```

当前 server 的 kits 根目录来自 `server/boot.json` 的 `extend.kits=/kits`。

## 框架读取的单 kit 清单

创建：

```text
server/kits/<vendor>/<name>/kit.json
```

最小结构：

```json
{
  "name": "vendor/name",
  "icon": "logo.png",
  "description": "套件名称",
  "remarks": "套件说明",
  "version": "1.0.0",
  "author": "superpms",
  "require": {},
  "services": {},
  "system": false,
  "private": false
}
```

## 写入框架根清单

把 kit 加入根清单：

```json
{
  "require": {
    "vendor/name": "v1.0.0"
  }
}
```

根清单路径是：

```text
server/kits/kit.json
```

没有进入根清单的 kit 不会被 `Setup` 加载，也不会出现在 `KitsRegistryCenter::localList()` 中。

## 框架启动文件

如果 kit 需要定义常量或函数，创建：

```text
server/kits/<vendor>/<name>/define.php
```

如果 kit 需要注册服务或加载辅助函数，创建：

```text
server/kits/<vendor>/<name>/autoload.php
```

启动期加载顺序是 `define.php` 再 `autoload.php`。

## 框架可读的运行态文件

如需记录平台安装态、授权范围或其他运行态状态，创建或由接口写入：

```text
server/kits/<vendor>/<name>/kit.extra.json
```

常见结构：

```json
{
  "installed": false,
  "scope": []
}
```

`installed` 由平台 install 流程使用；`scope` 当前由租户列表、服务 connector 和 workflow trigger 等 server 调用点读取。

这些字段能被框架读取，并不代表业务能力已经完成。具体套件还必须满足对应 server 业务能力规范。
