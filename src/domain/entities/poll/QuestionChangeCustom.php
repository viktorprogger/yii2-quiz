<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\poll;

use app\modules\poll\domain\exceptions\DomainDataCorruptionException;

final class QuestionChangeCustom implements QuestionChangeInterface
{
    private string $text;
    /**
     * @var AnswerChange[]
     */
    private array $answers;

    public function __construct(string $text, AnswerChange ...$answers)
    {
        $this->validate($text, $answers);

        $this->text = $text;
        $this->answers = $answers;
    }

    private function validate(string $text, array $answers): void
    {
        if ($text === '') {
            throw new DomainDataCorruptionException("Question text must be a non-empty string");
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
