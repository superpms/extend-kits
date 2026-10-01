# 管理端与租户端配置

`extend-kits` 解析单套件的配置入口，server 通过 GetConfig、SaveConfig 读取和保存。

manage 节点支持 cfg_sql、cfg_db、cfg_file、cfg_pages。cfg_sql 指向数据库结构资源；cfg_db 定义可读写的配置键；cfg_file 指向管理配置文件；cfg_pages 指向管理页面声明。

customer 节点支持 cfg_db、cfg_pages。server 按 customer 声明及 extra.scope 判断访问范围，配置使用当前主体对应的注册器。

KitFileSource 提供管理端及租户端对应的路径、配置定义和页面读取方法，管理端文件配置通过 setManageCfgFile() 和 save() 保存。

页面类型、请求与 SDK 见 APM admin《套件展示、配置页面与宿主 SDK》；server 访问规则见 APM server《套件声明、配置与访问边界》。
