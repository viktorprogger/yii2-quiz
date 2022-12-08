<?php

declare(strict_types=1);

namespace app\modules\poll\tests\unit\domain\entities\clientAnswer;

use app\modules\poll\domain\entities\clientAnswer\QuestionAnswer;
use app\modules\poll\domain\exceptions\DomainDataCorruptionException;
use PHPUnit\Framework\TestCase;

class QuestionAnswerTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            'all is ok' => [
                [
                    1,
                    1,
                    ],
            ],
            'invalid answerId' => [
                [
                    0,
                    1,
                ],
                "Question ID must be a positive integer, given '0'",
            ],
            'invalid pollId' => [
                [
                    1,
                    0,
                ],
                "Answer ID must be a positive integer, given '0'",
            ],
            'invalid two fields, the first is thrown' => [
                [
                    0,
                    0,
                ],
                "Question ID must be a positive integer, given '0'",
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testEntityCreation(array $arguments, ?string $exceptionText = null): void
    {
        if ($exceptionText !== null) {
            $this->expectException(DomainDataCorruptionException::class);
            $this->expectExceptionMessage($exceptionText);
        }

        $entity = new QuestionAnswer(...$arguments);

        self::assertEquals($arguments[0], $entity->getAnswerId());
        self::assertEquals($arguments[1], $entity->getQuestionId());
    }
}
