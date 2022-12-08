<?php

declare(strict_types=1);

namespace app\modules\poll\tests\integration;

use app\modules\poll\infrastruture\controllers\PollController;
use PHPUnit\Framework\TestCase;
use stdClass;
use yii\console\Application;
use yii\console\controllers\MigrateController;
use yii\db\Connection;
use yii\web\Response;

final class PollControllerTest extends TestCase
{
    private static $application;

    public static function setUpBeforeClass(): void
    {
        define('YII_ENV', 'test');
        require_once dirname(__DIR__, 2) . '/vendor/yiisoft/yii2/Yii.php';
        $user = new stdClass();
        $user->id = 1;

        self::$application = new Application(
            [
                'id' => 'app',
                'basePath' => dirname(__DIR__, 2),
                'controllerNamespace' => 'app\modules\poll\infrastruture\controllers',
                'components' => [
                    'db' => Connection::class,
                    'user' => $user,
                ],
            ] + require dirname(__DIR__, 2) . '/config/main.php'
        );

        $migrator = new MigrateController('migrate', self::$application, ['interactive' => false]);
        $migrator->runAction('fresh');
        $migrator->runAction('up');

        parent::setUpBeforeClass();
    }

    public function testPollCreation()
    {
        $controller = self::$application->createControllerByID('poll');
        self::assertInstanceOf(PollController::class, $controller);

        $data = [
            'title' => 'test poll',
            'publishedFrom' => 1670525731,
            'publishedTo' => 1670526000,
            'userIds' => [],
            'questions' => [
                [
                    'type' => 'custom',
                    'text' => 'What do you like?',
                    'answers' => [
                        [
                            'text' => 'Cat',
                            'sort' => 100,
                            'canBeCommented' => false,
                        ],
                        [
                            'text' => 'Dog',
                            'sort' => 100,
                            'canBeCommented' => false,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->createMock(Response::class);
        $response->method('setStatusCode')->willReturn($response);
        $controller->actionCreate($data, $response);
    }
}
