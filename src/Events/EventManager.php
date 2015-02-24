<?php

namespace Nmullen\ApiEngine\Events;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class EventManager implements LoggerAwareInterface
{

    private $logger;

    private $listeners;

    private $response;

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function addListener($listener)
    {
        array_walk($listener->getEvents(), function ($value, $key) use ($listener) {
            $this->listeners[$key][$value][] = $listener;
            if ($listener instanceof LoggerAwareInterface) {
                $listener->setLogger($this->logger);
            }
        });
    }

    public function preSend($request)
    {
        array_walk_recursive($this->listeners['preSend'], function ($key) use (& $request) {
            if (is_object($key) && $request instanceof RequestInterface) {
                $request = $key->preSend($request);
            }
            if ($request instanceof \Psr\Http\Message\ResponseInterface) {
                return true;
            }
        });
        return $request;
    }

    public function postSend($response)
    {
        array_walk_recursive($this->listeners['postSend'], function ($key) use (&$response) {
            if (is_object($key) && $response instanceof ResponseInterface) {
                $response = $key->postSend($response);
            }
        });
        return $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}