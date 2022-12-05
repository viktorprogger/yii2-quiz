<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\poll;

use DateTimeImmutable;

final class PollChange
{
    private string $title;
    private DateTimeImmutable $publishedFrom;
    private DateTimeImmutable $publishedTo;
    /**
     * @var int[]
     */
    private array $userIds;
    /**
     * @var QuestionChangeInterface[]
     */
    private array $questions;

    public function __construct(
        string $title,
        DateTimeImmutable $publishedFrom,
        DateTimeImmutable $publishedTo,
        array $userIds,
        QuestionChangeInterface ...$questions
    ) {
        $this->title = $title;
        $this->publishedFrom = $publishedFrom;
        $this->publishedTo = $publishedTo;
        $this->userIds = $userIds;
        $this->questions = $questions;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPublishedFrom(): DateTimeImmutable
    {
        return $this->publishedFrom;
    }

    public function getPublishedTo(): DateTimeImmutable
    {
        return $this->publishedTo;
    }

    /**
     * @return QuestionChangeInterface[]
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }

    /**
     * @return int[]
     */
    public function getUserIds(): array
    {
        return $this->userIds;
    }
}
