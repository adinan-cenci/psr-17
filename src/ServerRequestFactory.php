<?php
namespace AdinanCenci\Psr17;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;

use AdinanCenci\Psr7\ServerRequest;
use AdinanCenci\Psr7\Stream;
use AdinanCenci\Psr7\UploadedFile;
use AdinanCenci\Psr17\Helper\Globals;
use AdinanCenci\Psr17\Helper\Inputs;
use AdinanCenci\Psr17\Helper\Headers;
use AdinanCenci\Psr17\FormData\MultipartFormDataParser;

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
        $method          = Globals::getMethod($_SERVER);
        $uri             = (new UriFactory())->createFromGlobals();
        $cookieParams    = $_COOKIE;
        $queryParams     = Globals::getQueryVariables();
        $attributes      = []; // ????????????
        $serverParams    = $_SERVER;

        $request = new ServerRequest(
            $protocolVersion, 
            $headers, 
            $body, 
            $target, 
            $method, 
            $uri, 
            $cookieParams, 
            $queryParams, 
            $attributes, 
            null, 
            [], 
            $serverParams
        );

        $request = $this->parseBody($request);

        return $request;
    }

    public function parseBody(ServerRequestInterface $request) : ServerRequestInterface
    {
        $contenTypeHeader = $request->getHeader('content-type');
        $contentType = $contenTypeHeader 
            ? Headers::parseHeader($contenTypeHeader[0])
            : '';

        $mime = isset($contentType['directive']) 
            ? $contentType['directive']
            : '';

        $method = $request->getMethod();

        if ($method == 'POST' && in_array($mime, ['application/x-www-form-urlencoded', 'multipart/form-data', ''])) {
            $request = $request->withParsedBody($_POST);
            $request = $request->withUploadedFiles(UploadedFileFactory::getFilesFromGlobals());
            return $request;
        }

        switch ($mime) {
            case 'application/json':
                $request = $this->parseJson($request);
                break;
            case 'text/xml':
                $request = $this->parseXml($request);
                break;
            case 'application/x-www-form-urlencoded':
                $request = $this->parseUrlEncoded($request);
                break;
            case 'multipart/form-data':
                $request = $this->parseMultipartFormData($request, $contentType['boundary']);
                break;
        }

        return $request;
    }

    public function parseJson(ServerRequestInterface $request) : ServerRequestInterface
    {
        $json       = $request->getBody()->getContents();
        $parsedJson = json_decode($json);
        $request    = $request->withParsedBody($parsedJson);
        return $request;
    }

    public function parseXml(ServerRequestInterface $request) : ServerRequestInterface
    {
        $xml        = $request->getBody()->getContents();
        $dom        = simplexml_load_string($xml);
        $request    = $request->withParsedBody($dom);
        return $request;
    }

    public function parseUrlEncoded(ServerRequestInterface $request) : ServerRequestInterface
    {
        $string  = $request->getBody()->getContents();
        parse_str($string, $parsed);
        $request = $request->withParsedBody($parsed);
        return $request;
    }

    public function parseMultipartFormData(ServerRequestInterface $request, string $boundary) : ServerRequestInterface
    {
        $body   = $request->getBody();
        $parser = new MultipartFormDataParser($body, $boundary);
        $parsed = $parser->parse();

        $variables = [];
        $uploadedFiles = [];

        foreach ($parsed as $formData) {
            if ($formData->isFile()) {
                $uploadedFile = new UploadedFile($formData->file, $formData->filename, $formData->contentType, null, $formData->size);
                Inputs::insertIntoArray($uploadedFiles, $formData->name, $uploadedFile);
            } else {
                Inputs::insertIntoArray($variables, $formData->name, $formData->value);
            }
        }

        $request = $request->withParsedBody($variables);
        $request = $request->withUploadedFiles($uploadedFiles);

        return $request;
    }
}
