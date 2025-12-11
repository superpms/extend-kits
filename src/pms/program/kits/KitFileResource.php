<?php

namespace pms\program\kits;

use pms\facade\Path;

/**
 * @property string|null $name                                 包名
 * @property string|null $registry                             注册发布中心地址
 * @property string|null $icon                                 图标名称
 * @property string|null $description                          包描述
 * @property string|null $comment                              包备注
 * @property string      $type                                 包类型[server:服务端套件(不允许激活使用),app:应用套件(需要激活使用)]
 * @property bool        $system                               是否为系统套件(系统套件不支持卸载)
 * @property bool        $private                              是否为私有套件(私有套件不支持更新)
 * @property string|null $version                              包版本
 * @property string|null $author                               包作者
 * @property string|null $view                                 配置项编辑页面
 * @property array       $require                              依赖包
 * @property string|null $config_db                            数据库配置项
 * @property string|null $config_file                          文件配置项
 * @property array       $extra                                扩展配置
 */
class KitFileResource extends KitsResource
{

    /**
     * @var string 当前kits.json路径
     */
    protected string $kit_file_path;

    /**
     * @var array 当前已挂载的config_db数据
     */
    protected array $mount_config_db = [];

    /**
     * @var array 当前已挂载的config_file数据
     */
    protected array $mount_config_file = [];

    public function __construct(array $info, string $path = '')
    {
        parent::__construct(false);
        $this->kit_file_path = $path;
        $this->type = $info['type'] ?? 'app';
        $this->name = $info['name'] ?? null;
        $this->system = array_key_exists('system', $info) ? $info['system'] : false;
        $this->private = array_key_exists('private', $info) ? $info['private'] : false;
        $this->icon = $info['icon'] ?? null;
        $this->description = $info['description'] ?? null;
        $this->comment = $info['comment'] ?? null;
        $this->version = $info['version'] ?? null;
        $this->view = $info['view'] ?? null;
        $this->author = $info['author'] ?? null;
        $this->require = $info['require'] ?? [];
        $this->config_db = $info['config_db'] ?? null;
        $this->config_file = $info['config_file'] ?? null;
        $this->extra = $info['extra'] ?? [];
    }

    public function setExtra(string $key, mixed $value): static
    {
        if (empty($this->extra)) {
            $this->extra = [];
        }
        $this->extra[$key] = $value;
        return $this;
    }

    public function getExtra(?string $key = null, mixed $default = null): mixed
    {
        if (empty($this->extra)) {
            $this->extra = [];
        }
        if ($key === null) {
            return $this->extra;
        }
        return $this->extra[$key] ?? (is_array($default) ? (new parent())->restore($default) : $default);
    }

    public function getConfigDbPath(): ?string
    {
        if (empty($this->config_db)) {
            return null;
        }
        $path = Path::getKitsRoot($this->name, $this->config_db);
        if (is_file($path)) {
            return $path;
        }
        return null;
    }

    protected function mountConfigDb(): static
    {
        if (empty($this->mount_config_db)) {
            $path = $this->getConfigDbPath();
            if ($path === null) {
                $this->mount_config_db = [];
            } else {
                $this->mount_config_db = json_decode(file_get_contents($path), true);
            }
        }
        return $this;
    }

    public function getConfigDb()
    {
        return $this->mountConfigDb()->mount_config_db['data'] ?? [];
    }

    public function getConfigDbVersion()
    {
        return $this->mountConfigDb()->mount_config_db['version'] ?? null;
    }


    public function getConfigFilePath(): ?string
    {
        if (empty($this->config_file)) {
            return null;
        }
        $path = Path::getKitsRoot($this->name, $this->config_file);
        if (is_file($path)) {
            return $path;
        }
        return null;
    }

    protected function mountConfigFile(): static
    {
        if (empty($this->mount_config_file)) {
            $path = $this->getConfigFilePath();
            if ($path === null) {
                $this->mount_config_file = [];
            } else {
                $extension = pathinfo($path, PATHINFO_EXTENSION);

                switch ($extension){
                    case 'json':
                        $this->mount_config_file = load_json_config($path);
                        break;
                    case 'php':
                        $this->mount_config_file = load_php_config($path);
                        break;
                    case 'ini':
                        $this->mount_config_file = load_ini_config($path);
                        break;
                }
            }
        }
        return $this;
    }

    public function getConfigFile(): array
    {
        return $this->mountConfigFile()->mount_config_file;
    }

    public function saveConfigFile(array $data): bool|int
    {
        $this->mountConfigFile();
        foreach ($data as $key => $value) {
            if(array_key_exists($key, $this->mount_config_file)) {
                $this->mount_config_file[$key] = $value;
            }
        }
        $path = $this->getConfigFilePath();
        if(empty($path)){
            return false;
        }
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        switch ($extension){
            case 'json':
                return save_json_config($path,$this->mount_config_file);
            case 'php':
                return save_php_config($path,$this->mount_config_file);
            case 'ini':
                break;
        }
        return false;
    }


    public function save(): bool|int
    {
        if (!empty($this->kit_file_path)) {
            return file_put_contents($this->kit_file_path, json_encode($this, 320));
        }
        return false;
    }

}