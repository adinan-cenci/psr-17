<?php

namespace AdinanCenci\Psr17\FormData;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use AdinanCenci\Psr7\UploadedFile;
use AdinanCenci\Psr17\Helper\Headers;

/**
 * Parses a single piece of formdata into comprehensive information.
 */
class FormDataParser
{
    /**
     * @var bool
     *
     * Whether we have parsed the headers.
     */
    protected bool $headersParsed = false;

    /**
     * The final parsed data.
     *
     * @var AdinanCenci\Psr17\FormData\FormData
     */
    protected FormData $formData;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->formData = new FormData();
    }

    /**
     * Adds a chunk to the data we will be parsing.
     *
     * @param string $chunk
     *   Data read from the request's body.
     */
    public function addChunk(string $chunk): void
    {
        if (!$chunk) {
            return;
        }

        if (!$this->headersParsed) {
            $parts = $this->separateHeadersFromBody($chunk);
            $chunk = $parts['body'];
            $this->readHeaders($parts['headers']);
            $this->headersParsed = true;
        }

        $this->formData->addValue($chunk);
    }

    /**
     * Returns the parsed form data.
     *
     * @return AdinanCenci\Psr17\FormData\FormData
     *   The form data.
     */
    public function getFormData(): FormData
    {
        return $this->formData;
    }

    /**
     * Given the initial chunk of the data, separates the header from the body.
     *
     * @param string $chunk
     *   The header and and the beginning of the body if not in its entirety.
     *
     * @return array
     *   The headers and beginning of the body ( if not entirely ) separated.
     */
    protected function separateHeadersFromBody(string $chunk): array
    {
        $position = $this->getHeadersEnd($chunk, $lineBreakLenght);

        return [
            'headers' => substr($chunk, 0, $position),
            'body'    => substr($chunk, $position + $lineBreakLenght)
        ];
    }

    /**
     * Given a chunk, it finds where the header ends.
     *
     * @param string $chunk
     *   Chunk of data.
     * @param $lineBreakLength
     *   It will become the lenght of the line-breaking characters that
     *   separate the header and body.
     *
     * @return int
     *   The position where the header ends.
     */
    protected function getHeadersEnd(string $chunk, &$lineBreakLenght = 0): int
    {
        if ($pos = strpos($chunk, "\r\n\r\n")) {
            $lineBreakLenght = 4;
            return $pos;
        }

        if ($pos = strpos($chunk, "\n\n")) {
            $lineBreakLenght = 2;
            return $pos;
        }

        $lineBreakLenght = 0;
        return strlen($chunk);
    }

    /**
     * Reads information from the headers.
     *
     * @param string $headers
     *   The headers of the formdata.
     */
    protected function readHeaders(string $headers): void
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
