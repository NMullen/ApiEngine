<?php

namespace Nmullen\ApiEngine\Events\Listener;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class LogListener implements LoggerAwareInterface
{

    private $logger;

    public function getEvents()
    {
        return ['preSend' => 0, 'postSend' => 0, 'postException' => '0'];
    }

    public function preSend(RequestInterface $request)
    {
        $this->logger->info(sprintf('Request : %s %s', $request->getMethod(), $request->getUri()));
        return $request;
    }

    public function postSend(ResponseInterface $response)
    {
        $this->logger->info(sprintf('Response : %s, %s', $response->getStatusCode(), $response->getReasonPhrase()));
        return $response;
    }

    public function postException($exception)
    {
        $this->logger->addError($exception->getMessage(), $exception->getTrace());
        return $exception;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}