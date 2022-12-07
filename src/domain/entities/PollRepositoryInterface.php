<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities;

use app\modules\poll\domain\entities\clientAnswer\ClientAnswerChange;
use app\modules\poll\domain\entities\exceptions\DomainDataCorruptionException;
use app\modules\poll\domain\entities\exceptions\EntityNotFoundException;
use app\modules\poll\domain\entities\poll\Poll;
use app\modules\poll\domain\entities\poll\PollChange;

interface PollRepositoryInterface
{
    /**
     * @throws DomainDataCorruptionException
     */
    public function create(PollChange $poll): void;

    /**
     * @throws EntityNotFoundException
     */
    public function update(int $id, PollChange $poll): void;

    /**
     * @throws DomainDataCorruptionException
     */
    public function getActiveForUser(int $userId): ?Poll;

    public function addAnswer(ClientAnswerChange $answer): void;

    public function addRejection(int $pollId, int $userId, $licenseId): void;
}
