<?php

namespace AdinanCenci\Psr17\FormData;

use Psr\Http\Message\StreamInterface;

/**
 * Parses a multipart-form-data stream into comprehensive information.
 */
class MultipartFormDataParser
{
    /**
     * The request's body.
     *
     * @var Psr\Http\Message\StreamInterface
     */
    protected StreamInterface $stream;

    /**
     * @var string
     *
     * The boundary that separates the different parts of the data.
     */
    protected string $boundary;

    /**
     * The parsed data.
     *
     * @var AdinanCenci\Psr17\FormData\FormData[]
     */
    protected array $parts = [];

    /**
     * The current part we are working with.
     *
     * @var AdinanCenci\Psr17\FormData\FormData
     */
    protected $currentPart = null;

    /**
     * Constructor.
     *
     * @param Psr\Http\Message\StreamInterface $stream
     *   The request's body.
     * @param string $boundary
     *   The boundary that separates the different parts of the data.
     */
    public function __construct(StreamInterface $stream, string $boundary)
    {
        if (!$boundary) {
            throw new \InvalidArgumentException('Invalid boundary');
        }

        $this->stream   = $stream;
        $this->boundary = $boundary;
        $this->boundaryLength = strlen($boundary);
    }

    /**
     * Parses the data and returns it.
     *
     * @return AdinanCenci\Psr17\FormData\FormData[]
     *   The parsed data.
     */
    public function parse(): array
    {
        $chunkSize   = 8192;
        $pointer     = 0;

        // Read the first line with the boundary.
        $this->stream->seek(0);
        $beginning    = $this->stream->read(150);
        $position = $this->lookForBoundaryLine($beginning, $this->boundary, $boundaryLineLenght);
        if ($position == -1) {
            throw new \InvalidArgumentException('Boundary not found withing the data.');
        }

        // Move the pointer to the first part.
        $this->stream->seek($boundaryLineLenght);
        $pointer      = $boundaryLineLenght;
        $this->addChunk('');
        unset($beginning);

        while ($chunk = $this->stream->read($chunkSize)) {
            $chunkLength      = strlen($chunk);
            $boundaryPosition = $this->lookForBoundaryLine($chunk, $this->boundary, $boundaryLineLenght);

            // It is possible for the chunk's end to land on top of the
            // boundary's thus failing to detect it. So we read just a bit
            // further, just to make sure.
            if ($boundaryPosition == -1) {
                $aBitMore          = $this->stream->read($this->boundaryLength * 2);
                $chunk            .= $aBitMore;
                $chunkLength      += strlen($aBitMore);
                $boundaryPosition  = $this->lookForBoundaryLine($chunk, $this->boundary, $boundaryLineLenght);
            }

            // No Boundary, adds the chunk to current part.
            if ($boundaryPosition == -1) {
                $this->addChunk($chunk);
                $pointer += $chunkLength;
            // Found a new boundary, this piece of data ends, another begins.
            } else {
                $chunkLength  = $boundaryPosition;
                $chunk        = substr($chunk, 0, $chunkLength);
                $pointer     += $chunkLength + $boundaryLineLenght;

                $this->addChunk($chunk);
                $this->newPart();

                $this->stream->seek($pointer);
            }
        }

        $formData = [];

        foreach ($this->parts as $p) {
            $data = $p->getFormData();
            if ($data->name) {
                $formData[] = $data;
            }
        }

        return $formData;
    }

    /**
     * Adds a string to the current form data parser.
     *
     * @param string $chunk
     *   Chunk of data.
     */
    protected function addChunk(string $chunk): void
    {
        $currentPart = $this->getCurrentPart();
        $currentPart->addChunk($chunk);
    }

    /**
     * Returns the current part we are working with.
     *
     * @return AdinanCenci\Psr17\FormData\FormDataParser
     *   The current data parser.
     */
    protected function getCurrentPart(): FormDataParser
    {
        $this->currentPart = $this->currentPart
            ? $this->currentPart
            : $this->newPart();

        return $this->currentPart;
    }

    /**
     * Adds a new data parser to the list and returns it.
     *
     * @return AdinanCenci\Psr17\FormData\FormDataParser
     *   The new part.
     */
    protected function newPart(): FormDataParser
    {
        $newPart = new FormDataParser();
        $this->parts[] = $this->currentPart = $newPart;
        return $newPart;
    }

    /**
     * Looks for the boundary withing a string.
     *
     * @param string $chunk
     *   Chunk of data to look into.
     * @param string $boundary
     *   The boundary.
     * @param int $boundaryLineLenght
     *   It will return the length of the line where the boundary can be found.
     *
     * @return int
     *   The position in the $chunk where the boundary begins.
     */
    protected function lookForBoundaryLine(string $chunk, string $boundary, &$boundaryLineLenght = 0): int
    {
        $regex = "/[\r\n]*-*$this->boundary-*[\r\n]*/";
        if (preg_match($regex, $chunk, $matches, \PREG_OFFSET_CAPTURE)) {
            $boundaryLineLenght = strlen($matches[0][0]);
            return $matches[0][1];
        }

        $boundaryLineLenght = 0;
        return -1;
    }
}
