<?php

declare(strict_types=1);

namespace app\modules\poll\tests\integration\support;

use yii\web\User as YiiUser;

class User extends YiiUser
{
    public function getLicenseId(): int
    {
        return 2;
    }
}
