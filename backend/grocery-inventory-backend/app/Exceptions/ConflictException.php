<?php

namespace App\Exceptions;

use RuntimeException;

class ConflictException extends RuntimeException
{
    public function __construct(string $message = 'The resource was modified by someone else. Reload and try again.')
    {
        parent::__construct($message);
    }
}
