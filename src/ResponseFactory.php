<?php
namespace AdinanCenci\Psr17;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamInterface;

use AdinanCenci\Psr7\Response;
use AdinanCenci\Psr7\Stream;
use Psr\Http\Message\UriInterface;

class ResponseFactory implements ResponseFactoryInterface 
{
    public function createResponse(int $code = 200, string $reasonPhrase = '') : ResponseInterface 
    {
        return new Response('1.0', [], null, $code, $reasonPhrase);
    }

    public function ok($body = null) 
    {
        return $this->response(200, 'OK', $body);
    }

    public function created($body = null) 
    {
        return $this->response(201, 'Created', $body);
    }

    /**
     * 300 ...
     */
    public function moved(int $code, string $reasonPhrase, $location) : ResponseInterface 
    {
        $location = $location instanceof UriInterface
            ? (string) $location
            : $location;

        if (! is_string($location)) {
            throw new \InvalidArgumentException('Location must be a string or an instance of Psr\Http\Message\UriInterface');
        }

        $response = $this->createResponse($code, $reasonPhrase);
        $response = $response->withHeader('Location', $location);
        return $response;
    }

    public function movedPermanently($location) : ResponseInterface 
    {
        return $this->moved(301, 'Moved Permanently', $location);
    }

    public function movedTemporarily($location) : ResponseInterface 
    {
        return $this->moved(302, 'Moved Temporarily', $location);
    }

    /**
     * 400 ...
     */
    public function badRequest($body = null) 
    {
        return $this->response(400, 'Bad Request', $body);
    }

    public function unauthorized($body = null) 
    {
        return $this->response(401, 'Unauthorized', $body);
    }

    public function forbidden($body = null) 
    {
        return $this->response(403, 'Forbidden', $body);
    }

    public function notFound($body = null) 
    {
        return $this->response(404, 'Not Found', $body);
    }

    /**
     * 500 ...
     */
    public function internalServerError($body = null) 
    {
        return $this->response(500, 'Internal Server Error', $body);
    }

    public function notImplemented($body = null) 
    {
        return $this->response(501, 'Not Implemented', $body);
    }

    public function badGateway($body = null) 
    {
        return $this->response(502, 'Bad Gateway', $body);
    }

    public function serviceUnavailable($body = null) 
    {
        return $this->response(503, 'Service Unavailable', $body);
    }

    protected function response(int $code, string $reasonPhrase, $body = null) 
    {
        $response = $this->createResponse($code, $reasonPhrase);

        $body = is_string($body)
            ? (new StreamFactory)->createStream($body)
            : $body;

        if ($body && !$body instanceof StreamInterface) {
            throw new \InvalidArgumentException('Body must be a string or an instance of Psr\Http\Message\StreamInterface');
        }

        if ($body) {
            $response = $response->withBody($body);
        }

        return $response;
    }

}
