<?php
namespace AdinanCenci\Psr17;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use AdinanCenci\Psr7\Response;

class ResponseFactory implements ResponseFactoryInterface 
{
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface 
    {
        return new Response('1.0', [], null, $code, $reasonPhrase);
    }
}
