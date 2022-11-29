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

    public static function getMethod(array $headers = []) : string
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        if ($method != 'post') {
            return $method;
        }

        if (! isset($headers['x-http-method-override'])) {
            return $method;
        }

        $overriden = strtoupper($headers['x-http-method-override']);

        if (in_array($overriden, array('PUT', 'DELETE', 'PATCH'))) {
            $method = $overriden;
        }

        return $method;
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
            $names    = (array) $input['name'];
            $types    = (array) $input['type'];
            $tmpNames = (array) $input['tmp_name'];
            $errors   = (array) $input['error'];
            $sizes    = (array) $input['size'];

            foreach ($names as $key => $name) {
                $files[] = [
                    'inputName' => $inputName,
                    'name'      => $name, 
                    'type'      => $types[$key],
                    'tmpName'   => $tmpNames[$key],
                    'error'     => $errors[$key],
                    'size'      => $sizes[$key],
                ];
            }
        }

        return $files;
    }

    protected static function getServerVar($name, $default = '') 
    {
        return isset($_SERVER[$name]) 
            ? $_SERVER[$name] 
            : $default;
    }
}
