<?php

namespace pms\program\kits;

use pms\facade\Path;
use pms\program\kits\contract\KitNodeCustomerInterface;
use pms\program\kits\contract\KitNodeManageInterface;

/**
 * @property string|null                   $name                                                         包名
 * @property bool                          $root                                                         是否为根包
 * @property string|null                   $registry                                                     子包注册发布中心地址
 * @property string                        $registry_env                                                 环境[project:普通项目环境<默认>](须与注册发布中心环境一致)
 * @property string                        $icon                                                         包图标地址
 * @property string|null                   $description                                                  包描述
 * @property string|null                   $remarks                                                      包备注
 * @property string|null                   $version                                                      包版本
 * @property string                        $author                                                       包作者
 * @property array                         $require                                                      依赖子包
 * @property array                         $services                                                     套件服务声明
 * @property array                         $capabilities                                                 套件能力声明
 * @property KitNodeManageInterface|null   $manage                                                       配置项-管理端
 * @property KitNodeCustomerInterface|null $customer                                                     配置项-客户端(此项非null时方可允许客户端进行安装/使用)
 * @property bool                          $system                                                       是否为系统套件(系统套件不支持卸载)
 * @property bool                          $private                                                      是否为私有套件(私有套件不支持更新)
 */
class KitFileSource extends KitFile
{

    /**
     * @var string 当前kits.json路径
     */
    protected string $kit_file_path;


    public function __construct(array $info, string $path = '')
    {
        $this->kit_file_path = $path;
        parent::__construct(false);

        $this->name = static::stringOrNull($info['name'] ?? null);
        $this->root = static::boolValue($info['root'] ?? false);
        $this->registry = static::stringOrNull($info['registry'] ?? null);
        $this->registry_env = static::stringValue($info['registry_env'] ?? 'project', 'project');
        $this->icon = static::stringOrNull($info['icon'] ?? null);
        $this->description = static::stringOrNull($info['description'] ?? null);
        $this->remarks = static::stringOrNull($info['remarks'] ?? null);
        $this->version = static::stringOrNull($info['version'] ?? null);
        $this->author = static::stringOrNull($info['author'] ?? null);
        $this->require = isset($info['require']) && is_array($info['require']) ? $info['require'] : [];
        $this->services = isset($info['services']) && is_array($info['services']) ? $info['services'] : [];
        $this->capabilities = isset($info['capabilities']) && is_array($info['capabilities']) ? $info['capabilities'] : [];

        $this->manage = null;
        if (array_key_exists('manage', $info)) {
            $manage = $info['manage'];
            if (is_array($manage)) {
                $this->manage = [];
                if (array_key_exists('cfg_sql', $manage)) {
                    $this->manage['cfg_sql'] = static::stringValue($manage['cfg_sql'] ?? '');
                }
                if (array_key_exists('cfg_db', $manage)) {
                    $this->manage['cfg_db'] = static::stringValue($manage['cfg_db'] ?? '');
                }
                if (array_key_exists('cfg_file', $manage)) {
                    $this->manage['cfg_file'] = static::stringValue($manage['cfg_file'] ?? '');
                }
                if (array_key_exists('cfg_pages', $manage)) {
                    $this->manage['cfg_pages'] = static::stringValue($manage['cfg_pages'] ?? '');
                }
                if (array_key_exists('autoinstall', $manage)) {
                    $this->manage['autoinstall'] = static::boolValue($manage['autoinstall'] ?? false);
                }
                if (array_key_exists('install', $manage)) {
                    $this->manage['install'] = is_array($manage['install']) ? $manage['install'] : [];
                }
            }
        }
        $this->customer = null;
        if (array_key_exists('customer', $info)) {
            $customer = $info['customer'];
            if (is_array($customer)) {
                $this->customer = [];
                if (array_key_exists('autoinstall', $customer)) {
                    $this->customer['autoinstall'] = static::boolValue($customer['autoinstall'] ?? false);
                }
                if (array_key_exists('cfg_db', $customer)) {
                    $this->customer['cfg_db'] = static::stringValue($customer['cfg_db'] ?? '');
                }
                if (array_key_exists('cfg_pages', $customer)) {
                    $this->customer['cfg_pages'] = static::stringValue($customer['cfg_pages'] ?? '');
                }
            }

        }

        $this->system = static::boolValue($info['system'] ?? false);
        $this->private = static::boolValue($info['private'] ?? false);
    }

    protected static function stringOrNull(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }

    protected static function stringValue(mixed $value, string $default = ''): string
    {
        return is_string($value) ? $value : $default;
    }

    protected static function boolValue(mixed $value): bool
    {
        return is_bool($value) ? $value : false;
    }

    public function getService(?string $key = null): array
    {
        $services = $this->toArray()['services'] ?? [];
        if (!is_array($services)) {
            return [];
        }
        if ($key === null) {
            return $services;
        }
        $service = $services[$key] ?? [];
        return is_array($service) ? $service : [];
    }

    /**
     * 获取套件能力声明。
     */
    public function getCapabilities(?string $value = null): array
    {
        $capabilities = $this->toArray()['capabilities'] ?? [];
        if (!is_array($capabilities)) {
            return [];
        }
        if ($value === null) {
            return $capabilities;
        }

        $matched = [];
        foreach ($capabilities as $capability) {
            if (is_array($capability) && ($capability['value'] ?? null) === $value) {
                $matched[] = $capability;
            }
        }
        return $matched;
    }


    /**
     * @var KitFile|null 当前已挂载的extra数据
     */
    protected KitFile|null $extra = null;

    protected function mountExtra(): static
    {
        if ($this->extra === null) {
            $data = [];
            $path = $this->name !== null ? Path::getKitsRoot($this->name, 'kit.extra.json') : null;
            if (is_string($path) && is_file($path)) {
                $data = load_json_config($path);
            }
            $p = new parent();
            $this->extra = $p->restore($data);
        }
        return $this;
    }

    public function getExtra(?string $key = null, mixed $default = null): mixed
    {
        $this->mountExtra();
        if ($key === null) {
            return $this->extra;
        }
        return $this->extra[$key] ?? (is_array($default) ? (new parent())->restore($default) : $default);
    }

    public function setExtra(string $key, mixed $value): static
    {
        $this->mountExtra();
        $this->extra[$key] = $value;
        return $this;
    }

    public function isInstall(): bool
    {
        return $this->getExtra('installed', false);
    }

    public function install(): static
    {
        $this->setExtra('installed', true);
        return $this;
    }

    public function save(): bool|int
    {
        $status = [];
        if ($this->extra !== null && $this->name !== null) {
            $path = Path::getKitsRoot($this->name, 'kit.extra.json');
            $status[] = save_json_config($path, $this->extra);
        }

        if ($this->manage_cfg_file !== null && !empty($this->manage_cfg_file_path)) {
            $extension = pathinfo($this->manage_cfg_file_path, PATHINFO_EXTENSION);
            switch ($extension) {
                case 'json':
                    $status[] = save_json_config($this->manage_cfg_file_path, $this->manage_cfg_file);
                    break;
                case 'php':
                    $status[] = save_php_config($this->manage_cfg_file_path, $this->manage_cfg_file);
                    break;
            }

        }
        return !in_array(false, $status);
    }


    // -----------管理端-数据库安装文件----------- //

    protected string|null $manage_cfg_sql_path = '';

    public function getManageCfgSqlPath(): ?string
    {
        if ($this->manage_cfg_sql_path === '') {
            $this->manage_cfg_sql_path = null;
            $path = $this->manage?->cfg_sql ?? null;
            if (!empty($path)) {
                $path = Path::getKitsRoot($this->name, $path);
                if (is_file($path)) {
                    $this->manage_cfg_sql_path = $path;
                }
            }
        }
        return $this->manage_cfg_sql_path;
    }


    // -----------管理端-数据库配置项----------- //
    protected array|null $manage_cfg_db = null;
    protected string|null $manage_cfg_db_path = '';

    public function getManageCfgDbPath(): ?string
    {
        if ($this->manage_cfg_db_path === '') {
            $this->manage_cfg_db_path = null;
            $path = $this->manage?->cfg_db ?? null;
            if (!empty($path)) {
                $path = Path::getKitsRoot($this->name, $path);
                if (is_file($path)) {
                    $this->manage_cfg_db_path = $path;
                }
            }
        }
        return $this->manage_cfg_db_path;
    }

    protected function mountManageCfgDb(): static
    {
        if ($this->manage_cfg_db === null) {
            $this->manage_cfg_db = [];
            if ($this->manage !== null) {
                $path = static::getManageCfgDbPath();
                if ($path !== null) {
                    $this->manage_cfg_db = load_json_config($path);
                }
            }
        }
        return $this;
    }

    public function getManageCfgDb()
    {
        return $this->mountManageCfgDb()->manage_cfg_db['data'] ?? [];
    }

    public function getManageCfgDbVersion()
    {
        return $this->mountManageCfgDb()->manage_cfg_db['version'] ?? $this->manage_cfg_db['registry'] ?? null;
    }


    // -----------客户端-数据库配置项----------- //
    protected array|null $customer_cfg_db = null;
    protected string|null $customer_cfg_db_path = '';

    public function getCustomerCfgDbPath(): ?string
    {
        if ($this->customer_cfg_db_path === '') {
            $this->customer_cfg_db_path = null;
            $path = $this->customer?->cfg_db ?? null;
            if (!empty($path)) {
                $path = Path::getKitsRoot($this->name, $path);
                if (is_file($path)) {
                    $this->customer_cfg_db_path = $path;
                }
            }
        }
        return $this->customer_cfg_db_path;
    }

    protected function mountCustomerCfgDb(): static
    {
        if ($this->customer_cfg_db === null) {
            $this->customer_cfg_db = [];
            if ($this->customer !== null) {
                $path = static::getCustomerCfgDbPath();
                if ($path !== null) {
                    $this->customer_cfg_db = load_json_config($path);
                }
            }
        }
        return $this;
    }

    public function getCustomerCfgDb()
    {
        return $this->mountCustomerCfgDb()->customer_cfg_db['data'] ?? [];
    }

    public function getCustomerCfgDbVersion()
    {
        return $this->mountCustomerCfgDb()->customer_cfg_db['version'] ?? $this->customer_cfg_db['registry'] ?? null;
    }


    // -----------管理端-声明式安装动作----------- //

    public function getManageInstall(): array
    {
        $install = $this->manage?->install ?? [];
        if (!is_iterable($install)) {
            return [];
        }

        $actions = [];
        foreach ($install as $item) {
            if ($item instanceof KitFile) {
                $item = $item->toArray();
            }
            if (!is_array($item)) {
                continue;
            }

            $type = $item['type'] ?? null;
            $from = $item['from'] ?? null;
            $to = $item['to'] ?? null;
            if (!is_string($type) || !is_string($from) || !is_string($to)) {
                continue;
            }
            if (!in_array($type, ['copy', 'move'], true) || $from === '' || $to === '') {
                continue;
            }

            $actions[] = [
                'type' => $type,
                'from' => $from,
                'to' => $to,
            ];
        }

        return $actions;
    }


    // -----------管理端-管理页面----------- //
    protected array|null $manage_cfg_pages = null;
    protected string|null $manage_cfg_pages_path = '';

    public function getManageCfgPagesPath(): ?string
    {
        if ($this->manage_cfg_pages_path === '') {
            $this->manage_cfg_pages_path = null;
            $path = $this->manage?->cfg_pages ?? null;
            if (!empty($path)) {
                $path = Path::getKitsRoot($this->name, $path);
                if (is_file($path)) {
                    $this->manage_cfg_pages_path = $path;
                }
            }
        }
        return $this->manage_cfg_pages_path;
    }

    protected function mountManageCfgPages(): static
    {
        if ($this->manage_cfg_pages === null) {
            $this->manage_cfg_pages = [];
            if ($this->manage !== null) {
                $path = static::getManageCfgPagesPath();
                if ($path !== null) {
                    $this->manage_cfg_pages = load_json_config($path);
                }
            }
        }
        return $this;
    }

    public function getManageCfgPages(): ?array
    {
        return $this->mountManageCfgPages()->manage_cfg_pages;
    }






    // -----------客户端-管理页面----------- //

    protected array|null $customer_cfg_pages = null;
    protected string|null $customer_cfg_pages_path = '';

    public function getCustomerCfgPagesPath(): ?string
    {
        if ($this->customer_cfg_pages_path === '') {
            $this->customer_cfg_pages_path = null;
            $path = $this->customer?->cfg_pages ?? null;
            if (!empty($path)) {
                $path = Path::getKitsRoot($this->name, $path);
                if (is_file($path)) {
                    $this->customer_cfg_pages_path = $path;
                }
            }
        }
        return $this->customer_cfg_pages_path;
    }

    protected function mountCustomerCfgPages(): static
    {
        if ($this->customer_cfg_pages === null) {
            $this->customer_cfg_pages = [];
            if ($this->customer !== null) {
                $path = static::getCustomerCfgPagesPath();
                if ($path !== null) {
                    $this->customer_cfg_pages = load_json_config($path);
                }
            }
        }
        return $this;
    }

    public function getCustomerCfgPages(): ?array
    {
        return $this->mountCustomerCfgPages()->customer_cfg_pages;
    }


    // -----------管理端-配置文件----------- //
    protected array|null $manage_cfg_file = null;
    protected string|null $manage_cfg_file_path = '';

    public function getManageCfgFilePath(): ?string
    {
        if ($this->manage_cfg_file_path === '') {
            $this->manage_cfg_file_path = null;
            $path = $this->manage?->cfg_file ?? null;
            if (!empty($path)) {
                $path = Path::getKitsRoot($this->name, $path);
                if (is_file($path)) {
                    $this->manage_cfg_file_path = $path;
                }
            }
        }
        return $this->manage_cfg_file_path;
    }

    protected function mountManageCfgFile(): static
    {
        if ($this->manage_cfg_file === null) {
            $this->manage_cfg_file = [];
            if ($this->manage !== null) {
                $path = static::getManageCfgFilePath();
                if ($path !== null) {
                    $extension = pathinfo($path, PATHINFO_EXTENSION);
                    switch ($extension) {
                        case 'json':
                            $this->manage_cfg_file = load_json_config($path);
                            break;
                        case 'php':
                            $this->manage_cfg_file = load_php_config($path);
                            break;
                        case 'ini':
                            $this->manage_cfg_file = load_ini_config($path);
                            break;
                    }
                }
            }
        }
        return $this;
    }

    public function getManageCfgFile(): ?array
    {
        return $this->mountManageCfgFile()->manage_cfg_file;
    }

    public function setManageCfgFile(array $data): static
    {
        $this->mountManageCfgFile();
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->manage_cfg_file)) {
                $this->manage_cfg_file[$key] = $value;
            }
        }
        return $this;
    }



}
