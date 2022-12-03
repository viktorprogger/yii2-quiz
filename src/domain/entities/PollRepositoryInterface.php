<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities;

use app\modules\poll\domain\entities\poll\Poll;
use app\modules\poll\domain\entities\poll\PollChange;

interface PollRepositoryInterface
{
    public function create(PollChange $poll): Poll;

    public function update(int $id, PollChange $poll): Poll;

    public function getActiveForUser(int $userId): ?Poll;
}
