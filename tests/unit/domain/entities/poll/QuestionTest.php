<?php

declare(strict_types=1);

namespace app\modules\poll\tests\unit\domain\entities\poll;

use app\modules\poll\domain\entities\clientAnswer\QuestionAnswer;
use app\modules\poll\domain\entities\poll\Answer;
use app\modules\poll\domain\entities\poll\Question;
use app\modules\poll\domain\exceptions\DomainDataCorruptionException;
use PHPUnit\Framework\TestCase;

class QuestionTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            'all is ok' => [
                [
                    1,
                    'Question test text',
                    new Answer(1, 2, 'Answer test text', true),
                    new Answer(3, 4, 'Answer test text', true),
                ],
                json_encode(
                    [
                        'id' => 1,
                        'text' => 'Question test text',
                        'answers' => [
                            [
                                'id' => 1,
                                'sort' => 2,
                                'text' => 'Answer test text',
                                'canBeCommented' => true,
                            ],
                            [
                                'id' => 3,
                                'sort' => 4,
                                'text' => 'Answer test text',
                                'canBeCommented' => true,
                            ],
                        ],
                    ],
                    JSON_THROW_ON_ERROR
                ),
            ],
            'all is ok without answers' => [
                [
                    1,
                    'Question test text',
                ],
                json_encode(
                    [
                        'id' => 1,
                        'text' => 'Question test text',
                        'answers' => [],
                    ],
                    JSON_THROW_ON_ERROR
                ),
            ],
            'invalid id' => [
                [
                    0,
                    'Question test text',
                ],
                '',
                "Entity ID must be a positive integer, given '0'",
            ],
            'Empty text' => [
                [
                    1,
                    '',
                ],
                '',
                "Question text must be a non-empty string",
            ],
            'invalid two fields, the first is thrown' => [
                [
                    0,
                    '',
                ],
                '',
                "Entity ID must be a positive integer, given '0'",
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testEntityCreation(array $arguments, string $serialized, ?string $exceptionText = null): void
    {
        if ($exceptionText !== null) {
            $this->expectException(DomainDataCorruptionException::class);
            $this->expectExceptionMessage($exceptionText);
        }

        $entity = new Question(...$arguments);

        self::assertEquals($arguments[0], $entity->getId());
        self::assertEquals($arguments[1], $entity->getText());
        self::assertEquals(array_slice($arguments, 2), $entity->getAnswers());
        self::assertEquals($serialized, json_encode($entity, JSON_THROW_ON_ERROR));
    }
}
