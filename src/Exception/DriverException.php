<?php

namespace Nmullen\ApiEngine\Exception;

class DriverException extends \Exception implements ApiEngineException
{

    public static function error($error)
    {
        return new self($error);
    }
}