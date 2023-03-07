<?php
declare(strict_types=1);
namespace NiceYu\ThinkAnnotation\Security;

use stdClass;

class AdminUserProvider implements UserProviderInterface
{
    public function supports(): bool
    {
        // TODO: Implement supports() method.
        return true;
    }

    public function getCredentials(): string
    {
        // TODO: Implement getCredentials() method.
        return 'getCredentials';
    }

    public function getUser(string $credentials): object
    {
        // TODO: Implement getUser() method.
        $user = new stdClass();
        $user->id = 10086;
        return $user;
    }
}