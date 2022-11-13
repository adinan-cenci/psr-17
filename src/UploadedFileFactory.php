<?php
namespace AdinanCenci\Psr17;

use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\StreamInterface;

use AdinanCenci\Psr17\Helper\Globals;
use AdinanCenci\Psr7\UploadedFile;

class UploadedFileFactory implements UploadedFileFactoryInterface
{
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface 
    {
        return new UploadedFile($stream, $clientFilename, $clientMediaType, $error, $size);
    }

    public static function getFilesFromGlobals() : array
    {
        $uploadedFiles = [];

        $files = Globals::getUploadedFiles();

        foreach ($files as $file) {
            $inputName = $file['inputName'];
            $uploadedFiles[$inputName][] = new UploadedFile($file['tmpName'], $file['name'], $file['type'], $file['error'], $file['size']);
        }

        return $uploadedFiles;
    }
}
