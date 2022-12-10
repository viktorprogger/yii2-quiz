<?php

declare(strict_types=1);

namespace app\modules\poll\tests\unit\domain\entities\clientAnswer;

use app\modules\poll\domain\entities\clientAnswer\ClientAnswer;
use app\modules\poll\domain\entities\clientAnswer\QuestionAnswer;
use app\modules\poll\domain\exceptions\DomainDataCorruptionException;
use PHPUnit\Framework\TestCase;

class ClientAnswerTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            'all is ok' => [
                [
                    1,
                    1,
                    1,
                    1,
                    $this->mockAnswer(),
                    $this->mockAnswer(),
                ],
            ],
            'ok without questionAnswers' => [
                [
                    1,
                    1,
                    1,
                    1,
                ],
            ],
            'invalid id' => [
                [
                    0,
                    1,
                    1,
                    1,
                ],
                "Entity ID must be a positive integer, given '0'",
            ],
            'invalid pollId' => [
                [
                    1,
                    0,
                    1,
                    1,
                ],
                "Poll ID must be a positive integer, given '0'"
            ],
            'invalid userId' => [
                [
                    1,
                    1,
                    0,
                    1,
                ],
                "User ID must be a positive integer, given '0'"
            ],
            'invalid licenseId' => [
                [
                    1,
                    1,
                    1,
                    0,
                ],
                "License ID must be a positive integer, given '0'"
            ],
            'invalid two fields, the first is thrown' => [
                [
                    1,
                    1,
                    0,
                    0,
                ],
                "User ID must be a positive integer, given '0'"
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

        $entity = new ClientAnswer(...$arguments);

        self::assertEquals($arguments[0], $entity->getId());
        self::assertEquals($arguments[1], $entity->getPollId());
        self::assertEquals($arguments[2], $entity->getUserId());
        self::assertEquals($arguments[3], $entity->getLicenseId());
        self::assertEquals(array_slice($arguments, 4), $entity->getAnswers());
    }

    private function mockAnswer(): QuestionAnswer
    {
        return new QuestionAnswer(random_int(1, 1000), random_int(1, 1000), '');
    }
}
