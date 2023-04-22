<?php

namespace App\Exceptions;

use App\Constants\HttpStatus;
use Illuminate\Http\Request;

class AuthException extends Exception
{
    protected $user_id = 0;

    public function __construct(string $message = null, int $http_code = HttpStatus::UNAUTHORIZED, $user_id = 0)
    {
        parent::__construct($message, $http_code);
        $this->user_id = $user_id;
    }

    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            // 登录日志
            // UserLoginLog::getInstance()->add($this->user_id, 0, $this->msg);

            $this->setHttpCode(401);
            return $this->errorJson($this->msg);
        }
    }
}
