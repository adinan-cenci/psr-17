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
        $uri             = (new UriFactory())->createFromGlobals();
        $cookieParams    = $_COOKIE;
        $queryParams     = Globals::getQueryVariables();
        $attributes      = []; // ????????????
        $parsedBody      = self::parseBody($method, $body, $headers['content-type'] ?? null);
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

    public static function getMime(string $contentType) : string
    {
        return preg_match('#^([^;]+)#', $contentType, $matches)
            ? trim(strtolower($matches[1]))
            : $contentType;
    }

    public static function breakContentType(string $contentType) : array
    {
        $parts = [];

        $parts['mime'] = preg_match('#^([^;]+);? ?(.*)#', $contentType, $matches)
            ? strtolower($matches[1])
            : $contentType;

        if (isset($matches[2])) {
            $parameters = trim($matches[2]);
            $parameters = preg_split('/; ?/', $parameters);

            foreach ($parameters as $p) {
                $split = explode('=', $p);
                $parts[ $split[0] ] = $split[1];
            }
        }

        return $parts;
    }

    public static function parseBody(string $method, StreamInterface $body, $contentType = null) 
    {
        $contentType = self::breakContentType($contentType);
        $mime = $contentType['mime'];

        if ($method == 'POST' && in_array($mime, ['application/x-www-form-urlencoded', 'multipart/form-data', ''])) {
            return $_POST;
        }

        $contents = $body->read($_SERVER['CONTENT_LENGTH']);

        switch ($contentType) {
            case 'application/json':
                json_decode($contents);
                break;
            case 'application/xml':
            case 'text/xml':
                return simplexml_load_string($contents);
                break;
            case 'application/x-www-form-urlencoded':
                parse_str($contents, $parsed);
                return $parsed;
                break;
            case 'multipart/form-data':
                return self::parseFormData($contents, $contentType['boundary']);
                break;
        }
    }

    public static function parseFormData($string, $boundary) 
    {
        $parts = preg_split("/-+$boundary/", $string);
    }
}
