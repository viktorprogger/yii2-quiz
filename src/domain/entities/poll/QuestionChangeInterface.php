<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\poll;

interface QuestionChangeInterface
{
    public function getText(): string;

    /**
     * @return Answer[]
     */
    public function getAnswers(): array;
}
