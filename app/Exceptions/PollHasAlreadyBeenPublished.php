<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class PollHasAlreadyBeenPublished extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (!$message) {
            $message = 'The Poll has been already published';
        }

        parent::__construct($message, $code, $previous);
    }
}
