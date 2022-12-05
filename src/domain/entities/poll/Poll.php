<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\poll;

use DateTimeImmutable;
use JsonSerializable;

final class Poll implements JsonSerializable
{
    private int $id;
    private string $title;
    private DateTimeImmutable $publishedFrom;
    private DateTimeImmutable $publishedTo;
    /**
     * @var int[]
     */
    private array $userIds;
    /**
     * @var Question[]
     */
    private array $questions;

    public function __construct(
        int $id,
        string $title,
        DateTimeImmutable $publishedFrom,
        DateTimeImmutable $publishedTo,
        array $userIds,
        Question ...$questions
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->publishedFrom = $publishedFrom;
        $this->publishedTo = $publishedTo;
        $this->userIds = $userIds;
        $this->questions = $questions;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getPublishedFrom(): DateTimeImmutable
    {
        return $this->publishedFrom;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getPublishedTo(): DateTimeImmutable
    {
        return $this->publishedTo;
    }

    /**
     * @return Question[]
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

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'publishedFrom' => $this->publishedFrom->getTimestamp(),
            'publishedTo' => $this->publishedTo->getTimestamp(),
            'questions' => $this->questions,
        ];
    }
}
