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
        $this->client = new Client(['base_path' => 'http://requestb.in']);
    }

    public function testGet()
    {
        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response = $this->client->get('1d1u1dh1'));
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testPost()
    {
        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface',
            $response = $this->client->post('1d1u1dh1', ['fizz' => 'buzz']));
        $this->assertSame(200, $response->getStatusCode());
    }
}
