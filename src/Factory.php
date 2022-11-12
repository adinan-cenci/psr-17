<?php
namespace AdinanCenci\Psr17;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

use AdinanCenci\Psr7\Request;
use AdinanCenci\Psr7\ServerRequest;
use AdinanCenci\Psr7\Response;
use AdinanCenci\Psr7\Stream;
use AdinanCenci\Psr7\Uri;
use AdinanCenci\Psr7\UploadedFile;

class Factory implements 
    RequestFactoryInterface, 
    ResponseFactoryInterface, 
    ServerRequestFactoryInterface, 
    StreamFactoryInterface, 
    UploadedFileFactoryInterface, 
    UriFactoryInterface
{
    public function createRequest(string $method, $uri): RequestInterface 
    {
        $uri = $uri instanceof UriInterface
            ? $uri
            : $this->createUri($uri);

        return new Request('1.0', [], null, '', $method, $uri);
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface 
    {
        return new Response('1.0', [], null, $code, $reasonPhrase);
    }

    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface 
    {
        $uri = $uri instanceof UriInterface
            ? $uri
            : $this->createUri($uri);

        return new ServerRequest('1.0', [], null, '', $method, $uri = null, [], [], [], null, []);
    }

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

    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface 
    {
        return new UploadedFile($stream, $clientFilename, $clientMediaType, $error, $size);
    }

    public function createUri(string $uri = '') : UriInterface
    {
        return Uri::parseString($uri);
    }
}
