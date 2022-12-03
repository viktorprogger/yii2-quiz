<?php

declare(strict_types=1);

use app\modules\poll\domain\entities\PollRepositoryInterface;

return [
    'container' => [
        'singletons' => [
            PollRepositoryInterface::class => '',
        ],
    ],
];
