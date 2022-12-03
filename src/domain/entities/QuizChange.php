<?php

declare(strict_types=1);

namespace app\modules\quiz\domain\entities;

use DateTimeImmutable;

final class QuizChange
{
    private string $title;
    private DateTimeImmutable $publishedFrom;
    private DateTimeImmutable $publishedTo;
    /**
     * @var QuestionChangeInterface[]
     */
    private array $questions;

    public function __construct(
        string $title,
        DateTimeImmutable $publishedFrom,
        DateTimeImmutable $publishedTo,
        QuestionChangeInterface ...$questions
    ) {
        $this->title = $title;
        $this->publishedFrom = $publishedFrom;
        $this->publishedTo = $publishedTo;
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
}
