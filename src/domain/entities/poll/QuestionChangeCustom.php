<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\poll;

final class QuestionChangeCustom implements QuestionChangeInterface
{
    private string $text;
    /**
     * @var Answer[]
     */
    private array $answers;

    public function __construct(string $text, Answer ...$answers)
    {
        $this->text = $text;
        $this->answers = $answers;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getAnswers(): array
    {
        return $this->answers;
    }
}
