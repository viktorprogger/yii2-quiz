<?php

declare(strict_types=1);

use app\modules\poll\domain\entities\PollRepositoryInterface;
use app\modules\poll\infrastruture\entities\PollRepository;
use app\modules\poll\UserCustom;
use yii\db\Connection;
use yii\web\Response;
use yii\web\User;

return [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'components' => [
        'user' => [
            'class' => UserCustom::class,
            'identityClass' => UserCustom::class,
        ],
        'db' => Connection::class,
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => [],
    'container' => [
        'singletons' => [
            PollRepositoryInterface::class => PollRepository::class,
            Connection::class => [
                'dsn' => 'mysql:host=' . getenv('DB_HOST') . '; dbname=' . getenv('DB_NAME'),
                'username' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
                'charset' => 'utf8',
            ],
        ],
        'definitions' => [
            User::class => static fn () => Yii::$app->user,
            Response::class => static fn () => Yii::$app->response,
        ],
    ]
];
