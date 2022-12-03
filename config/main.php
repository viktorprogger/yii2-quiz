<?php

declare(strict_types=1);

use app\modules\quiz\domain\entities\QuizRepositoryInterface;

return [
    'container' => [
        'singletons' => [
            QuizRepositoryInterface::class => '',
        ],
    ],
];
