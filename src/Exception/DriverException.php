<?php

namespace Nmullen\ApiEngine\Exception;

class DriverException extends \Exception implements ApiEngineException
{
    public static function error($error)
    {
        return new self($error);
    }

    public static function curlError($error, $num)
    {
        return new self($error, $num);
    }
}