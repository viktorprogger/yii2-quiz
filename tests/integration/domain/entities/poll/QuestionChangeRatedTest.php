<?php

declare(strict_types=1);

namespace app\modules\poll\tests\integration\domain\entities\poll;

use app\modules\poll\domain\entities\poll\QuestionChangeRated;
use PHPUnit\Framework\TestCase;

class QuestionChangeRatedTest extends TestCase
{
    public function testAnswersCreation(): void
    {
        $text = 'Question text';
        $maximum = 10;
        $dontCommentSince = 5;
        $entity = new QuestionChangeRated($text, $maximum, $dontCommentSince);

        $answers = [];
        foreach ($entity->getAnswers() as $answer) {
            $answers[$answer->getText()] = $answer->getSort();
            self::assertEquals((int) $answer->getText() < $dontCommentSince, $answer->canBeCommented());
        }
        ksort($answers);

        self::assertCount($maximum, $answers);
        self::assertEquals(1, array_key_first($answers));
        self::assertEquals($maximum, array_key_last($answers));

        $sortPrevious = PHP_INT_MIN;
        foreach ($answers as $sort) {
            self::assertGreaterThan($sortPrevious, $sort);
            $sortPrevious = $sort;
        }
    }
}
