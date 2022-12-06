<?php

declare(strict_types=1);

namespace app\modules\poll\domain\entities\exceptions;

use BadFunctionCallException;

final class NotImplementedException extends BadFunctionCallException
{
    protected $message = 'This method is not implemented yet';
}
