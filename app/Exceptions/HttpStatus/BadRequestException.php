<?php

namespace App\Exceptions\HttpStatus;

use App\Constants\HttpStatus;
use App\Exceptions\Exception;
use Throwable;

class BadRequestException extends Exception
{
    public function __construct(string $message = null, int $http_code = HttpStatus::BAD_REQUEST, Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = 'Bad Request！';
        }

        parent::__construct($message, $http_code, $previous);
    }
}
