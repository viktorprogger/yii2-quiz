<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\poll;

use app\modules\poll\domain\exceptions\DomainDataCorruptionException;

final class QuestionChangeRated implements QuestionChangeInterface
{
    private string $text;
    /**
     * @var Answer[]
     */
    private array $answers;

    /**
     * @param string $text Question text. E.g. "Do you like our service?"
     * @param int $maximum The maximum grade available for the question. 10 means there will be 10 answers starting from "1"
     * @param int $dontCommentSince Low-rated answers can be commented. Commenting is not available for rating from this value and above
     */
    public function __construct(string $text, int $maximum, int $dontCommentSince)
    {
        $this->validate($text, $maximum);

        $answers = [];
        for ($i = 1; $i <= $maximum; $i++) {
            $answers[] = new AnswerChange($i, (string) $i, $i < $dontCommentSince);
        }

        $this->text = $text;
        $this->answers = $answers;
    }

    private function validate(string $text, int $maximum): void
    {
        if (mb_strlen($text) < 5) {
            throw new DomainDataCorruptionException(
                "Question text must be a string of 5 characters or more, given '$text'"
            );
        }
        if ($maximum < 1) {
            throw new DomainDataCorruptionException("Maximum rating must be a positive integer, given $maximum");
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
