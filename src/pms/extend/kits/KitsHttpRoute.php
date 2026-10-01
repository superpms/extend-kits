<?php

namespace pms\extend\kits;

use InvalidArgumentException;
use pms\contract\HttpEntrypointInterface;
use pms\facade\Path;
use pms\helper\kits\KitsRegistryCenter;
use pms\hook\HttpEntrypointHook;
use pms\interpreter\http\sandbox\HttpRoute;

/**
 * 套件 HTTP 路由：解析终端、套件节点和接口路径。
 */
class KitsHttpRoute extends HttpRoute implements HttpEntrypointInterface
{
    protected string $kitName = '';
    protected array $pathParams = [];

    /**
     * 读取宿主配置中的套件挂载路径。
     * @return list<string> 挂载路径
     */
    public static function prefixes(): array
    {
        $prefix = HttpEntrypointHook::normalizePrefix(config('kits.http.prefix', 'kits'));
        if ($prefix === '/') {
            throw new InvalidArgumentException('套件 HTTP 挂载路径需要非空固定路径段');
        }
        return [$prefix];
    }

    /**
     * 解析完整套件请求路径，保留 HTTP 转发上下文。
     * @param string $pathinfo 完整路径
     * @param string|null $forward 已指定的接口类
     */
    public function __construct(string $pathinfo, ?string $forward = null)
    {
        parent::__construct($pathinfo, $forward);
        $this->inPrefix = false;
        $this->inStatic = false;
        $this->inApp = false;
        $this->isStm = true;
        $relative = HttpEntrypointHook::relativePath($pathinfo, static::prefixes()[0]);
        if ($relative === null || preg_match(
            '#^/([A-Za-z0-9:_-]+)/([A-Za-z][A-Za-z0-9_]*-[A-Za-z][A-Za-z0-9_]*)/([A-Za-z][A-Za-z0-9_]*(?:/[A-Za-z][A-Za-z0-9_]*)*)/?$#D',
            $relative,
            $parts,
        ) !== 1) {
            return;
        }
        $name = str_replace('-', '/', $parts[2]);
        $root = KitsRegistryCenter::useLocalKitFile('kit.json');
        if ($root === null || !isset($root->require[$name])
            || KitsRegistryCenter::useLocalKit($name) === null) {
            return;
        }
        $this->kitName = $name;
        $this->app = $parts[2];
        $this->terminal = $parts[1];
        $this->interface = str_replace('/', '\\', $parts[3]);
        $this->pathinfo = $relative;
        $this->inPrefix = true;
        $this->inApp = true;
        $this->calcInTerminal();
        $this->interfaceClass = $forward ?? 'kits\\' . str_replace('/', '\\', $name) . '\\http\\' . $this->interface;
        $this->pathParams = [
            'terminal' => $this->terminal,
            'kit' => $this->app,
            'path' => $parts[3],
        ];
    }

    /**
     * 返回套件路径参数，接口类由套件目录直接定位。
     * @return array<string, string> 路径参数
     */
    public function constructInterface(): array
    {
        return $this->isForward || !$this->inPrefix ? [] : $this->pathParams;
    }

    /**
     * 生成使用当前套件挂载前缀的外部路径。
     * @param string $pathinfo 终端、套件节点及内部接口路径
     * @return string 外部路径
     */
    public static function withPrefix(string $pathinfo): string
    {
        $prefix = static::prefixes()[0];
        $pathinfo = '/' . ltrim($pathinfo, '/');
        return HttpEntrypointHook::relativePath($pathinfo, $prefix) === null
            ? $prefix . $pathinfo
            : $pathinfo;
    }

    /**
     * 获取套件接口使用的 HTTP 配置目录。
     * @return list<string> HTTP 全局、宿主套件配置和套件配置目录
     */
    public function configPaths(): array
    {
        $interpreter = config('http.app.structure.package', 'http');
        return [
            Path::getConfig('interpreter', $interpreter),
            Path::getConfig('interpreter', $interpreter, 'kits', $this->kitName),
            Path::getKitsRoot($this->kitName, config('http.app.structure.config', 'config')),
        ];
    }
}
