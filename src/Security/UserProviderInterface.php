<?php
declare(strict_types=1);

namespace NiceYu\ThinkAnnotation\Security;
interface UserProviderInterface
{
    /**
     * 是否开启安全模式
     * @return bool
     */
    public function supports(): bool;

    /**
     * 获取凭证
     * @return string
     */
    public function getCredentials(): string;

    /**
     * 得到用户信息
     * @param string $credentials
     * @return object
     */
    public function getUser(string $credentials):object;
}