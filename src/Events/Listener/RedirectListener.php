<?php

namespace Nmullen\ApiEngine\Events\Listener;

use Nmullen\Http\Request;
use Nmullen\Http\Uri;
use Psr\Http\Message\ResponseInterface;

class RedirectListener
{

    public function getEvents()
    {
        return ['postSend' => 10];
    }

    public function postSend(ResponseInterface $response)
    {
        if ($response->getStatusCode() > 300 && $response->getStatusCode() < 399 && $response->hasHeader('location')) {
            return new Request('GET', new Uri($response->getHeader('Location')));
        }
        return $response;
    }
}