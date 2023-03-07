<?php
declare(strict_types=1);

namespace NiceYu\ThinkAnnotation\Inject;

use NiceYu\ThinkAnnotation\Security\UserProviderInterface;
use NiceYu\ThinkAnnotation\Service;
use think\exception\HttpException;

trait InjectSecurity
{
    /**
     * security 配置信息
     * @var array
     */
    private array $security = [];

    /**
     * 设置注释守卫
     * @return Service
     */
    private function setSecurity(): Service
    {
        $enable = $this->app->config->get('route_security.enable',false);
        $module = $this->app->config->get('route_security.module',[]);
        $this->security = compact('enable','module');
        return $this;
    }

    /**
     * 使用守卫信息
     * @param string $symbol
     * @return bool
     */
    private function makeSecurity(string $symbol):bool
    {
        /** 获取配置信息 */
        ['enable'=>$enable, 'module'=>$module] = $this->security;

        /** 未使用守卫 */
        if (!$symbol) return true;

        /** 关闭守卫 */
        if (!$enable) return true;

        /** 配置模块为空 */
        if (!array_key_exists($symbol,$module)) {
            throw new HttpException(500,"尚未配置守卫模块: $symbol");
        }

        /** 获取到绑定标识 */
        $provider = explode('\\', $module[$symbol]);
        $provider = array_pop($provider);

        /** 将用户提供者绑定到容器内 */
        $this->app->bind($provider, $module[$symbol]);
        $userProvider = $this->app->get($provider);

        /** 查看是否实现服务类 */
        if (!($userProvider instanceof UserProviderInterface)){
            throw new HttpException(500,"请实现 UserProviderInterface 服务类");
        }

        /** 使用用户提供者 */
        if ($userProvider->supports()){

            /** 获取到凭证 */
            $credentials = $userProvider->getCredentials();

            /** 获取到用户信息 */
            $this->app->request->userBadgeInfo  = $userProvider->getUser($credentials);
            $this->app->request->userIdentifier = $credentials;
            return true;
        }
        return false;
    }
}