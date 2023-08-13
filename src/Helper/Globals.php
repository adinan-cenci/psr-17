<?php 
namespace AdinanCenci\Psr17\Helper;

abstract class Globals 
{
    public static function getProtocolVersion() : string
    {
        if (empty($_SERVER['SERVER_PROTOCOL'])) {
            return '1.1';
        }

        preg_match('/([\d\.])+/', $_SERVER['SERVER_PROTOCOL'], $matches);

        return $matches[1];
    }

    public static function getMethod(array $server = []) : string
    {
        return isset($server['REQUEST_METHOD'])
            ? strtoupper($server['REQUEST_METHOD'])
            : 'GET';
    }

    public static function getHeaders() : array
    {
        $headers = array();

        if (function_exists('getallheaders')) {
            $headers = \getallheaders();
        }

        if ($headers) {

            foreach ($headers as $key => $value) {
                $headers[strtolower($key)] = $value;
            }

            return $headers;
        }

        $headers = array();

        foreach ($_SERVER as $key => $value) {

            $key = strtolower($key);

            if (substr($key, 0, 5) != 'http_' && ($key != 'content_type') && $key != 'content_length') {
                continue;
            }

            $name = ltrim(str_replace('_', '-', $key), 'http-');

            $headers[$name] = $value;
        }

        return $headers;
    }

    public static function getScheme() : string 
    {
        $schema = self::getServerVar('HTTP_X_FORWARDED_PROTO');

        if ($schema) {
            return $schema;
        }

        $https = self::getServerVar('HTTPS');

        if ($https == 'on') {
            return 'https';
        }

        return self::getServerVar('REQUEST_SCHEME', 'http');
    }

    public static function getPassword() : string 
    {
        return self::getServerVar('PHP_AUTH_PW');
    }

    public static function getUser() : string 
    {
        return self::getServerVar('PHP_AUTH_USER');
    }

    public static function getPort() 
    {
        $port = self::getServerVar('SERVER_PORT', null);
        return is_int($port) 
            ? $port 
            : null;
    }

    public static function getHost() : string
    {
        return self::getServerVar('HTTP_HOST', NULL);
    }

    public static function getPath() : string 
    {
        $uri = self::getServerVar('REQUEST_URI');
        $query = self::getQueryString();

        $path = str_replace($query, '', $uri);

        return rtrim($path, '?');

        return $path;
    }

    public static function getQueryString() : string 
    {
        return self::getServerVar('QUERY_STRING');
    }

    public static function getQueryVariables() : array
    {
        $query = self::getQueryString();

        if (empty($query)) {
            return [];
        }

        parse_str($query, $output);

        return $output ?? [];
    }

    public static function getUploadedFiles() : array
    {
        $files = [];

        foreach ($_FILES as $inputName => $input) {
            $paths = is_array($_FILES[$inputName]['name']) 
                ? Arrays::getAllPaths($_FILES[$inputName]['name'])
                : [$inputName];

            foreach ($paths as $path) {
                $files[] = self::getUploadedFile($inputName, $path);
            }
        }

        return $files;
    }

    protected static function getUploadedFile(string $inputName, $path) : array
    {
        if (is_array($path)) {
            $fullPath = [$inputName];
            array_splice($fullPath, 1, count($path), $path);

            return [
                'path'      => $fullPath,
                'name'      => Arrays::getValueAtEndOfPath($_FILES[$inputName]['name'], $path), 
                'type'      => Arrays::getValueAtEndOfPath($_FILES[$inputName]['type'], $path), 
                'tmpName'   => Arrays::getValueAtEndOfPath($_FILES[$inputName]['tmp_name'], $path), 
                'error'     => Arrays::getValueAtEndOfPath($_FILES[$inputName]['error'], $path), 
                'size'      => Arrays::getValueAtEndOfPath($_FILES[$inputName]['size'], $path)
            ];
        }

        return [
            'path'      => [$path],
            'name'      => $_FILES[$inputName]['name'], 
            'type'      => $_FILES[$inputName]['type'], 
            'tmpName'   => $_FILES[$inputName]['tmp_name'], 
            'error'     => $_FILES[$inputName]['error'], 
            'size'      => $_FILES[$inputName]['size']
        ];
    }

    protected static function getServerVar($name, $default = '') 
    {
        return isset($_SERVER[$name]) 
            ? $_SERVER[$name] 
            : $default;
    }
}
