<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\clientAnswer;

use app\modules\poll\domain\exceptions\DomainDataCorruptionException;

final class QuestionAnswer
{
    private int $questionId;
    private int $answerId;

    public function __construct(int $questionId, int $answerId)
    {
        $this->validate($questionId, $answerId);

        $this->questionId = $questionId;
        $this->answerId = $answerId;
    }

    private function validate(int $questionId, int $answerId): void
    {
        if ($questionId < 1) {
            throw new DomainDataCorruptionException("Question ID must be a positive integer, given '$questionId'");
        }
        if ($answerId < 1) {
            throw new DomainDataCorruptionException("Answer ID must be a positive integer, given '$answerId'");
        }
    }

    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    public function getAnswerId(): int
    {
        return $this->answerId;
    }
}
