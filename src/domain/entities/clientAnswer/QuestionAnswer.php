<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\clientAnswer;

use app\modules\poll\domain\exceptions\DomainDataCorruptionException;

final class QuestionAnswer
{
    private int $questionId;
    private int $answerId;
    private string $comment;

    public function __construct(int $questionId, int $answerId, string $comment)
    {
        $this->validate($questionId, $answerId);

        $this->questionId = $questionId;
        $this->answerId = $answerId;
        $this->comment = $comment;
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

    public function getComment(): string
    {
        return $this->comment;
    }
}
