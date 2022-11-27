<?php

namespace Framework;

class Response {

    const OK           = 200;
    const NOT_FOUND    = 404;
    CONST SERVER_ERROR = 501;

    public static function json($data = [], $code = self::OK)
    {
        http_response_code($code);
        header('content-type:application/json');
        return json_encode($data);
    }
}