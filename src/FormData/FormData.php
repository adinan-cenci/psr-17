<?php

namespace AdinanCenci\Psr17\FormData;

use Psr\Http\Message\StreamInterface;

/**
 * Represents a single piece of information extracted from a formdata stream.
 */
class FormData
{
    /**
     * @var string
     *
     * The name of the input.
     */
    protected string $name = '';

    /**
     * @var string
     *
     * The value.
     */
    protected string $value = '';

    /**
     * @var resource
     *
     * Resource with the uploaded file's content.
     */
    protected $file = null;

    /**
     * @var string
     *
     * The name of the uploaded file.
     */
    protected string $filename = '';

    /**
     * @var string
     *
     * The tempname of the uploaded file.
     */
    protected string $tempName = '';

    /**
     * @var string
     *
     * The mime type of the upload file.
     */
    protected string $contentType = '';

    /**
     * @var int
     *
     * The size of the uploaded file in bytes.
     */
    protected int $size = 0;

    /**
     * Magic method to read protected properties.
     *
     * @var string $var
     *   The name of the property.
     *
     * @return mixed
     *   The value of the property.
     */
    public function __get($var)
    {
        return $this->{$var};
    }

    /**
     * Checks if the data regards a file.
     *
     * @return bool
     *   True if it regards a file.
     */
    public function isFile(): bool
    {
        return $this->file != null;
    }

    /**
     * Set the name of the input.
     *
     * @param string
     *   The name of the input.
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Append data to the value.
     *
     * @param string $value
     *   The data to be added.
     */
    public function addValue($value)
    {
        if ($this->isFile()) {
            fwrite($this->file, $value);
            $this->size += strlen($value);
        } else {
            $this->value .= $value;
        }
    }

    /**
     * Sets the file's mime type.
     *
     * @param string $contentType
     *   The mime type.
     */
    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Sets the filename.
     *
     * @param string $filename
     *   The filename.
     */
    public function setFilename(string $filename)
    {
        $this->filename = $filename;
        $this->createFile();
    }

    /**
     * Creates a temporary file to save data to.
     */
    protected function createFile()
    {
        $this->file     = tmpfile();
        $this->tempName = stream_get_meta_data($this->file)['uri'];
    }
}
