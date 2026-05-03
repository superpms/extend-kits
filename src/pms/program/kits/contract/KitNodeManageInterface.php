<?php

namespace pms\program\kits\contract;

/**
 * @property string $cfg_sql           管理端数据库-安装文件
 * @property string $cfg_db            管理端数据库-配置项
 * @property string $cfg_file          管理端文件-配置项
 * @property string $cfg_pages         管理端配置页面
 * @property bool   $autoinstall       管理端是否自动安装
 * @property array  $install           管理端声明式安装动作
 */
interface KitNodeManageInterface
{

}
