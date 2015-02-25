<?php

namespace Nmullen\ApiEngine\Driver;

use Nmullen\ApiEngine\Exception\DriverException;
use Nmullen\Http\Response;
use Nmullen\Http\Stream;
use Psr\Http\Message\RequestInterface as Request;

class CurlDriver
{

    private $curl;

    private $options;

    private $headers = [];

    private $body;

    /**
     * The Curl Driver.
     * accepts options:
     * stream : The location response bodies should be streamed from, by default this is php://memory
     * timeout: The time curl should wait for a response from the remote server.
     * connection: The time curl should wait to close the connection.
     * @param array $options
     */
    public function __construct($options = [])
    {
        $options['stream'] = array_key_exists('stream', $options) ? $options['stream'] : 'php://temp';
        $options['timeout'] = array_key_exists('timeout', $options) ? $options['timeout'] : 5;
        $options['connection'] = array_key_exists('connection', $options) ? $options['connection'] : 10;
        $this->options = $options;
    }

    /**
     * Takes a Request object connects to the specified Url and returns the result.
     * @param \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws DriverException
     */
    public function send(Request $request)
    {
        $this->body = new Stream($this->options['stream']);
        $this->headers = [];
        $this->initCurl();
        $this->prepareCurl($request);
        $response = $this->processResponse($this->executeCurl());
        $this->closeCurl();
        return $response;
    }

    /**
     * Opens a curl resource.
     * @throws DriverException If curl does not start most likely cause is curl not being provided.
     */
    private function initCurl()
    {
        if (!is_resource($this->curl) && false === $this->curl = curl_init()) {
            throw DriverException::error('Unable to initialise curl, check curl is installed');
        }
    }

    /**
     * Sets curl up to handle the request.
     * @param \Psr\Http\Message\RequestInterface $request
     */
    private function prepareCurl(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, (string)$request->getBody());
            if ($request->hasHeader('content-type')) {
                $request = $request->withoutHeader('content-type');
            }
        }
        $header = [];
        foreach ($request->getHeaders() as $key => $value) {
            $header[$key] = implode(', ', $value);
        }
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($this->curl, CURLOPT_URL, $request->getUri()->getAuthority() . $request->getRequestTarget());
        curl_setopt($this->curl, CURLOPT_HTTP_VERSION, $request->getProtocolVersion());
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->options['timeout']);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->options['connection']);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, [$this, 'headerCallback']);
        curl_setopt($this->curl, CURLOPT_WRITEFUNCTION, [$this, 'bodyCallback']);
    }

    /**
     * Creates the Response object
     * @return \Psr\Http\Message\ResponseInterface
     * @throws DriverException if an error occurs during the request.
     */
    protected function processResponse()
    {
        if (curl_error($this->curl)) {
            throw DriverException::curlError(curl_error($this->curl), curl_errno($this->curl));
        }
        return new Response(curl_getinfo($this->curl, CURLINFO_HTTP_CODE), $this->headers, $this->body);
    }

    /**
     * @return boolean true|false returns true if curl starts, false if it doesn't.
     */
    private function executeCurl()
    {
        return curl_exec($this->curl);
    }

    /**
     * Closes the curl resource if it exists.
     */
    private function closeCurl()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }

    /**
     * ensure curl is closed
     */
    public function __destruct()
    {
        $this->closeCurl();
    }

    /**
     * used by CURLOPT_HEADERFUNCTION to set the headers.
     * @param $curl object not used.
     * @param $headerLine
     * @return int the length of the headerline
     */
    private function headerCallback($curl, $headerLine)
    {
        preg_match_all('/(.*)\:\s(.*)/', $headerLine, $match);
        $header = array_combine($match[1], $match[2]);
        $this->headers = array_merge($this->headers, $header);
        return strlen($headerLine);
    }

    /**
     * used by CURLOPT_WRITEFUNCTION to write to the body.
     * @param $curl
     * @param $body
     * @return int
     */
    private function bodyCallback($curl, $body)
    {
        $this->body->write($body);
        return strlen($body);
    }
}