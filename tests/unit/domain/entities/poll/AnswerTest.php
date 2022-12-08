<?php

declare(strict_types=1);

namespace app\modules\poll\tests\unit\domain\entities\poll;

use app\modules\poll\domain\entities\clientAnswer\QuestionAnswer;
use app\modules\poll\domain\entities\poll\Answer;
use app\modules\poll\domain\exceptions\DomainDataCorruptionException;
use PHPUnit\Framework\TestCase;

class AnswerTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            'all is ok' => [
                [
                    1,
                    1,
                    'Answer test text',
                    true,
                ],
                json_encode(
                    [
                        'id' => 1,
                        'sort' => 1,
                        'text' => 'Answer test text',
                        'canBeCommented' => true,
                    ],
                    JSON_THROW_ON_ERROR
                ),
            ],
            'invalid id' => [
                [
                    0,
                    1,
                    'Answer test text',
                    true,
                ],
                '',
                "Entity ID must be a positive integer, given '0'",
            ],
            'Empty text' => [
                [
                    1,
                    1,
                    '',
                    true,
                ],
                '',
                "Answer text must be a non-empty string",
            ],
            'invalid two fields, the first is thrown' => [
                [
                    0,
                    1,
                    '',
                    true,
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

        $entity = new Answer(...$arguments);

        self::assertEquals($arguments[0], $entity->getId());
        self::assertEquals($arguments[1], $entity->getSort());
        self::assertEquals($arguments[2], $entity->getText());
        self::assertEquals($arguments[3], $entity->canBeCommented());
        self::assertEquals($serialized, json_encode($entity, JSON_THROW_ON_ERROR));
    }

    private function mockAnswer(): QuestionAnswer
    {
        return new QuestionAnswer(random_int(1, 1000), random_int(1, 1000));
    }
}
