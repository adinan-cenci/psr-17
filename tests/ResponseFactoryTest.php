<?php

namespace AdinanCenci\Psr17\Tests;

use AdinanCenci\Psr17\ResponseFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ResponseFactoryTest extends TestCase
{
    protected function getFactory()
    {
        return new ResponseFactory();
    }

    public function testCreateResponse()
    {
        $response = $this->getFactory()->createResponse(200, 'foo bar');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
