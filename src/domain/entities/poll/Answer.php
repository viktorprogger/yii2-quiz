<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\poll;

use app\modules\poll\domain\exceptions\DomainDataCorruptionException;
use JsonSerializable;

final class Answer implements JsonSerializable
{
    private int $id;
    private int $sort;
    private string $text;
    private bool $canBeCommented;

    public function __construct(int $id, int $sort, string $text, bool $canBeCommented)
    {
        $this->validate($id, $text);

        $this->id = $id;
        $this->sort = $sort;
        $this->text = $text;
        $this->canBeCommented = $canBeCommented;
    }

    private function validate(int $id, string $text): void
    {
        if ($id < 1) {
            throw new DomainDataCorruptionException("Entity ID must be a positive integer, given '$id'");
        }
        if ($text === '') {
            throw new DomainDataCorruptionException("Answer text must be a non-empty string");
        }
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
