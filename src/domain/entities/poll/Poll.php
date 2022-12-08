<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\poll;

use app\modules\poll\domain\exceptions\DomainDataCorruptionException;
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
        $this->validate($id, $title, $publishedFrom, $publishedTo);

        $this->id = $id;
        $this->title = $title;
        $this->publishedFrom = $publishedFrom;
        $this->publishedTo = $publishedTo;
        $this->userIds = $userIds;
        $this->questions = $questions;
    }

    private function validate(
        int $id,
        string $title,
        DateTimeImmutable $publishedFrom,
        DateTimeImmutable $publishedTo
    ): void {
        if ($id < 1) {
            throw new DomainDataCorruptionException("Entity ID must be a positive integer, given '$id'");
        }
        if (mb_strlen($title) < 5) {
            throw new DomainDataCorruptionException(
                "Poll title must be a string of 5 characters or more, given '$title'"
            );
        }
        if ($publishedFrom->diff($publishedTo)->invert !== 1) {
            throw new DomainDataCorruptionException("Publish end date must be greater than publish start date");
        }
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
