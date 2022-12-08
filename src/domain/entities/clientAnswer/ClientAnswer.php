<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\clientAnswer;

use app\modules\poll\domain\exceptions\DomainDataCorruptionException;

final class ClientAnswer
{
    private int $pollId;
    private int $userId;
    private int $licenseId;
    /**
     * @var QuestionAnswer[]
     */
    private array $answers;
    private int $id;

    public function __construct(int $id, int $pollId, int $userId, int $licenseId, QuestionAnswer ...$answers)
    {
        $this->validate($id, $pollId, $userId, $licenseId);

        $this->id = $id;
        $this->pollId = $pollId;
        $this->userId = $userId;
        $this->licenseId = $licenseId;
        $this->answers = $answers;
    }

    private function validate(int $id, int $pollId, int $userId, int $licenseId): void
    {
        if ($id < 1) {
            throw new DomainDataCorruptionException("Entity ID must be a positive integer, given '$id'");
        }
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

    public function getId(): int
    {
        return $this->id;
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
