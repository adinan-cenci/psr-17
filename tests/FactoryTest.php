<?php
namespace AdinanCenci\Psr17\Tests;

use AdinanCenci\Psr17\Factory;
use PHPUnit\Framework\TestCase;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class FactoryTest extends TestCase 
{
    protected function getFactory() 
    {
        return new Factory();
    }

    public function testCreateRequest() 
    {
        $request = $this->getFactory()->createRequest('GET', 'http://foo-bar.com');

        $this->assertInstanceOf(RequestInterface::class, $request);
    }

    public function testCreateResponse() 
    {
        $response = $this->getFactory()->createResponse(200, 'foo bar');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testCreateServerRequest() 
    {
        $serverRequest = $this->getFactory()->createServerRequest('POST', 'http://foo-bar.com', []);

        $this->assertInstanceOf(ServerRequestInterface::class, $serverRequest);
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

    public function testCreateUploadedFile() 
    {
        $factory = $this->getFactory();

        $uploadedFile = $factory->createUploadedFile(
            $factory->createStream('foobar'),
            6,
            \UPLOAD_ERR_OK,
            'foobar.txt',
            'text/plain'
        );

        $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFile);
    }

    public function testCreateUri() 
    {
        $uri = $this->getFactory()->createUri('https://foo-bar.com/path/file.txt?query=random#h1');

        $this->assertInstanceOf(UriInterface::class, $uri);
    }
}
