<?php

namespace App\Exceptions\HttpStatus;

use App\Constants\HttpStatus;
use App\Exceptions\Exception;
use Throwable;

class ForbiddenException extends Exception
{
    public function __construct(string $message = null, int $http_code = HttpStatus::FORBIDDEN, Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = 'Forbidden！';
        }

        parent::__construct($message, $http_code, $previous);
    }
}
