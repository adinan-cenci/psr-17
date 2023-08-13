<?php 
namespace AdinanCenci\Psr17\FormData;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use AdinanCenci\Psr7\UploadedFile;
use AdinanCenci\Psr17\Helper\Headers;

class FormDataParser 
{
    protected bool $headersParsed = false;

    protected FormData $formData;

    public function __construct() 
    {
        $this->formData = new FormData();
    }

    public function addChunk(string $chunk) : void
    {
        if (!$chunk) {
            return;
        }

        if (!$this->headersParsed) {
            $parts               = $this->separateHeadersFromBody($chunk);
            $chunk               = $parts['body'];
            $this->readHeaders($parts['headers']);
            $this->headersParsed = true;
        }

        $this->formData->addValue($chunk);
    }

    public function getFormData() : FormData
    {
        return $this->formData;
    }

    protected function separateHeadersFromBody(string $chunk) : array
    {
        $position = $this->getHeadersEnd($chunk, $lineBreakLenght);

        return [
            'headers' => substr($chunk, 0, $position),
            'body'    => substr($chunk, $position + $lineBreakLenght)
        ];
    }

    protected function getHeadersEnd(string $chunk, &$lineBreakLenght = 0) : int
    {
        if ($pos = strpos($chunk, "\r\n\r\n")) {
            $lineBreakLenght = 4;
            return $pos;
        }

        if ($pos = strpos($chunk, "\n\n")) {
            $lineBreakLenght = 2;
            return $pos;
        }
        
        throw new \Exception('fuck this');
    }

    protected function readHeaders(string $headers) : void
    {
        $headerLines = preg_split("/[\r\n]/", $headers);

        foreach ($headerLines as $headerLine) {
            $header = Headers::parseHeader($headerLine);

            if (isset($header['name'])) {
                $this->formData->setName($header['name']);
            }

            if (isset($header['filename'])) {
                $this->formData->setFilename($header['filename']);
            }

            if (strtolower($header['headerName']) == 'content-type') {
                $this->formData->setContentType($header['directive']);
            }
        }
    }
}
