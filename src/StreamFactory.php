<?php

namespace AdinanCenci\Psr17;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\StreamFactoryInterface;
use AdinanCenci\Psr7\Stream;

class StreamFactory implements StreamFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createStream(string $content = ''): StreamInterface
    {
        $resource = fopen('php://memory', 'r+');
        $stream = $this->createStreamFromResource($resource);
        $stream->write($content);

        return $stream;
    }

    /**
     * Creates a new stream from a file in the file system.
     *
     * @param string $filename
     *   Path to the file, an obsolute one preferably.
     * @param string $mode
     *   The mode to open the file.
     *
     * @return Psr\Http\Message\StreamInterface
     *   The new stream object.
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        $resource = fopen($filename, $mode);
        return $this->createStreamFromResource($resource);
    }

    /**
     * Creates a new stream from a resource.
     *
     * @param resource $resource
     *   A resource.
     *
     * @return Psr\Http\Message\StreamInterface
     *   The new stream object.
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
