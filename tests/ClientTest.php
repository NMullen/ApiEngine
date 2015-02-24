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
        $this->client = new Client(['base_path' => 'http://google.com']);
    }

    public function testGet()
    {
        $this->assertTrue(true);
        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response = $this->client->get('/'));
        //    $this->assertSame(200, $response->getStatusCode());
    }

    public function testPost()
    {
        $this->assertTrue(true);
        //$this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface',
        //$response = $this->client->post('1d1u1dh1', ['fizz' => 'buzz', 'buzz' => 'does fizz']));
        //$this->assertSame(200, $response->getStatusCode());
    }
}
