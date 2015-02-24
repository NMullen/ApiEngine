<?php

namespace Nmullen\ApiEngine\Events\Listener;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class LogListener implements LoggerAwareInterface
{

    /**
     * @var LoggerInterface
     */
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

    public function postException(Exception $exception)
    {
        $this->logger->error($exception->getMessage(), $exception->getTrace());
        return $exception;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}