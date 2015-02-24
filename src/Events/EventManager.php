<?php

namespace Nmullen\ApiEngine\Events;

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
        array_walk_recursive($this->listeners['preSend'], function ($key) use ($request) {
            if (is_object($key)) {
                $request = $key->preSend($request);
            }
            if ($request instanceof \Psr\Http\Message\ResponseInterface) {
                $this->response = $request;
                return true;
            }
        });
        return $request;
    }

    public function postSend($response)
    {
        foreach ($this->listeners['postSend'] as $level => $key) {
            foreach ($key as $event) {
                $response = $event->postSend($response);
                if ($response instanceof \Psr\Http\Message\RequestInterface) {
                    $this->logger->info('redirecting');
                    return $response;
                }
            }
        }
        return $response;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}