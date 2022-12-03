<?php

declare(strict_types=1);

namespace app\modules\quiz\domain\entities;

interface QuestionChangeInterface
{
    public function getText(): string;

    /**
     * @return Answer[]
     */
    public function getAnswers(): array;
}
