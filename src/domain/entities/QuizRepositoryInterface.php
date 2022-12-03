<?php

declare(strict_types=1);

namespace app\modules\quiz\domain\entities;

interface QuizRepositoryInterface
{
    public function create(QuizChange $quiz): Quiz;

    public function update(int $id, QuizChange $quiz): Quiz;

    public function getActiveForUser(int $userId): ?Quiz;
}
