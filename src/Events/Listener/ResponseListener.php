<?php

namespace Nmullen\ApiEngine\Events\Listener;

use Nmullen\ApiEngine\Http\Request;
use Nmullen\ApiEngine\Http\Uri;
use Psr\Http\Message\ResponseInterface;

class ResponseListener
{

    public function getEvents()
    {
        return ['postSend' => 1];
    }

    public function postSend(ResponseInterface $response)
    {
        if ($response->getStatusCode() > 300 && $response->getStatusCode() < 399 && $response->hasHeader('location')) {
            return new Request('GET', new Uri($response->getHeader('location')));
        }
        return $response;
    }
}