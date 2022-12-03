<?php

declare(strict_types=1);

namespace app\modules\quiz\domain\entities;

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
        $answers = [];
        for ($i = 1; $i <= $maximum; $i++) {
            $answers[] = new AnswerChange($i, (string) $i, $i < $dontCommentSince);
        }

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
