<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\poll;

use app\modules\poll\domain\exceptions\DomainDataCorruptionException;
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
        $this->validate($title, $publishedFrom, $publishedTo, $questions);

        $this->title = $title;
        $this->publishedFrom = $publishedFrom;
        $this->publishedTo = $publishedTo;
        $this->userIds = $userIds;
        $this->questions = $questions;
    }

    private function validate(
        string $title,
        DateTimeImmutable $publishedFrom,
        DateTimeImmutable $publishedTo,
        array $questions
    ): void {
        if (mb_strlen($title) < 5) {
            throw new DomainDataCorruptionException(
                "Poll title must be a string of 5 characters or more, given '$title'"
            );
        }

        if ($publishedTo->diff($publishedFrom)->invert !== 1) {
            throw new DomainDataCorruptionException("Publish end date must be greater than publish start date");
        }

        if (count($questions) === 0) {
            throw new DomainDataCorruptionException("Poll must have at least one question");
        }
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
