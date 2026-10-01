# 读取配置声明与页面

manage 使用 cfg_db、cfg_file、cfg_pages 声明配置及页面入口；cfg_sql 指向套件数据库结构资源。customer 使用 cfg_db、cfg_pages 声明当前主体配置及页面。

KitFileSource 的 getManageCfgDb()、getCustomerCfgDb() 返回配置定义，getManageCfgPages()、getCustomerCfgPages() 返回页面描述。getManageCfgFile() 读取文件配置，setManageCfgFile() 更新已存在键并交给 save() 持久化。

server GetConfig 返回 db、file；SaveConfig 按 cfg_db 定义过滤键并写入配置定义和值。manage 使用平台注册器，customer 使用实际主体对应的注册器；文件配置由 manage 维护。

完整访问规则见 APM server《套件声明、配置与访问边界》；页面渲染与请求见 APM admin《套件展示、配置页面与宿主 SDK》。
