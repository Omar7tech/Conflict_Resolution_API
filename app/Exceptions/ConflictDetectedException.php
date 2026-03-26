<?php

namespace App\Exceptions;

use Exception;

class ConflictDetectedException extends Exception
{
     public $details;

    public function __construct(array $details = [], $message = "Conflict detected", $code = 409)
    {
        parent::__construct($message, $code);
        $this->details = $details;
    }
}
