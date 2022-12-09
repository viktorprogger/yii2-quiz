<?php

declare(strict_types=1);

namespace app\modules\poll\tests\integration\support;

use yii\console\controllers\MigrateController as YiiMigrateController;

final class MigrateController extends YiiMigrateController
{
    public function stdout($string)
    {
        // Do nothing
    }
}
