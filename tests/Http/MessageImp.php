<?php

namespace Nmullen\ApiEngine\Test\Http;

use Nmullen\ApiEngine\Http\Message;

class MessageImp
{
    use Message;

    public function parseHeadersForTest($headers)
    {
        $this->headers = $this->parseHeaders($headers);
    }
}