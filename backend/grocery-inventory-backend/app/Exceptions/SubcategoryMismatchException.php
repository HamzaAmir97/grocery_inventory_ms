<?php

namespace App\Exceptions;

use RuntimeException;

class SubcategoryMismatchException extends RuntimeException
{
    public function __construct(string $message = 'The selected subcategory does not belong to the selected category.')
    {
        parent::__construct($message);
    }
}
