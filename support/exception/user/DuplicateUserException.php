<?php

namespace support\exception\user;

use Throwable;

class DuplicateUserException extends \PDOException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}