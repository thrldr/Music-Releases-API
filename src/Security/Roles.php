<?php

namespace App\Security;

class Roles
{
    const authenticated = "IS_AUTHENTICATED_FULLY";
    const admin = "ROLE_ADMIN";

    public static function isValid(string $role)
    {
        $reflection = new \ReflectionClass(self::class);
        $roles = $reflection->getConstants();
        return in_array($role, $roles);
    }
}