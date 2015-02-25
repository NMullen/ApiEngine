<?php

class CurlDriverTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Nmullen\ApiEngine\Driver\CurlDriver
     */
    private $curl;
    /**
     * @var \Nmullen\ApiEngine\Http\Request
     */
    private $request;

    public function setUp()
    {
        $this->curl = new \Nmullen\ApiEngine\Driver\CurlDriver();
    }

    public function testAssertTrue()
    {
        $this->assertTrue(true);
    }
    /*
    public function testGet()
    {
        $uri = new \Nmullen\ApiEngine\Http\Uri('http://httpbin.org/html');
        $request = new \Nmullen\ApiEngine\Http\Request('GET', $uri);
        $response = $this->curl->send($request);
        $this->assertSame(200, $response->getStatusCode());
        $body = $response->getBody()->getContents();
        $this->assertStringStartsWith('<!DOCTYPE html>', $body);
    }

    public function testPost()
    {
        $uri = new \Nmullen\ApiEngine\Http\Uri('http://posttestserver.com/post.php');
        $request = new \Nmullen\ApiEngine\Http\Request('POST', $uri);
        $request->getBody()->write('fizz=buzz');
        $body = $request->getBody()->getContents();
        $this->assertSame('fizz=buzz', $body);
        $response = $this->curl->send($request);
        $this->assertSame(200, $response->getStatusCode());
    }
    //*/
}
