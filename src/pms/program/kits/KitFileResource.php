<?php

namespace pms\program\kits;

/**
 * @property string|null $name                        包名
 * @property string|null $registry                    注册发布中心地址
 * @property string|null $icon                        图标名称
 * @property string|null $description                 包描述
 * @property string|null $comment                     包备注
 * @property string|null $type                        包类型
 * @property string|null $version                     包版本
 * @property string|null $author                      包作者
 * @property string|null $view                        配置项控制页
 * @property array       $require                     依赖包
 * @property string|null $config_db                   数据库配置项
 * @property array       $extra                       扩展配置
 */
class KitFileResource extends KitsResource
{

    /**
     * @var string 当前kits.json路径
     */
    protected string $kit_file_path;

    public function __construct(array $info, string $path = '')
    {
        parent::__construct(false);
        $this->kit_file_path = $path;
        $this->name = $info['name'] ?? null;
        $this->icon = $info['icon'] ?? null;
        $this->description = $info['description'] ?? null;
        $this->comment = $info['comment'] ?? null;
        $this->version = $info['version'] ?? null;
        $this->view = $info['view'] ?? null;
        $this->author = $info['author'] ?? null;
        $this->require = $info['require'] ?? [];
        $this->config_db = $info['config_db'] ?? null;
        $this->extra = $info['extra'] ?? [];
    }

    public function setExtra(string $key, mixed $value): static
    {
        if (empty($root->extra)) {
            $this->extra = [];
        }
        $this->extra[$key] = $value;
        return $this;
    }

    public function save(): bool|int
    {
        if (!empty($this->kit_file_path)) {
            return file_put_contents($this->kit_file_path, json_encode($this, 320));
        }
        return false;
    }

}