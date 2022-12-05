<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\poll;

use app\modules\poll\domain\entities\exceptions\DomainDataCorruptionException;

final class AnswerChange
{
    private int $sort;
    private string $text;
    private bool $canBeCommented;

    public function __construct(int $sort, string $text, bool $canBeCommented)
    {
        $this->validate($text);

        $this->sort = $sort;
        $this->text = $text;
        $this->canBeCommented = $canBeCommented;
    }

    private function validate(string $text): void
    {
        if (mb_strlen($text) < 5) {
            throw new DomainDataCorruptionException("Answer text must be a non-empty string");
        }
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
