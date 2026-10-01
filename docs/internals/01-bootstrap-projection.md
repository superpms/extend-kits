# Composer 接入与启动链

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

## 套件加载与使用

根清单 require 登记的 kit 参与启动加载。宿主读取配置和执行业务时，按本地套件声明、授权范围、设备及配置完整性判断可用性。
