<?php

namespace AdinanCenci\Psr17\Tests;

use AdinanCenci\Psr17\StreamFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class StreamFactoryTest extends TestCase
{
    protected function getFactory()
    {
        return new StreamFactory();
    }

    public function testCreateStream()
    {
        $stream = $this->getFactory()->createStream('');

        $this->assertInstanceOf(StreamInterface::class, $stream);
    }

    public function testCreateStreamFromFile()
    {
        $stream = $this->getFactory()->createStreamFromFile('./tests/files/testCreateStreamFromFile.txt', 'r+');

        $this->assertInstanceOf(StreamInterface::class, $stream);
    }

    public function testCreateStreamFromResource()
    {
        $resource = fopen('./tests/files/testCreateStreamFromFile.txt', 'r+');
        $stream = $this->getFactory()->createStreamFromResource($resource);

        $this->assertInstanceOf(StreamInterface::class, $stream);
    }
}
