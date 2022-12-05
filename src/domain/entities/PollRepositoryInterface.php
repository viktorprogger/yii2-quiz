<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities;

use app\modules\poll\domain\entities\clientAnswer\ClientAnswer;
use app\modules\poll\domain\entities\clientAnswer\ClientAnswerChange;
use app\modules\poll\domain\entities\poll\Poll;
use app\modules\poll\domain\entities\poll\PollChange;

interface PollRepositoryInterface
{
    public function create(PollChange $poll): Poll;

    public function update(int $id, PollChange $poll): Poll;

    public function getActiveForUser(int $userId): ?Poll;

    public function addAnswer(ClientAnswerChange $answer): ClientAnswer;

    public function addRejection(int $pollId, int $getId, $getLicenseId): void;
}
