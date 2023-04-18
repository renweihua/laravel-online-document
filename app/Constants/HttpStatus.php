<?php

namespace App\Constants;

class HttpStatus
{
    /**
     * @Message("success！")
     */
    public const SUCCESS = 200;

    /**
     * @Message("Bad Request！")
     */
    public const BAD_REQUEST = 400;

    /**
     * @Message("UNAUTHORIZED！")
     */
    public const UNAUTHORIZED = 401;

    /**
     * @Message("Forbidden！")
     */
    public const FORBIDDEN = 403;

    /**
     * @Message("Server Error！")
     */
    public const SERVER_ERROR = 500;
}
