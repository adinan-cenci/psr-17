<?php

namespace AdinanCenci\Psr17\Tests;

use AdinanCenci\Psr7\ServerRequest;
use AdinanCenci\Psr17\ServerRequestFactory;
use AdinanCenci\Psr17\StreamFactory;
use AdinanCenci\Psr17\UriFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

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
        $file     = './tests/files/multipart-form-data.txt';
        $boundary = '----WebKitFormBoundaryTNvOF5ccQEMLOAA3';

        $uriFactory = new UriFactory();
        $streamFactory = new StreamFactory();

        $uri  = $uriFactory->createUri('https://mytest.com/');
        $body = $streamFactory->createStreamFromFile($file);

        $request = new ServerRequest(
            '1.1',
            [
                'Content-type' => 'multipart/form-data; boundary=' . $boundary,
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

        $postData = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();

        $this->assertEquals([
            'party_name' => 'The Fellowship of the Ring',
            'foundation' => 'Rivendell',
            'members' => [
                'wizard' => 'Gandalf',
                'ranger' => 'Aragorn',
                'warrior' => 'Boromir',
                'tank' => 'Gimli',
                'halfling' => [
                    'Samwise Gamgee',
                    'Frodo Baggins',
                    'Pippin Took',
                    'Merry Brandybuck',
                ]
            ]
        ], $postData);

        $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFiles['enemies']['final_boss']);
        $this->assertEquals('sauron.jpg', $uploadedFiles['enemies']['final_boss']->getClientFilename());
    }

    public function testParseJson()
    {
        $json = '{"series":"The Lord of the Rings","author":"J. R. R. Tolkien"}';

        $uriFactory = new UriFactory();
        $streamFactory = new StreamFactory();

        $uri  = $uriFactory->createUri('https://mytest.com/file.json');
        $body = $streamFactory->createStream($json);

        $request = new ServerRequest(
            '1.1',
            [
                'Content-type' => 'application/json',
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

        $postData = $request->getParsedBody();

        $this->assertInstanceOf(\stdClass::class, $postData);
        $this->assertEquals('The Lord of the Rings', $postData->series);
    }

    public function testParseXml()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <series>
            <book>The Fellowship of the Ring</book>
            <book>The Two Towers</book>
            <book>The Return of the King</book>
        </series>';

        $uriFactory = new UriFactory();
        $streamFactory = new StreamFactory();

        $uri  = $uriFactory->createUri('https://mytest.com/file.json');
        $body = $streamFactory->createStream($xml);

        $request = new ServerRequest(
            '1.1',
            [
                'Content-type' => 'text/xml',
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

        $postData = $request->getParsedBody();

        $this->assertInstanceOf(\SimpleXMLElement::class, $postData);
        $this->assertEquals('The Fellowship of the Ring', $postData->book[0]);
        $this->assertEquals('The Two Towers', $postData->book[1]);
        $this->assertEquals('The Return of the King', $postData->book[2]);
    }
}
