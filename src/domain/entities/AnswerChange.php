<?php

declare(strict_types=1);

namespace app\modules\quiz\domain\entities;

final class AnswerChange
{
    private int $sort;
    private string $text;
    private bool $canBeCommented;

    public function __construct(int $sort, string $text, bool $canBeCommented)
    {
        $this->sort = $sort;
        $this->text = $text;
        $this->canBeCommented = $canBeCommented;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function canBeCommented(): bool
    {
        return $this->canBeCommented;
    }
}
