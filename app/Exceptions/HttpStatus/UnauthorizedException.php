<?php

namespace App\Exceptions\HttpStatus;

use App\Constants\HttpStatus;
use App\Exceptions\Exception;
use Throwable;

class UnauthorizedException extends Exception
{
    public function __construct(string $message = null, int $http_code = HttpStatus::UNAUTHORIZED, Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = 'UNAUTHORIZED！';
        }

        parent::__construct($message, $http_code, $previous);
    }
}
