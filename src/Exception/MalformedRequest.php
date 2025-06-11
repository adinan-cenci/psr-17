<?php

namespace AdinanCenci\Psr17\Exception;

use Psr\Http\Message\RequestInterface;

class MalformedRequest extends \InvalidArgumentException
{
    /**
     * The request that caused the exception.
     *
     * @var Psr\Http\Message\RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @param string $message
     *   The Exception message to throw.
     * @param int $code
     *   The Exception code.
     * @param \Throwable $previous
     *   The previous exception used for the exception chaining.
     * @param Psr\Http\Message\RequestInterface
     *   The request that caused the exception.
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        RequestInterface $request
    ) {
        parent::__construct($message, $code, $previous);
        $this->request = $request;
    }

    /**
     * Returns the request that caused the exception.
     *
     * @return Psr\Http\Message\RequestInterface
     *   Request object.
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
