<?php

declare(strict_types=1);

namespace app\modules\poll;

use yii\web\IdentityInterface;
use yii\web\User;

final class UserCustom extends User implements IdentityInterface
{
    public $identityClass = self::class;

    public static function findIdentity($id)
    {
        return new self();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return new self();
    }

    public function getAuthKey()
    {
        return 'abcde';
    }

    public function validateAuthKey($authKey)
    {
        return true;
    }

    public function getLicenseId(): int
    {
        return 123;
    }

    public function loginByAccessToken($token, $type = null): self
    {
        return new self();
    }

    public function getId(): int
    {
        return 11;
    }
}
