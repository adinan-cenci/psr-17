<?php
namespace AdinanCenci\Psr17;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;

use AdinanCenci\Psr7\ServerRequest;
use AdinanCenci\Psr7\Stream;
use AdinanCenci\Psr17\Helper\Globals;

class ServerRequestFactory implements ServerRequestFactoryInterface 
{
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface 
    {
        $uri = $uri instanceof UriInterface
            ? $uri
            : (new UriFactory())->createUri($uri);

        return new ServerRequest('1.0', [], null, '', $method, $uri = null, [], [], [], null, []);
    }

    public function createFromGlobals() : ServerRequestInterface
    {
        $protocolVersion = Globals::getProtocolVersion();
        $headers         = Globals::getHeaders();
        $body            = new Stream(fopen('php://input', 'r'));
        $target          = Globals::getPath();
        $method          = Globals::getMethod($headers);
        $uri             = UriFactory::createFromGlobals();
        $cookieParams    = $_COOKIE;
        $queryParams     = Globals::getQueryVariables();
        $attributes      = []; // ????????????
        $parsedBody      = self::parseBody($body, $headers['content-type'] ?? null);
        $uploadedFiles   = UploadedFileFactory::getFilesFromGlobals();
        $serverParams    = $_SERVER;

        return new ServerRequest(
            $protocolVersion, 
            $headers, 
            $body, 
            $target, 
            $method, 
            $uri, 
            $cookieParams, 
            $queryParams, 
            $attributes, 
            $parsedBody, 
            $uploadedFiles,
            $serverParams
        );
    }

    public static function parseBody(StreamInterface $body, $contentType = null) 
    {
        if ($contentType == null) {
            return $_POST;
        }

        $contents = $body->getContents();

        switch ($contentType) {
            case 'application/json':
                json_decode($contents);
                break;
            case 'application/xml':
            case 'text/xml':
                return simplexml_load_string($contents);
                break;
        }
    }
}
