<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\clientAnswer;

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
        $this->id = $id;
        $this->pollId = $pollId;
        $this->userId = $userId;
        $this->licenseId = $licenseId;
        $this->answers = $answers;
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
