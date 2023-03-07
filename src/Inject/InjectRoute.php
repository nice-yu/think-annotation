<?php
declare(strict_types=1);

namespace NiceYu\ThinkAnnotation\Inject;

use NiceYu\ThinkAnnotation\Annotation\Route;
use NiceYu\ThinkAnnotation\Annotation\ScanClass;
use NiceYu\ThinkAnnotation\Service;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

trait InjectRoute
{
    /**
     * 暂停或开启遍历
     * @var bool
     */
    private bool $scan = true;

    /**
     * route 配置信息
     * @var array
     */
    private array $config;

    /**
     * 当前请求路由
     * @var string
     */
    protected string $currentRoute;

    /**
     * 设置配置信息
     * @return Service
     */
    private function setRoute(): Service
    {
        /** 获取控制器位置 */
        $path   = $this->app->config->get('route_annotation.controller', array());
        $path   = $path ?: array($this->app->getAppPath());

        /** 控制器文件夹名称 */
        $name   = $this->app->config->get('route.controller_layer','controller');

        $this->config = compact('path','name');

        $this->setCurrentRequestPath();

        return $this;
    }

    /**
     * @return Service
     * @throws ReflectionException
     */
    public function useRoute(): Service
    {
        /** 获取到配置信息 */
        ['path'=> $path, 'name'=> $name] = $this->config;

        /** 组装路径 */
        foreach ($path as $dir){
            /** 跳过不存在的文件夹 */
            if (!is_dir($dir.$name))  continue;

            /** 已经匹配到的情况下不需要继续运行 */
            if (!$this->scan) break;

            /** 递归文件夹目录 */
            $this->ScanController($dir.$name);
        }
        return $this;
    }

    /**
     * @param string $dir
     * @return void
     * @throws ReflectionException
     */
    private function ScanController(string $dir)
    {
        foreach (ScanClass::createMap($dir) as $class => $path){

            /** 已经匹配到的情况下不需要继续运行 */
            if (!$this->scan) break;

            /** 反射到此类 */
            $refClass   = new ReflectionClass($class);

            /**
             * 类注解
             * @var Route $group
             */
            $routeGroup = $this->reader->getClassAnnotation($refClass, Route::class) ?: null;

            /**
             * 方法注解
             * @var Route $group
             */
            foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $refMethod) {

                /** 已经匹配到的情况下不需要继续运行 */
                if (!$this->scan) break;

                /** 获取到此方法路径 */
                if ($method = $this->reader->getMethodAnnotation($refMethod, Route::class)){

                    /** 将参数按照规则整理 */
                    [
                        'routePath' => $routePath,
                        'method'    => $methods,
                        'version'   => $versions,
                        'security'  => $security,
                        'defaults'  => $defaults,
                    ]  = $this->sureOptions($routeGroup,$method);

                    /** 对比当前路由 */
                    if ($this->currentRoute !== $routePath) continue;

                    /** 查看版本信息 */
                    if (!($this->hasVersion($versions))) continue;

                    /** 查看守卫信息 */
                    if (!($this->makeSecurity($security))) continue;

                    /** 将匹配到的路由 */
                    $action = "$class@{$refMethod->getName()}";
                    $group  = $this->route->getGroup();
                    $rule   = $group->addRule($routePath, $action, $methods);
                    $rule->option([
                        'routePath' => $routePath,
                        'method'    => $methods,
                        'version'   => $versions,
                        'security'  => $security,
                        'defaults'  => $defaults,
                    ]);

                    $this->scan = false;
                    break;
                }
            }
        }
    }

    /**
     * 获取当前请求地址
     * @return void
     */
    private function setCurrentRequestPath():void
    {
        /** 获取当前请求地址 - 默认只有控制器和方法 */
        $path = explode('/',$this->app->request->pathinfo());
        if (isset($path[1])){
            $this->currentRoute = "/$path[0]/$path[1]";
        } else {
            $this->currentRoute = "/$path[0]";
        }
    }

    /**
     * 确定参数选项
     * @param $routeGroup
     * @param Route $routeMethod
     * @return array
     */
    private function sureOptions($routeGroup, Route $routeMethod):array
    {
        $routePath  = '';
        $method     = $routeMethod->getMethod();
        $version    = $routeMethod->getVersion();
        $security   = $routeMethod->getSecurity();
        $defaults   = $routeMethod->getDefaults();

        /** 类不存在注解 */
        if (is_null($routeGroup)){
            $routePath  = $this->sureRequestPath('',$routeMethod->getName());
        } elseif ($routeGroup instanceof Route) {
            $routePath  = $this->sureRequestPath($routeGroup->getName(),$routeMethod->getName());
            $method     = $method ?: $routeGroup->getMethod();
            $version    = $version ?: $routeGroup->getVersion();
            $security   = $security ?: $routeGroup->getSecurity();
            $defaults   = $defaults ?: $routeGroup->getDefaults();
        }
        return compact('routePath','method','version','security','defaults');
    }

    /**
     * 确定请求地址
     * @param string $classPath
     * @param string $routePath
     * @return string
     */
    private function sureRequestPath(string $classPath,string $routePath): string
    {
        /** 方法上存在 `/`  */
        if (0 === strpos($routePath,'/')){

            return $routePath;
        }

        /** 方法上不存在 `/`  */
        $methodPath = "$classPath/$routePath";

        /** 路径补充 `/` */
        if (0 !== strpos($methodPath,'/')){
            return "/$methodPath";
        }
        return $methodPath;
    }
}