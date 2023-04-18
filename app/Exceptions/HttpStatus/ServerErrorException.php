<?php

namespace App\Exceptions\HttpStatus;

use App\Constants\HttpStatus;
use App\Exceptions\Exception;
use Throwable;

class ServerErrorException extends Exception
{
    public function __construct(string $message = null, int $http_code = HttpStatus::SERVER_ERROR, Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = 'Server Error！';
        }

        parent::__construct($message, $http_code, $previous);
    }
}
