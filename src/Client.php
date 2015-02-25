<?php
namespace Nmullen\ApiEngine;

use Nmullen\ApiEngine\Driver\CurlDriver;
use Nmullen\ApiEngine\Events\EventManager;
use Nmullen\ApiEngine\Events\Listener\LogListener;
use Nmullen\ApiEngine\Events\Listener\RedirectListener;
use Nmullen\ApiEngine\Http\Request;
use Nmullen\ApiEngine\Http\Stream;
use Nmullen\ApiEngine\Http\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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
     * @var CurlDriver
     */
    private $driver;
    /**
     * @var Uri
     */
    private $base_path;

    private $events;

    public function __construct($options = [])
    {
        $stream = new \Monolog\Handler\StreamHandler('/tmp/monolog.txt', \Monolog\Logger::DEBUG);
        $logger = new \Monolog\Logger('test', [$stream]);

        $this->options = array_merge($this->options, $options);
        $this->driver = new CurlDriver($this->getOption('curl'));
        $this->base_path = new Uri($this->getOption('base_path'));
        $this->events = new EventManager();
        $this->events->setLogger($logger);
        $this->events->addListener(new LogListener());
        $this->events->addListener(new RedirectListener());
    }

    /**
     * @param $key string Any key stored in the options array.
     * @return mixed|null returns the value of a key or null if it does not exist
     */
    public function getOption($key)
    {
        return array_key_exists($key, $this->options) ? $this->options[$key] : null;
    }

    /**
     * Creates and sends a Request via the 'GET' method.
     *
     * @param $path string Appended to the base_path to form the request url.
     * @param array $parameters Values appended to the request url as a query string.
     * @param array $headers Headers to send with the request.
     * @return Http\Response
     */
    public function get($path, $parameters = [], $headers = [])
    {
        $url = $this->base_path->withPath($path);
        $url = $url->withQuery(http_build_query($parameters));
        return $this->send(new Request('GET', $url, $headers));
    }

    public function send(RequestInterface $request)
    {
        $response = null;

        $request = $this->events->preSend($request);

        if ($request instanceof Request) {
            $response = $this->driver->send($request);
        } elseif ($request instanceof ResponseInterface) {
            $response = $request;
        }

        if ($response instanceof ResponseInterface) {
            $response = $this->events->postSend($response);
        }

        if ($response instanceof RequestInterface) {
            return $this->send($response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        }
    }

    public function post($path, $parameters = [], $headers = [])
    {
        $url = $this->base_path->withPath($path);
        $stream = new Stream('php://memory');
        $stream->write(http_build_query($parameters));
        $request = new Request('POST', $url, $headers, $stream);
        return $this->send($request);
    }
}