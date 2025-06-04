<?php

namespace AdinanCenci\Psr17;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UriFactoryInterface;
use AdinanCenci\Psr17\Helper\Globals;
use AdinanCenci\Psr7\Uri;

class UriFactory implements UriFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createUri(string $uri = ''): UriInterface
    {
        return Uri::parseString($uri);
    }

    /**
     * Creates the URI for the current request.
     *
     * @return Psr\Http\Message\UriInterface
     *   The requested URI.
     */
    public function createFromGlobals(): UriInterface
    {
        $scheme   = Globals::getScheme();
        $password = Globals::getPassword();
        $username = Globals::getUser();
        $host     = Globals::getHost();
        $port     = Globals::getPort();
        $path     = Globals::getPath();
        $query    = Globals::getQueryString();
        $fragment = '';

        return new Uri($scheme, $username, $password, $host, $port, $path, $query, $fragment);
    }
}
