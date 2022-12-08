<?php

declare(strict_types=1);

namespace app\modules\poll\tests\unit\domain\entities\poll;

use app\modules\poll\domain\entities\poll\Answer;
use app\modules\poll\domain\entities\poll\AnswerChange;
use app\modules\poll\domain\entities\poll\Question;
use app\modules\poll\domain\entities\poll\QuestionChangeCustom;
use app\modules\poll\domain\exceptions\DomainDataCorruptionException;
use PHPUnit\Framework\TestCase;

class QuestionChangeCustomTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            'all is ok' => [
                [
                    'Question test text',
                    new AnswerChange(2, 'Answer test text', true),
                    new AnswerChange( 4, 'Answer test text', true),
                ],
            ],
            'answers can\'t be empty' => [
                [
                    'Question test text',
                ],
                'Question must have at least two answers',
            ],
            'Error with one answer' => [
                [
                    'Question test text',
                    new AnswerChange(2, 'Answer test text', true),
                ],
                'Question must have at least two answers',
            ],
            'Empty text' => [
                [
                    '',
                    new AnswerChange(2, 'Answer test text', true),
                ],
                "Question text must be a non-empty string",
            ],
            'invalid two fields, the first is thrown' => [
                [
                    '',
                ],
                "Question text must be a non-empty string",
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

        $entity = new QuestionChangeCustom(...$arguments);

        self::assertEquals($arguments[0], $entity->getText());
        self::assertEquals(array_slice($arguments, 1), $entity->getAnswers());
    }
}
