<?php
namespace Nmullen\ApiEngine;

use Nmullen\ApiEngine\Driver\CurlDriver;
use Nmullen\ApiEngine\Http\Request;
use Nmullen\ApiEngine\Http\Uri;
use Psr\Http\Message\RequestInterface;

class Client
{

    /**
     * @var array
     */
    protected $options = [
        'base_path' => '',
        'curl' => ['timeout' => 2, 'connection' => 5],
    ];
    /**
     * @var Driver\CurlDriver
     */
    private $driver;
    /**
     * @var Uri
     */
    private $basepath;

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, $options);
        $this->driver = new CurlDriver($this->getOption('curl'));
        $this->basepath = new Uri($this->getOption('base_path'));
    }

    public function getOption($key)
    {
        return array_key_exists($key, $this->options) ? $this->options[$key] : null;
    }

    public function get($path, $parameters = [], $headers = [])
    {
        $url = $this->basepath->withPath($path);
        $url = $url->withQuery(http_build_query($parameters));
        return $this->send(new Request('GET', $url, $headers));
    }

    public function send(RequestInterface $request)
    {
        return $this->driver->send($request);
    }
}