<?php 
namespace AdinanCenci\Psr17\FormData;

use Psr\Http\Message\StreamInterface;

class FormData 
{
    protected string $name = '';

    protected string $value = '';

    protected $file = null;

    protected string $filename = '';

    protected string $tempName = '';

    protected string $contentType = '';

    protected int $size = 0;

    public function __get($var) 
    {
        return $this->{$var};
    }

    public function isFile() : bool
    {
        return $this->file != null;
    }

    public function setName($name) 
    {
        $this->name = $name;
    }

    public function addValue($value) 
    {
        if ($this->isFile()) {
            fwrite($this->file, $value);
            $this->size += strlen($value);
        } else {
            $this->value .= $value;
        }
    }

    public function setContentType(string $contentType) 
    {
        $this->contentType = $contentType;
    }

    public function setFilename(string $filename) 
    {
        $this->filename = $filename;
        $this->createFile();
    }

    protected function createFile() 
    {
        $this->file     = tmpfile();
        $this->tempName = stream_get_meta_data($this->file)['uri'];
    }
}
