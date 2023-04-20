<?php

namespace App\Exceptions;

use App\Constants\HttpStatus;
use App\Traits\Json;
use Throwable;

class Exception extends \Exception
{
    use Json;

    protected $msg;

    public function __construct($message = "success", $code = HttpStatus::BAD_REQUEST, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->msg = $message;
    }
}
