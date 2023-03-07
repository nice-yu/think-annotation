<?php
declare(strict_types=1);
namespace NiceYu\ThinkAnnotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use NiceYu\ThinkAnnotation\Inject\InjectRoute;
use NiceYu\ThinkAnnotation\Inject\InjectSecurity;
use NiceYu\ThinkAnnotation\Inject\InjectVersion;
use think\event\RouteLoaded;
use think\Route;

class Service extends \think\Service
{
    use InjectRoute, InjectVersion, InjectSecurity;

    /** @var Route */
    protected Route $route;

    /** @var Reader */
    protected Reader $reader;

    /**
     * 将服务类注册到 thinkphp
     * @return void
     */
    public function register()
    {
        /** 查看是否需要路由注解 */
        if ($this->app->config->get('route_annotation.enable', true)){

            /** 使用注解路由 */
            $this->app->event->listen(RouteLoaded::class, function (){

                /** 将阅读类放入类内 */
                $this->reader = new AnnotationReader();

                /** 获取当前路由信息 */
                $this->route = $this->app->route;

                /** 设置配置信息 */
                $this->setVersion()->setSecurity()->setRoute()->useRoute();
            });
        }
    }
}