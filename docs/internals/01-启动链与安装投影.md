# 启动链与安装投影

本页解释 composer 安装期和框架启动期两条链路。

## 安装期投影

`composer/pms/extend-kits/composer.json` 的 `extra.pms`：

```json
{
  "pms": {
    "dir": ["/kits"],
    "copy": {
      "/kits/kit.json": "/resource/kit.json"
    }
  }
}
```

当前项目的 vendor install hook 会读取包的 `extra.pms`，创建目录并复制资源模板。这个 hook 的实现不在本包内。

本包提供的默认模板是：

```text
composer/pms/extend-kits/resource/kit.json
```

## 启动期加载

启动期来自 Composer `autoload.files`：

```text
composer/pms/extend-kits/bin/autoload.php
```

autoload 文件只在 `LifecycleHook` 存在时挂载 `Setup`。真正路径挂载和 kit 文件加载发生在 boot 生命周期。

## 当前 server 的实际路径

当前 server `boot.json`：

```json
{
  "extend": {
    "kits": "/kits"
  }
}
```

因此：

- `Path::getKitsRoot('kit.json')` 对应 `server/kits/kit.json`。
- `Path::getKitsRoot('print/feieyun', 'autoload.php')` 对应 `server/kits/print/feieyun/autoload.php`。

## 自动加载不等于安装

被根清单 require 后，kit 的 `define.php` 和 `autoload.php` 会参与启动加载。但平台安装态仍由 `kit.extra.json.installed` 表示，租户安装态仍由 server 端 `SystemKits` 表示。

所以：

- 启动加载：决定代码是否进入运行时。
- 平台安装：决定平台是否把 kit 视为 installed。
- 租户安装或 autoinstall：决定租户侧是否 active 或 usable。
