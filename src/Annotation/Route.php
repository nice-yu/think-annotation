<?php
declare(strict_types=1);
namespace NiceYu\ThinkAnnotation\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * Annotation class for @Route().
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"METHOD","CLASS"})
 */
final class Route
{
    /**
     * 请求地址
     * @var string
     */
    private string $name;

    /**
     * 请求类型
     * @Enum({"GET","POST","PUT","DELETE","PATCH","HEAD"})
     * @var string
     */
    private string $method;

    /**
     * 代码版本
     * @var array
     */
    private array $version;

    /**
     * 安全模块
     * @var string
     */
    private string $security;

    /**
     * 默认信息
     * @var array
     */
    private array $defaults;

    /**
     * @param string $name
     * @param string $method
     * @param array $version
     * @param string $security
     * @param array $defaults
     */
    public function __construct(
        string $name,
        string $method = 'GET|POST',
        array $version = [],
        string $security = '',
        array $defaults = []
    )
    {
        $this->name = $name;
        $this->method = $method;
        $this->version = $version;
        $this->security = $security;
        $this->defaults = $defaults;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getVersion(): array
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getSecurity(): string
    {
        return $this->security;
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }
}