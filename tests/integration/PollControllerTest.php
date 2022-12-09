<?php

declare(strict_types=1);

namespace app\modules\poll\tests\integration;

use app\modules\poll\domain\entities\poll\Poll;
use app\modules\poll\infrastruture\controllers\PollController;
use app\modules\poll\tests\integration\support\MigrateController;
use EnricoStahn\JsonAssert\Assert;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use stdClass;
use yii\console\Application;
use yii\db\Connection;
use yii\web\HttpException;
use yii\web\Response;

final class PollControllerTest extends TestCase
{
    use Assert;

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

        self::dbClear();

        parent::setUpBeforeClass();
    }

    public function creationDataProvider(): array
    {
        return [
            'question custom, all is ok' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'userIds' => [1, 2, 3],
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
                ],
            ],
            'question custom, all is ok without userIds' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
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
                ],
            ],
            'question custom, error in empty title' => [
                [
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
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
                ],
                true,
            ],
            'question custom, error in empty publishedFrom' => [
                [
                    'title' => 'test poll',
                    'publishedTo' => 1670526000,
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
                ],
                true,
            ],
            'question custom, error in empty publishedTo' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
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
                ],
                true,
            ],
            'question custom, error in empty questions' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'questions' => [],
                ],
                true,
            ],
            'error in question type' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'questions' => [
                        [
                            'type' => 'customs',
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
                ],
                true,
            ],
            'question custom, error in empty question text' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'questions' => [
                        [
                            'type' => 'custom',
                            'text' => '',
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
                ],
                true,
            ],
            'question custom, error in empty question answers' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'questions' => [
                        [
                            'type' => 'custom',
                            'text' => '',
                            'answers' => [],
                        ],
                    ],
                ],
                true,
            ],
            'question custom, error in question answers count' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'questions' => [
                        [
                            'type' => 'customs',
                            'text' => 'What do you like?',
                            'answers' => [
                                [
                                    'text' => 'Cat',
                                    'sort' => 100,
                                    'canBeCommented' => false,
                                ],
                            ],
                        ],
                    ],
                ],
                true,
            ],
            'question custom, error in empty answer text' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'questions' => [
                        [
                            'type' => 'customs',
                            'text' => 'What do you like?',
                            'answers' => [
                                [
                                    'text' => '',
                                    'sort' => 100,
                                    'canBeCommented' => false,
                                ],
                            ],
                        ],
                    ],
                ],
                true,
            ],
            'question rated, all is ok' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'userIds' => [1, 2, 3],
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'What do you like?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
            ],
            'question rated, all is ok without userIds' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'What do you like?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
            ],
            'question rated, error in empty title' => [
                [
                    'title' => '',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'What do you like?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
                true,
            ],
            'question rated, error in empty publishedFrom' => [
                [
                    'title' => 'test poll',
                    'publishedTo' => 1670526000,
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'What do you like?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
                true,
            ],
            'question rated, error in empty publishedTo' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'What do you like?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
                true,
            ],
            'question rated, error in empty question text' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => '',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
                true,
            ],
            'question rated, error in question maximum rate' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'What do you like?',
                            'maximum' => 1,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
                true,
            ],
            'question rated, error in big dontCommentSince' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'What do you like?',
                            'maximum' => 10,
                            'dontCommentSince' => 11,
                        ],
                    ],
                ],
                true,
            ],
            'question rated, error in low dontCommentSince' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'What do you like?',
                            'maximum' => 10,
                            'dontCommentSince' => 0,
                        ],
                    ],
                ],
                true,
            ],
            'questions combined, all is ok' => [
                [
                    'title' => 'test poll',
                    'publishedFrom' => 1670525731,
                    'publishedTo' => 1670526000,
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'What do you like?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
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
                ],
            ],
        ];
    }

    /**
     * @dataProvider creationDataProvider
     */
    public function testPollCreation(array $data, bool $expectException = false): void
    {
        if ($expectException) {
            $this->expectException(HttpException::class);
        }

        $controller = self::$application->createControllerByID('poll');
        self::assertInstanceOf(PollController::class, $controller);

        $response = $this->createMock(Response::class);
        if ($expectException) {
            $response->method('setStatusCode')->willReturn($response);
        } else {
            $response->expects(self::once())->method('setStatusCode')->willReturn($response);
        }
        $controller->actionCreate($data, $response);
    }

    public function gettingDataProvider(): array
    {
        return [
            'empty data, empty result' => [
                null,
            ],
            'only old poll, empty result' => [
                null,
                [
                    'title' => 'test poll',
                    'publishedFrom' => strtotime('-2 weeks'),
                    'publishedTo' => strtotime('-1 week'),
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'How do you like this test?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
            ],
            'one actual poll' => [
                'test poll',
                [
                    'title' => 'test poll',
                    'publishedFrom' => strtotime('-2 weeks'),
                    'publishedTo' => strtotime('+1 week'),
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'How do you like this test?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
            ],
            'two actual polls, the oldest is returned' => [
                'test poll old',
                [
                    'title' => 'test poll old',
                    'publishedFrom' => strtotime('-2 weeks'),
                    'publishedTo' => strtotime('+1 week'),
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'How do you like this test?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
                [
                    'title' => 'test poll new',
                    'publishedFrom' => strtotime('-1 weeks'),
                    'publishedTo' => strtotime('+1 week'),
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'How do you like this test?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
            ],
            'one actual and one archived polls, the actual is returned' => [
                'test poll new',
                [
                    'title' => 'test poll old',
                    'publishedFrom' => strtotime('-2 weeks'),
                    'publishedTo' => strtotime('yesterday'),
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'How do you like this test?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
                [
                    'title' => 'test poll new',
                    'publishedFrom' => strtotime('-1 weeks'),
                    'publishedTo' => strtotime('+1 week'),
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'How do you like this test?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
            ],
            'one common and one addressed polls, addressed is returned' => [
                'test poll addressed',
                [
                    'title' => 'test poll common',
                    'publishedFrom' => strtotime('-2 weeks'),
                    'publishedTo' => strtotime('+1 week'),
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'How do you like this test?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
                [
                    'title' => 'test poll addressed',
                    'publishedFrom' => strtotime('-1 weeks'),
                    'publishedTo' => strtotime('+1 week'),
                    'userIds' => [1, 2, 3],
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'How do you like this test?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
            ],
            'one common and one addressed foreign polls, common is returned' => [
                'test poll common',
                [
                    'title' => 'test poll common',
                    'publishedFrom' => strtotime('-2 weeks'),
                    'publishedTo' => strtotime('+1 week'),
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'How do you like this test?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
                [
                    'title' => 'test poll addressed',
                    'publishedFrom' => strtotime('-1 weeks'),
                    'publishedTo' => strtotime('+1 week'),
                    'userIds' => [2, 3],
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'How do you like this test?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
            ],
            'one common and one addressed archived polls, common is returned' => [
                'test poll common',
                [
                    'title' => 'test poll common',
                    'publishedFrom' => strtotime('-2 weeks'),
                    'publishedTo' => strtotime('+1 week'),
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'How do you like this test?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
                [
                    'title' => 'test poll addressed',
                    'publishedFrom' => strtotime('-1 weeks'),
                    'publishedTo' => strtotime('yesterday'),
                    'userIds' => [1, 2, 3],
                    'questions' => [
                        [
                            'type' => 'rated',
                            'text' => 'How do you like this test?',
                            'maximum' => 10,
                            'dontCommentSince' => 5,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider gettingDataProvider
     */
    public function testPollGetting(?string $pollExpected, array ...$pollsToCreate): void
    {
        self::dbClear();

        /** @var PollController $controller */
        $controller = self::$application->createControllerByID('poll');
        $response = $this->createMock(Response::class);
        $response->method('setStatusCode')->willReturn($response);
        foreach ($pollsToCreate as $data) {
            $controller->actionCreate($data, $response);
        }

        $poll = $controller->actionGet();

        if ($pollExpected === null) {
            self::assertNull($poll);
        } else {
            self::assertInstanceOf(Poll::class, $poll);
            self::assertInstanceOf(JsonSerializable::class, $poll);

            $poll = json_decode(json_encode($poll, JSON_THROW_ON_ERROR), false);
            self::assertJsonMatchesSchema($poll, __DIR__ . '/support/pollSchema.json');
            self::assertJsonValueEquals($pollExpected, 'title', $poll);
        }
    }

    private static function dbClear(): void
    {
        ob_start();
        $migrator = new MigrateController('migrate', self::$application, ['interactive' => false]);
        $migrator->runAction('fresh');
        $migrator->runAction('up');
        ob_end_clean();
    }
}
