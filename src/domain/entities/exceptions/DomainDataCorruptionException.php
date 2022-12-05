<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\exceptions;

use InvalidArgumentException;

final class DomainDataCorruptionException extends InvalidArgumentException
{
}
