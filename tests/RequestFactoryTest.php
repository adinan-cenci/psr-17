<?php

namespace AdinanCenci\Psr17\Tests;

use AdinanCenci\Psr17\RequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class RequestFactoryTest extends TestCase
{
    protected function getFactory()
    {
        return new RequestFactory();
    }

    public function testCreateRequest()
    {
        $request = $this->getFactory()->createRequest('GET', 'http://foo-bar.com');

        $this->assertInstanceOf(RequestInterface::class, $request);
    }
}
