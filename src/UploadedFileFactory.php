<?php

namespace AdinanCenci\Psr17;

use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\StreamInterface;
use AdinanCenci\Psr7\UploadedFile;
use AdinanCenci\Psr17\Helper\Globals;
use AdinanCenci\Psr17\Helper\Input;
use AdinanCenci\Psr17\Helper\Arrays;

class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {
        return new UploadedFile($stream, $clientFilename, $clientMediaType, $error, $size);
    }

    /**
     * Creates file objects from the PHP globals.
     *
     * @return Psr\Http\Message\UploadedFileInterface[]
     *   Array of uploaded files.
     */
    public static function getFilesFromGlobals(): array
    {
        $uploadedFiles = [];

        $files = Globals::getUploadedFiles();

        foreach ($files as $file) {
            $uploaded = new UploadedFile($file['tmpName'], $file['name'], $file['type'], $file['error'], $file['size']);
            Arrays::setValueAtEndOfPath($uploadedFiles, $file['path'], $uploaded);
        }

        return $uploadedFiles;
    }
}
