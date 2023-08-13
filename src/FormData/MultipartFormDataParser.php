<?php 
namespace AdinanCenci\Psr17\FormData;

use Psr\Http\Message\StreamInterface;

class MultipartFormDataParser 
{
    protected StreamInterface $stream;
    protected string $boundary;

    protected array $parts = [];
    protected $currentPart = null;

    public function __construct(StreamInterface $stream, string $boundary) 
    {
        $this->stream   = $stream;
        $this->boundary = $boundary;
        $this->boundaryLength = strlen($boundary);
    }

    /**
     * @return []FormData
     */
    public function parse() : array
    {
        $chunkSize   = 8192;
        $pointer     = 0;

        // Read the first line with the boundary.
        $this->stream->seek(0);
        $beginning    = $this->stream->read(150);
        $this->lookForBoundaryLine($beginning, $this->boundary, $boundaryLineLenght);
        // Move the pointer to the first part.
        $this->stream->seek($boundaryLineLenght);
        $pointer      = $boundaryLineLenght;
        $this->addChunk('');
        unset($beginning);

        while ($chunk = $this->stream->read($chunkSize)) {
            $chunkLength      = strlen($chunk);
            $boundaryPosition = $this->lookForBoundaryLine($chunk, $this->boundary, $boundaryLineLenght);

            // It is possible for the chunk's end to land on top of the boundary's 
            // thus failing to detect it. So we read just a bit further, just to make sure.
            if ($boundaryPosition == -1) {
                $aBitMore          = $this->stream->read( $this->boundaryLength * 2 );
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

    protected function addChunk(string $chunk) : void 
    {
        $currentPart = $this->getCurrentPart();
        $currentPart->addChunk($chunk);
    }

    protected function getCurrentPart() : FormDataParser
    {
        return $this->currentPart = $this->currentPart 
            ? $this->currentPart
            : $this->newPart();
    }

    protected function newPart() : FormDataParser
    {
        $newPart = new FormDataParser();
        $this->parts[] = $this->currentPart = $newPart;
        return $newPart;
    }

    protected function lookForBoundaryLine(string $chunk, string $boundary, &$boundaryLineLenght = 0) : int
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


