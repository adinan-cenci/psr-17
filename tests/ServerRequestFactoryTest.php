<?php

namespace AdinanCenci\Psr17\Tests;

use AdinanCenci\Psr7\ServerRequest;
use AdinanCenci\Psr17\ServerRequestFactory;
use AdinanCenci\Psr17\StreamFactory;
use AdinanCenci\Psr17\UriFactory;
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

    public function testParseMultipartFormData()
    {
        $uriFactory = new UriFactory();
        $uri = $uriFactory->createUri('https://mytest.com/');

        $streamFactory = new StreamFactory();
        $body = $streamFactory->createStreamFromFile('./tests/files/multipart-form-data.txt');

        $request = new ServerRequest(
            '1.1',
            [
                'Content-type' => 'multipart/form-data; boundary=----WebKitFormBoundaryTNvOF5ccQEMLOAA3',
            ],
            $body,
            '/',
            'PUT',
            $uri,
            [],
            [],
            [],
            null,
            [],
            []
        );

        $factory = $this->getFactory();
        $request = $factory->parseBody($request);
    }
}
