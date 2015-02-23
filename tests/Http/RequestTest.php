<?php
namespace Nmullen\ApiEngine\Test\Http;

use Nmullen\ApiEngine\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Request
     */
    private $request;

    public function setUp()
    {
        $this->request = new Request();
    }

    public function testMethodIsNull()
    {
        $this->assertNull($this->request->getMethod());
    }

    public function testWithMethodIsImmutable()
    {
        $request = $this->request->withMethod('GET');
        $this->assertNotSame($this->request, $request);
    }

    public function testUriIsNull()
    {
        $this->assertNull($this->request->getUri());
    }

    /**
     * @dataProvider invalidMethodProvider
     */
    public function testConstructionInvalidMethod($method)
    {
        $this->setExpectedException('InvalidArgumentException');
        new Request($method);
    }

    /**
     * @dataProvider validMethodProvider
     */
    public function testConstructionValidMethods($method)
    {
        $this->assertInstanceOf('Nmullen\\ApiEngine\\Http\\Request', new Request($method));
    }

    public function invalidMethodProvider()
    {
        return [
            ['int' => 1],
            ['string' => 'NOTGOOD'],
            ['float' => 1.1],
            ['array' => ['test' => 'value']],
            ['object' => [(object)['body' => 'BODY']]]
        ];
    }

    public function validMethodProvider()
    {
        return [
            ['CONNECT'],
            ['DELETE'],
            ['GET'],
            ['HEAD'],
            ['OPTIONS'],
            ['PATCH'],
            ['POST'],
            ['PUT'],
            ['TRACE'],
        ];
    }


}
