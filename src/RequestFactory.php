<?php

namespace AdinanCenci\Psr17;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestFactoryInterface;
use AdinanCenci\Psr7\Request;

class RequestFactory implements RequestFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        $uri = $uri instanceof UriInterface
            ? $uri
            : (new UriFactory())->createUri($uri);

        return new Request('1.0', [], null, '', $method, $uri);
    }
}
