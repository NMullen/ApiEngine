<?php

namespace Nmullen\ApiEngine\Exception;

class InvalidArgumentException extends \InvalidArgumentException implements ApiEngineException
{

    public static function InvalidHttpMethodProvided($given)
    {
        $given = (is_string($given) ? $given : self::getType($given));
        return new self(sprintf('%s is not a valid HTTP method', $given));
    }

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

    public static function InvalidType($name, $expected, $given)
    {
        return new self(sprintf(
            'Invalid Argument supplied for %s, expected %s was given %s',
            $name,
            $expected,
            self::getType($given)
        ));
    }

    public static function outOfRange($name, $range, $given)
    {
        return new self(sprintf(
            'The value %s is not within the valid range (%s) for %s',
            $given,
            $range,
            $name
        ));
    }

    public static function ValueNotFound($name, $value)
    {
        return new self(sprintf('no value found for key %s matching %s', $name, $value));
    }

    public static function InvalidUrl($url)
    {
        return new self(sprintf('the url %s is invalid', $url));
    }
}