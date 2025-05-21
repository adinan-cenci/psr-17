<?php

namespace AdinanCenci\Psr17;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use AdinanCenci\Psr7\Response;
use AdinanCenci\Psr7\Stream;

class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response('1.0', [], null, $code, $reasonPhrase);
    }

    /**
     * Creates a generic 200 type response.
     *
     * Not part of the PSR-17, just a helpful method.
     *
     * @param string|Psr\Http\Message\StreamInterface $body
     *   The body of the response.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The new response object.
     */
    public function ok($body = ''): ResponseInterface
    {
        return $this->response(200, 'OK', $body);
    }

    /**
     * Creates a generic 201 type response.
     *
     * Not part of the PSR-17, just a helpful method.
     *
     * @param string|Psr\Http\Message\StreamInterface $body
     *   The body of the response.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The new response object.
     */
    public function created($body = ''): ResponseInterface
    {
        return $this->response(201, 'Created', $body);
    }

    /**
     * Creates a generic 3xx type response.
     *
     * Not part of the PSR-17, just a helpful method.
     *
     * @param int $code
     *   HTTP response code.
     * @param string $reasonPhrase
     *   The reason phrase to use with the provided status code.
     * @param string|Psr\Http\Message\UriInterface $location
     *   The location to redirect the user to.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The new response object.
     */
    public function moved(int $code, string $reasonPhrase, $location): ResponseInterface
    {
        $location = $location instanceof UriInterface
            ? (string) $location
            : $location;

        if (!is_string($location)) {
            throw new \InvalidArgumentException(
                'Location must be a string or an instance of Psr\Http\Message\UriInterface'
            );
        }

        $response = $this->createResponse($code, $reasonPhrase);
        $response = $response->withHeader('Location', $location);
        return $response;
    }

    /**
     * Creates a generic 301 type response.
     *
     * Not part of the PSR-17, just a helpful method.
     *
     * @param string|Psr\Http\Message\UriInterface $location
     *   The location to redirect the user to.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The new response object.
     */
    public function movedPermanently($location): ResponseInterface
    {
        return $this->moved(301, 'Moved Permanently', $location);
    }

    /**
     * Creates a generic 302 type response.
     *
     * Not part of the PSR-17, just a helpful method.
     *
     * @param string|Psr\Http\Message\UriInterface $location
     *   The location to redirect the user to.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The new response object.
     */
    public function movedTemporarily($location): ResponseInterface
    {
        return $this->moved(302, 'Moved Temporarily', $location);
    }

    /**
     * Creates a generic 400 type response.
     *
     * Not part of the PSR-17, just a helpful method.
     *
     * @param string|Psr\Http\Message\StreamInterface $body
     *   The body of the response.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The new response object.
     */
    public function badRequest($body = ''): ResponseInterface
    {
        return $this->response(400, 'Bad Request', $body);
    }

    /**
     * Creates a generic 401 type response.
     *
     * Not part of the PSR-17, just a helpful method.
     *
     * @param string|Psr\Http\Message\StreamInterface $body
     *   The body of the response.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The new response object.
     */
    public function unauthorized($body = ''): ResponseInterface
    {
        return $this->response(401, 'Unauthorized', $body);
    }

    /**
     * Creates a generic 403 type response.
     *
     * Not part of the PSR-17, just a helpful method.
     *
     * @param string|Psr\Http\Message\StreamInterface $body
     *   The body of the response.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The new response object.
     */
    public function forbidden($body = ''): ResponseInterface
    {
        return $this->response(403, 'Forbidden', $body);
    }

    /**
     * Creates a generic 404 type response.
     *
     * Not part of the PSR-17, just a helpful method.
     *
     * @param string|Psr\Http\Message\StreamInterface $body
     *   The body of the response.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The new response object.
     */
    public function notFound($body = ''): ResponseInterface
    {
        return $this->response(404, 'Not Found', $body);
    }

    /**
     * Creates a generic 500 type response.
     *
     * Not part of the PSR-17, just a helpful method.
     *
     * @param string|Psr\Http\Message\StreamInterface $body
     *   The body of the response.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The new response object.
     */
    public function internalServerError($body = null): ResponseInterface
    {
        return $this->response(500, 'Internal Server Error', $body);
    }

    /**
     * Creates a generic 501 type response.
     *
     * Not part of the PSR-17, just a helpful method.
     *
     * @param string|Psr\Http\Message\StreamInterface $body
     *   The body of the response.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The new response object.
     */
    public function notImplemented($body = null): ResponseInterface
    {
        return $this->response(501, 'Not Implemented', $body);
    }

    /**
     * Creates a generic 502 type response.
     *
     * Not part of the PSR-17, just a helpful method.
     *
     * @param string|Psr\Http\Message\StreamInterface $body
     *   The body of the response.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The new response object.
     */
    public function badGateway($body = null): ResponseInterface
    {
        return $this->response(502, 'Bad Gateway', $body);
    }

    /**
     * Creates a generic 503 type response.
     *
     * Not part of the PSR-17, just a helpful method.
     *
     * @param string|Psr\Http\Message\StreamInterface $body
     *   The body of the response.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The new response object.
     */
    public function serviceUnavailable($body = null): ResponseInterface
    {
        return $this->response(503, 'Service Unavailable', $body);
    }

    /**
     * Creates a new response object.
     *
     * @param int $code
     *   HTTP response code.
     * @param string $reasonPhrase
     *   The reason phrase to use with the provided status code.
     * @param string|Psr\Http\Message\StreamInterface $body
     *   The body of the response.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The new response object.
     */
    protected function response(int $code, string $reasonPhrase, $body = ''): ResponseInterface
    {
        $response = $this->createResponse($code, $reasonPhrase);

        $body = is_string($body)
            ? (new StreamFactory())->createStream($body)
            : $body;

        if ($body && !$body instanceof StreamInterface) {
            throw new \InvalidArgumentException(
                'Body must be a string or an instance of Psr\Http\Message\StreamInterface'
            );
        }

        if ($body) {
            $response = $response->withBody($body);
        }

        return $response;
    }
}
