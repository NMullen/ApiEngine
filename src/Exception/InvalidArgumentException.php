<?php

namespace Nmullen\ApiEngine\Exception;

class InvalidArgumentException extends \InvalidArgumentException implements ApiEngineException
{
    public static function getType($given)
    {
        return (is_object($given) ? get_class($given) : gettype($given));
    }

    public static function InvalidStreamProvided($given)
    {
        return new self(sprintf(
            'Invalid stream provided, expected stream identifier or resource, given %s',
            self::getType($given)
        ));
    }
}