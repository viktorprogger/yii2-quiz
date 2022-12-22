<?php

declare(strict_types=1);

namespace app\modules\poll\tests\integration;

use app\modules\poll\domain\entities\poll\Poll;
use app\modules\poll\infrastruture\controllers\PollController;
use app\modules\poll\tests\integration\support\MigrateController;
use app\modules\poll\tests\integration\support\User;
use EnricoStahn\JsonAssert\Assert;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use yii\console\Application;
use yii\db\Connection;
use yii\web\HttpException;
use yii\web\Request;
use yii\web\Response;

final class PollControllerTest extends TestCase
{
    use Assert;

    private static $application;

    /**
     * @param array $data
     *
     * @return Request
     */
    public function getRequest(array $data): Request
    {
        $_POST = $data;
        $_POST['_method'] = 'POST';

        return new Request();
    }

    public static function setUpBeforeClass(): void
    {
        define('YII_ENV', 'test');
        require_once dirname(__DIR__, 2) . '/vendor/yiisoft/yii2/Yii.php';

        self::$application = new Application(
            [
                'id' => 'app',
                'basePath' => dirname(__DIR__, 2),
                'controllerNamespace' => 'app\modules\poll\infrastruture\controllers',
                'components' => [
                    'db' => Connection::class,
                ],
            ] + require dirname(__DIR__, 2) . '/config/web.php'
        );

        parent::setUpBeforeClass();
    }

    protected function setUp(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        $user->method('__get')->with('id')->willReturn(1);
        $user->method('getLicenseId')->willReturn(2);
        self::$application->set('user', $user);

        self::dbClear();
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
        $request = $this->getRequest($data);
        self::assertInstanceOf(PollController::class, $controller);
        self::assertInstanceOf(Poll::class, $controller->actionCreate($request));
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
            'one common archived and one addressed archived polls, none is returned' => [
                null,
                [
                    'title' => 'test poll common',
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
        /** @var PollController $controller */
        $controller = self::$application->createControllerByID('poll');
        foreach ($pollsToCreate as $data) {
            $request = $this->getRequest($data);
            self::assertInstanceOf(Poll::class, $controller->actionCreate($request));
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

    public function answeringDataProvider(): array
    {
        return [
            'all is ok' => [
                [
                    'What do you like?' => ['answer' => 'Cat'],
                    'How much do you like it?' => ['answer' => '5'],
                ],
            ],
            'all is ok with comments' => [
                [
                    'What do you like?' => ['answer' => 'Cat', 'comment' => 'Cats are fluffies'],
                    'How much do you like it?' => ['answer' => '4', 'comment' => 'I don\'t like animals at all'],
                ],
            ],
            'error, unexpected comment' => [
                [
                    'What do you like?' => ['answer' => 'Cat', 'comment' => 'Cats are fluffies'],
                    'How much do you like it?' => ['answer' => '5', 'comment' => 'I don\'t like animals at all'],
                ],
                '/Answer #\d+ can\'t be commented/',
            ],
            'error, unknown answer' => [
                [
                    'What do you like?' => ['answer' => 'Parrots'],
                    'How much do you like it?' => ['answer' => '5'],
                ],
                "/Answer #\d+ doesn't belong to question #\d+/",
            ],
            'error, unknown question' => [
                [
                    'What do you prefer?' => ['answer' => 'Cat'],
                    'How much do you like it?' => ['answer' => '5'],
                ],
                "/Question #\d+ doesn't belong to poll #\d+/",
            ],
            'error, too little answers' => [
                [
                    'What do you like?' => ['answer' => 'Cat'],
                ],
                "/Not all questions are answered/",
            ],
        ];
    }

    /**
     * @dataProvider answeringDataProvider
     */
    public function testAnswering($answers, ?string $exceptionMessage = null): void
    {
        if ($exceptionMessage !== null) {
            $this->expectExceptionMessageMatches($exceptionMessage);
        }

        $pollData = [
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
                            'canBeCommented' => true,
                        ],
                        [
                            'text' => 'Dog',
                            'sort' => 100,
                            'canBeCommented' => false,
                        ],
                    ],
                ],
                [
                    'type' => 'rated',
                    'text' => 'How much do you like it?',
                    'maximum' => 10,
                    'dontCommentSince' => 5,
                ],
            ],
        ];
        /** @var PollController $controller */
        $controller = self::$application->createControllerByID('poll');
        $request = $this->getRequest($pollData);
        $poll = $controller->actionCreate($request);

        $data = [];
        foreach ($answers as $questionText => $answerDefinition) {
            $questionId = $answerId = null;
            foreach ($poll->getQuestions() as $question) {
                if ($question->getText() === $questionText) {
                    $questionId = $question->getId();
                    foreach ($question->getAnswers() as $answer) {
                        if ($answer->getText() === $answerDefinition['answer']) {
                            $answerId = $answer->getId();
                        }
                    }
                }
            }

            $data[] = [
                'questionId' => $questionId ?? random_int(99999, 9999999),
                'answerId' => $answerId ?? random_int(99999, 9999999),
                'comment' => $answerDefinition['comment'] ?? '',
            ];
        }

        $response = $this->createMock(Response::class);
        if ($exceptionMessage === null) {
            $response
                ->expects(self::once())
                ->method('setStatusCode')
                ->with(201)
                ->willReturn($response);
        }
        $request = $this->getRequest($data);
        $controller->actionAnswer($poll->getId(), $request, self::$application->get('user'), $response);
    }

    public function testCantGetAnswered(): void
    {
        $pollCommonDefinition = [
            'title' => 'test poll',
            'publishedFrom' => strtotime('-2 weeks'),
            'publishedTo' => strtotime('+2 weeks'),
            'questions' => [
                [
                    'type' => 'rated',
                    'text' => 'How do you like this test?',
                    'maximum' => 10,
                    'dontCommentSince' => 5,
                ],
            ],
        ];
        $pollPersonalDefinition = [
            'title' => 'test poll',
            'userIds' => [1],
            'publishedFrom' => strtotime('-2 weeks'),
            'publishedTo' => strtotime('+2 weeks'),
            'questions' => [
                [
                    'type' => 'rated',
                    'text' => 'How do you like this test?',
                    'maximum' => 10,
                    'dontCommentSince' => 5,
                ],
            ],
        ];
        /** @var PollController $controller */
        $controller = self::$application->createControllerByID('poll');

        $request = $this->getRequest($pollCommonDefinition);
        $pollCommon = $controller->actionCreate($request);

        $request = $this->getRequest($pollPersonalDefinition);
        $pollPersonal = $controller->actionCreate($request);

        $pollActual = $controller->actionGet();
        self::assertContainsOnlyInstancesOf(Poll::class, [$pollCommon, $pollPersonal, $pollActual]);
        self::assertEquals($pollPersonal->getId(), $pollActual->getId(), 'The first poll must be the personal one');

        $response = $this->createMock(Response::class);
        $response->method('setStatusCode')->willReturn($response);

        $data = [
            [
                'questionId' => $pollActual->getQuestions()[0]->getId(),
                'answerId' => $pollActual->getQuestions()[0]->getAnswers()[0]->getId(),
            ],
        ];
        $request = $this->getRequest($data);
        $controller->actionAnswer(
            $pollActual->getId(),
            $request,
            self::$application->get('user'),
            $response
        );

        $pollActual = $controller->actionGet();
        self::assertInstanceOf(Poll::class, $pollActual);
        self::assertEquals($pollCommon->getId(), $pollActual->getId(), 'When the personal poll is answered, only the common one is available');

        $data = [
            [
                'questionId' => $pollActual->getQuestions()[0]->getId(),
                'answerId' => $pollActual->getQuestions()[0]->getAnswers()[0]->getId(),
            ],
        ];
        $request = $this->getRequest($data);
        $controller->actionAnswer(
            $pollActual->getId(),
            $request,
            self::$application->get('user'),
            $response
        );

        $pollActual = $controller->actionGet();
        self::assertNull($pollActual, 'There are no unanswered poll for the current user');
    }

    public function testRejection(): void
    {
        $pollDefinition = [
            'title' => 'test poll',
            'publishedFrom' => strtotime('-2 weeks'),
            'publishedTo' => strtotime('+2 weeks'),
            'questions' => [
                [
                    'type' => 'rated',
                    'text' => 'How do you like this test?',
                    'maximum' => 10,
                    'dontCommentSince' => 5,
                ],
            ],
        ];
        /** @var PollController $controller */
        $controller = self::$application->createControllerByID('poll');
        $request = $this->getRequest($pollDefinition);
        $poll = $controller->actionCreate($request);
        $response = $this->createMock(Response::class);
        $response->expects(self::once())->method('setStatusCode')->willReturn($response);
        $controller->actionReject($poll->getId(), self::$application->get('user'), $response);
    }

    public function testCantAnswerAnswered(): void
    {
        $this->expectExceptionMessage('This poll is answered already');

        $pollDefinition = [
            'title' => 'test poll',
            'publishedFrom' => strtotime('-2 weeks'),
            'publishedTo' => strtotime('+2 weeks'),
            'questions' => [
                [
                    'type' => 'rated',
                    'text' => 'How do you like this test?',
                    'maximum' => 10,
                    'dontCommentSince' => 5,
                ],
            ],
        ];
        /** @var PollController $controller */
        $controller = self::$application->createControllerByID('poll');
        $request = $this->getRequest($pollDefinition);
        $poll = $controller->actionCreate($request);
        $response = $this->createMock(Response::class);
        $response->expects(self::once())->method('setStatusCode')->willReturn($response);
        $data = [
            [
                'questionId' => $poll->getQuestions()[0]->getId(),
                'answerId' => $poll->getQuestions()[0]->getAnswers()[0]->getId(),
            ],
        ];
        $request = $this->getRequest($data);
        $controller->actionAnswer(
            $poll->getId(),
            $request,
            self::$application->get('user'),
            $response
        );
        $controller->actionAnswer(
            $poll->getId(),
            $request,
            self::$application->get('user'),
            $response
        );
    }

    public function testCantRejectRejected(): void
    {
        $this->expectExceptionMessage('This poll is answered already');

        $pollDefinition = [
            'title' => 'test poll',
            'publishedFrom' => strtotime('-2 weeks'),
            'publishedTo' => strtotime('+2 weeks'),
            'questions' => [
                [
                    'type' => 'rated',
                    'text' => 'How do you like this test?',
                    'maximum' => 10,
                    'dontCommentSince' => 5,
                ],
            ],
        ];
        /** @var PollController $controller */
        $controller = self::$application->createControllerByID('poll');
        $request = $this->getRequest($pollDefinition);
        $poll = $controller->actionCreate($request);
        $response = $this->createMock(Response::class);
        $response->expects(self::once())->method('setStatusCode')->willReturn($response);
        $controller->actionReject($poll->getId(), self::$application->get('user'), $response);
        $controller->actionReject($poll->getId(), self::$application->get('user'), $response);
    }

    public function testCantAnswerForeignPoll(): void
    {
        $this->expectExceptionMessage('User is not allowed to answer to this poll');

        $pollDefinition = [
            'title' => 'test poll',
            'userIds' => [1],
            'publishedFrom' => strtotime('-2 weeks'),
            'publishedTo' => strtotime('+2 weeks'),
            'questions' => [
                [
                    'type' => 'rated',
                    'text' => 'How do you like this test?',
                    'maximum' => 10,
                    'dontCommentSince' => 5,
                ],
            ],
        ];
        /** @var PollController $controller */
        $controller = self::$application->createControllerByID('poll');
        $request = $this->getRequest($pollDefinition);
        $poll = $controller->actionCreate($request);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(2);
        $user->method('__get')->with('id')->willReturn(2);
        $user->method('getLicenseId')->willReturn(2);

        $response = $this->createMock(Response::class);
        $data = [
            [
                'questionId' => $poll->getQuestions()[0]->getId(),
                'answerId' => $poll->getQuestions()[0]->getAnswers()[0]->getId(),
            ],
        ];
        $request = $this->getRequest($data);
        $controller->actionAnswer(
            $poll->getId(),
            $request,
            $user,
            $response
        );
    }

    public function testCantRejectForeignPoll(): void
    {
        $this->expectExceptionMessage('User is not allowed to answer to this poll');

        $pollDefinition = [
            'title' => 'test poll',
            'userIds' => [1],
            'publishedFrom' => strtotime('-2 weeks'),
            'publishedTo' => strtotime('+2 weeks'),
            'questions' => [
                [
                    'type' => 'rated',
                    'text' => 'How do you like this test?',
                    'maximum' => 10,
                    'dontCommentSince' => 5,
                ],
            ],
        ];
        /** @var PollController $controller */
        $controller = self::$application->createControllerByID('poll');
        $request = $this->getRequest($pollDefinition);
        $poll = $controller->actionCreate($request);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(2);
        $user->method('__get')->with('id')->willReturn(2);
        $user->method('getLicenseId')->willReturn(2);

        $response = $this->createMock(Response::class);
        $controller->actionReject($poll->getId(), $user, $response);
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
