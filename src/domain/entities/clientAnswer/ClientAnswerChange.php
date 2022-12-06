<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\clientAnswer;

use app\modules\poll\domain\entities\exceptions\DomainDataCorruptionException;

final class ClientAnswerChange
{
    private int $pollId;
    private int $userId;
    private int $licenseId;
    /**
     * @var QuestionAnswer[]
     */
    private array $answers;

    public function __construct(int $pollId, int $userId, int $licenseId, QuestionAnswer ...$answers)
    {
        $this->validate($pollId, $userId, $licenseId);
        $this->pollId = $pollId;
        $this->userId = $userId;
        $this->licenseId = $licenseId;
        $this->answers = $answers;
    }

    private function validate(int $pollId, int $userId, int $licenseId): void
    {
        if ($pollId < 1) {
            throw new DomainDataCorruptionException("Poll ID must be a positive integer, given '$pollId'");
        }
        if ($userId < 1) {
            throw new DomainDataCorruptionException("User ID must be a positive integer, given '$userId'");
        }
        if ($licenseId < 1) {
            throw new DomainDataCorruptionException("License ID must be a positive integer, given '$licenseId'");
        }
    }

    public function getPollId(): int
    {
        return $this->pollId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getLicenseId(): int
    {
        return $this->licenseId;
    }

    /**
     * @return QuestionAnswer[]
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }
}
