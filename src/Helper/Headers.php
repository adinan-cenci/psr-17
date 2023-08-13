<?php 
namespace AdinanCenci\Psr17\Helper;

/**
 * Headers lack standardization, but this crude helper will do for our necessities.
 */
abstract class Headers 
{
    /**
     * @param string $header
     * @return string[] The header as an associative array.
     * 
     * @example
     *   Headers::parseHeader('Content-Disposition: form-data; name="your_input"; filename="foobar.txt"')
     *   Returns [
     *     'headerName' => 'Content-Disposition', 
     *     'directive' => 'form-data', 
     *     'name' => 'your_input', 
     *     'filename' => 'foobar.txt'
     *   ]
     */
    public static function parseHeader(string $header) 
    {
        $headerName = '';
        $headerValue = $header;

        if (substr_count($header, ':')) {
            list($headerName, $headerValue) = preg_split('/ ?: ?/', $header);
        }

        $parts = [
            'headerName' => $headerName
        ];

        $parts['directive'] = preg_match('#^([^;]+);? ?(.*)#', $headerValue, $matches)
            ? strtolower($matches[1])
            : $headerValue;

        if (!empty($matches[2])) {
            $parameters = trim($matches[2]);
            $parameters = preg_split('/; ?/', $parameters);

            foreach ($parameters as $p) {
                $split = explode('=', $p);
                $parts[ $split[0] ] = trim($split[1], '"');
            }
        }

        return $parts;
    }
}
