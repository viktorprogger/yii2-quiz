<?php

declare(strict_types=1);

namespace app\modules\poll;

use yii\base\Module as BaseModule;

final class Module extends BaseModule
{
    public $controllerNamespace = __NAMESPACE__ . '\\infrastructure\\controllers';
}
