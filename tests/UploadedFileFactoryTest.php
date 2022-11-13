<?php
namespace AdinanCenci\Psr17\Tests;

use AdinanCenci\Psr17\UploadedFileFactory;
use AdinanCenci\Psr17\StreamFactory;
use PHPUnit\Framework\TestCase;

use Psr\Http\Message\UploadedFileInterface;

class UploadedFileFactoryTest extends TestCase 
{
    protected function getFactory() 
    {
        return new UploadedFileFactory();
    }

    public function testCreateUploadedFile() 
    {
        $factory = $this->getFactory();

        $uploadedFile = $factory->createUploadedFile(
            (new StreamFactory())->createStream('foobar'),
            6,
            \UPLOAD_ERR_OK,
            'foobar.txt',
            'text/plain'
        );

        $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFile);
    }
}
