<?php

declare(strict_types=1);

use app\modules\poll\domain\entities\PollRepositoryInterface;
use yii\web\Response;
use yii\web\User;

return [
    'container' => [
        'singletons' => [
            PollRepositoryInterface::class => '',
        ],
        'definitions' => [
            User::class => static fn () => Yii::$app->user,
            Response::class => static fn () => Yii::$app->response,
        ],
    ],
];
