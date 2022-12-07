<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\poll;

use app\modules\poll\domain\entities\exceptions\DomainDataCorruptionException;

final class QuestionChangeCustom implements QuestionChangeInterface
{
    private string $text;
    /**
     * @var Answer[]
     */
    private array $answers;

    public function __construct(string $text, Answer ...$answers)
    {
        $this->validate($text, $answers);

        $this->text = $text;
        $this->answers = $answers;
    }

    private function validate(string $text, array $answers): void
    {
        if (mb_strlen($text) < 5) {
            throw new DomainDataCorruptionException("Question text must be a string of 5 characters or more, given '$text'");
        }

        if (count($answers) < 2) {
            throw new DomainDataCorruptionException("Question must have at least two answers");
        }
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
