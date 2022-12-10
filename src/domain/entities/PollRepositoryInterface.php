<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities;

use app\modules\poll\domain\entities\clientAnswer\ClientAnswerChange;
use app\modules\poll\domain\entities\poll\Poll;
use app\modules\poll\domain\entities\poll\PollChange;
use app\modules\poll\domain\exceptions\DomainDataCorruptionException;
use app\modules\poll\domain\exceptions\EntityNotFoundException;

interface PollRepositoryInterface
{
    /**
     * @throws DomainDataCorruptionException
     */
    public function create(PollChange $poll): Poll;

    /**
     * @throws EntityNotFoundException
     */
    public function update(int $id, PollChange $poll): Poll;

    /**
     * @throws DomainDataCorruptionException
     */
    public function getActiveForUser(int $userId): ?Poll;

    public function addAnswer(ClientAnswerChange $answer): void;

    public function addRejection(int $pollId, int $userId, $licenseId): void;
}
