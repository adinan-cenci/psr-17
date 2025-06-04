<?php

namespace AdinanCenci\Psr17\Tests;

use AdinanCenci\Psr17\UriFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriFactoryTest extends TestCase
{
    protected function getFactory()
    {
        return new UriFactory();
    }

    public function testCreateUri()
    {
        $uri = $this->getFactory()->createUri('https://foo-bar.com/path/file.txt?query=random#h1');

        $this->assertInstanceOf(UriInterface::class, $uri);
    }
}
