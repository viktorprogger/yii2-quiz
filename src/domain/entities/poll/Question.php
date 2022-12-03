<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\poll;

use JsonSerializable;

final class Question implements JsonSerializable
{
    private int $id;
    private string $text;
    /**
     * @var Answer[]
     */
    private array $answers;

    public function __construct(int $id, string $text, Answer ...$answers)
    {
        $this->id = $id;
        $this->text = $text;
        $this->answers = $answers;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return Answer[]
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'answers' => $this->answers,
        ];
    }
}
