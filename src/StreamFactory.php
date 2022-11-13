<?php
namespace AdinanCenci\Psr17;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\StreamFactoryInterface;

use AdinanCenci\Psr7\Stream;

class StreamFactory implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface 
    {
        $resource = fopen('php://memory', 'r+');
        $stream = $this->createStreamFromResource($resource);
        $stream->write($content);

        return $stream;
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface 
    {
        $resource = fopen($filename, $mode);
        return $this->createStreamFromResource($resource);
    }

    public function createStreamFromResource($resource): StreamInterface 
    {
        return new Stream($resource);
    }
}
