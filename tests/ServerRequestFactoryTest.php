<?php
namespace AdinanCenci\Psr17\Tests;

use AdinanCenci\Psr17\ServerRequestFactory;
use PHPUnit\Framework\TestCase;

use Psr\Http\Message\ServerRequestInterface;

class ServerRequestFactoryTest extends TestCase 
{
    protected function getFactory() 
    {
        return new ServerRequestFactory();
    }

    public function testCreateServerRequest() 
    {
        $serverRequest = $this->getFactory()->createServerRequest('POST', 'http://foo-bar.com', []);

        $this->assertInstanceOf(ServerRequestInterface::class, $serverRequest);
    }
}
