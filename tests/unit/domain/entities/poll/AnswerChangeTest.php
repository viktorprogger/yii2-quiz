<?php

declare(strict_types=1);

namespace app\modules\poll\tests\unit\domain\entities\poll;

use app\modules\poll\domain\entities\clientAnswer\QuestionAnswer;
use app\modules\poll\domain\entities\poll\Answer;
use app\modules\poll\domain\entities\poll\AnswerChange;
use app\modules\poll\domain\exceptions\DomainDataCorruptionException;
use PHPUnit\Framework\TestCase;

class AnswerChangeTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            'all is ok' => [
                [
                    1,
                    'Answer test text',
                    true,
                ],
            ],
            'Empty text' => [
                [
                    1,
                    '',
                    true,
                ],
                "Answer text must be a non-empty string",
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

        $entity = new AnswerChange(...$arguments);

        self::assertEquals($arguments[0], $entity->getSort());
        self::assertEquals($arguments[1], $entity->getText());
        self::assertEquals($arguments[2], $entity->canBeCommented());
    }

    private function mockAnswer(): QuestionAnswer
    {
        return new QuestionAnswer(random_int(1, 1000), random_int(1, 1000));
    }
}
