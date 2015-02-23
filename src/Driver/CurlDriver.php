<?php

namespace Nmullen\ApiEngine\Driver;

use Nmullen\ApiEngine\Exception\DriverException;
use Nmullen\ApiEngine\Http\Response;
use Nmullen\ApiEngine\Http\Stream;
use Psr\Http\Message\RequestInterface as Request;

class CurlDriver
{

    private $curl;

    private $options;

    public function __construct($options = [])
    {
        $options['timeout'] = array_key_exists('timeout', $options) ? $options['timeout'] : 5;
        $options['connection'] = array_key_exists('connection', $options) ? $options['connection'] : 10;
        $this->options = $options;
    }

    public function send(Request $request)
    {
        $this->initCurl();
        $this->prepareCurl($request);
        $response = $this->processResponse($this->executeCurl());
        $this->closeCurl();
        return $response;
    }

    private function initCurl()
    {
        if (!is_resource($this->curl) && false === $this->curl = curl_init()) {
            throw DriverException::error('Unable to initialise Curl');
        }
    }

    public function prepareCurl(Request $request)
    {
        $curl = $this->curl;
        $header = [];
        foreach ($request->getHeaders() as $key => $value) {
            $header[$key] = implode(', ', $value);
        }
        curl_setopt($curl, CURLOPT_URL, $request->getRequestTarget());
        curl_setopt($curl, CURLOPT_HTTP_VERSION, $request->getProtocolVersion());
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->options['timeout']);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->options['connection']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        switch ($request->getMethod()) {
            case 'GET':
                curl_setopt($curl, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, (string)$request->getBody());
                break;
        }
    }

    private function processResponse($info)
    {
        if (!$info) {
            throw DriverException::curlError(curl_error($this->curl), curl_errno($this->curl));
        }
        $pos = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
        $status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $headers = $this->processHead(substr($info, 0, $pos));
        $body = new Stream(tempnam(sys_get_temp_dir(), 'apiEngine'), 'rb+');
        $body->write(substr($info, $pos));
        return new Response($status, $headers, $body);
    }

    private function processHead($head)
    {
        preg_match_all('/(.*)\:\s(.*)/', $head, $match);
        return array_combine($match[1], $match[2]);
    }

    private function executeCurl()
    {
        return curl_exec($this->curl);
    }

    private function closeCurl()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }

    public function __destruct()
    {
        $this->closeCurl();
    }
}