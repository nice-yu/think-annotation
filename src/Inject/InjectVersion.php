<?php
declare(strict_types=1);

namespace NiceYu\ThinkAnnotation\Inject;

use NiceYu\ThinkAnnotation\Service;

trait InjectVersion
{
    /**
     * version 配置信息
     * @var array
     */
    private array $version = [];

    /**
     * 设置注释版本
     * @return Service
     */
    private function setVersion(): Service
    {
        $enable = $this->app->config->get('route_version.enable',true);
        if ($enable){
            $param = $this->app->config->get('route_version.param',false);
            $name  = $this->app->config->get('route_version.name','version');
            if ($param){
                $version = request()->header($name, '1.0');
            }else{
                $version = request()->param($name, '1.0');
            }
            $this->version = compact('enable','param','name','version');
        }
        return $this;
    }

    /**
     * 对比当前版本是否可以使用
     * @param array $versions
     * @return bool
     */
    public function hasVersion(array $versions):bool
    {
        if (!empty($this->version)){
            ['enable'=>$enable,'version'=>$version] = $this->version;
            if ($enable){
                if (in_array($version,$versions)){
                    return true;
                }
                return false;
            }
        }
        return true;
    }
}