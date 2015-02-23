<?php
namespace Nmullen\ApiEngine\Test\Http;

class MessageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var MessageImp
     */
    private $message;

    public function setUp()
    {
        $this->message = new MessageImp();
    }

    public function testProtocolReturnsDefault()
    {
        $this->assertNotNull($this->message->getProtocolVersion());
    }

    public function testWithProtocolIsImmutable()
    {
        $protocol = 1.1;
        $this->assertNotSame($protocol, $this->message->getProtocolVersion());
        $new = $this->message->withProtocolVersion(1.1);
        $this->assertNotSame($this->message, $new);
        $this->assertEquals($new->getProtocolVersion(), 1.1);
    }

    public function testHeadersReturnsEmpty()
    {
        $this->assertTrue(is_array($this->message->getHeaders()));
        $this->assertCount(0, $this->message->getHeaders());
    }

    public function testWithHeaderSetsHeader()
    {
        $message = $this->message->withHeader('test', 'positive');
        $this->assertNotSame($message, $this->message);
        $this->assertSame('positive', $message->getHeader('test'));
    }

    public function testHeaderSetMaintainsCase()
    {
        $message = $this->message->withHeader('ThisIsMixed', 'PosiTive');
        $this->assertArrayHasKey('ThisIsMixed', $message->getHeaders());
    }

    public function testHeadWithAddedHeader()
    {
        $message = $this->message->withHeader('test', 'positive');
        $message = $message->withAddedHeader('test', 'negative');
        $this->assertArrayHasKey('test', $message->getHeaders());
        $this->assertSame('positive,negative', $message->getHeader('test'));
    }

    public function testWithHeaderRemoved()
    {
        $message = $this->message->withHeader('test', 'positive');
        $message = $message->withAddedHeader('test', 'negative');
        $this->assertArrayHasKey('test', $message->getHeaders());
        $this->assertSame('positive,negative', $message->getHeader('test'));
        $new = $message->withoutHeader('test');
        $this->assertNotSame($message, $new);
        $this->assertCount(0, $new->getHeaders());
    }

    public function testWithBody()
    {
        $stream = $this->getMock('Psr\\Http\\Message\\StreamableInterface');
        $message = $this->message->withBody($stream);
        $this->assertNotSame($message, $this->message);
        $this->assertNull($this->message->getBody());
        $this->assertInstanceOf('Psr\\Http\\Message\\StreamableInterface', $message->getBody());
    }

    public function testParseHeader()
    {
        $header = [
            'Transfer-Encoding' => 'chunked',
            'Date' => '24 March 1989',
            'Server' => 'Number1',
            'Connection' => 'close',
            'X-Powered-By' => 'ApiEngine',
            'Pragma' => 'public',
            'Expires' => '24 March 1989',
            'Etag' => 'pub1259380237;gz',
            'Cache-Control' => 'max-age=3600, public',
            'Content-Type' => 'text/html; charset=UTF-8',
            'Last-Modified' => '24 March 1989',
            'X-Pingback' => 'http://localhost',
            'Content-Encoding' => 'gzip',
            'Vary' => 'Accept-Encoding, Cookie, User-Agent',
        ];
        $this->message->parseHeadersForTest($header);
        $this->assertSame('chunked', $this->message->getHeader('transfer-encoding'));
        $this->assertSame('Accept-Encoding, Cookie, User-Agent', $this->message->getHeader('Vary'));
        $this->assertNotSame($header, $this->message->getHeaders());
    }
}