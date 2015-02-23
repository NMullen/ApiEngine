<?php
namespace Nmullen\ApiEngine\Test;

use Nmullen\ApiEngine\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        $this->client = new Client(['basepath' => 'http://httpbin.org/']);
    }

    public function testGet()
    {
        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response = $this->client->get('/get'));
    }

}
