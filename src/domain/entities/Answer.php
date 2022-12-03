<?php

declare(strict_types=1);

namespace app\modules\quiz\domain\entities;

use JsonSerializable;

final class Answer implements JsonSerializable
{
    private int $id;
    private int $sort;
    private string $text;
    private bool $canBeCommented;

    public function __construct(int $id, int $sort, string $text, bool $canBeCommented)
    {
        $this->id = $id;
        $this->sort = $sort;
        $this->text = $text;
        $this->canBeCommented = $canBeCommented;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return bool
     */
    public function canBeCommented(): bool
    {
        return $this->canBeCommented;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'sort' => $this->sort,
            'text' => $this->text,
            'canBeCommented' => $this->canBeCommented,
        ];
    }
}
