<?php

namespace AdinanCenci\Psr17\Helper;

abstract class Globals
{
    /**
     * Returns the version of the HTTP protocol for the current request.
     *
     * @return string
     *   The protocol version.
     */
    public static function getProtocolVersion(): string
    {
        if (empty($_SERVER['SERVER_PROTOCOL'])) {
            return '1.1';
        }

        preg_match('/([\d\.])+/', $_SERVER['SERVER_PROTOCOL'], $matches);

        return $matches[1];
    }

    /**
     * Returns the HTTP method for the current request.
     *
     * @param $server
     *   The server array.
     *
     * @return string
     *   The HTTP method.
     */
    public static function getMethod(array $server = []): string
    {
        return isset($server['REQUEST_METHOD'])
            ? strtoupper($server['REQUEST_METHOD'])
            : 'GET';
    }

    /**
     * Returns the headers of the current request.
     *
     * @return array
     *   The HTTP headers.
     */
    public static function getHeaders(): array
    {
        $headers = [];
        if (function_exists('getallheaders')) {
            $headers = \getallheaders();
        }

        if ($headers) {
            foreach ($headers as $key => $value) {
                $headers[strtolower($key)] = $value;
            }

            return $headers;
        }

        $headers = [];
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

    /**
     * Returns the schema of the current request.
     *
     * @return string
     *   The schema.
     */
    public static function getScheme(): string
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

    /**
     * Returns the basic authentication password from the current request.
     *
     * @return string
     *   The password.
     */
    public static function getPassword(): string
    {
        return self::getServerVar('PHP_AUTH_PW');
    }

    /**
     * Returns the basic authentication username from the current request.
     *
     * @return string
     *   The username.
     */
    public static function getUser(): string
    {
        return self::getServerVar('PHP_AUTH_USER');
    }

    /**
     * Returns the host of the current request.
     *
     * @return string
     *   The host.
     */
    public static function getHost(): string
    {
        $host = self::getServerVar('HTTP_X_FORWARDED_HOST', null) ?? self::getServerVar('HTTP_HOST', null);
        return substr_count($host, ':')
            ? preg_replace('/:[0-9]+$/', '', $host)
            : $host;
    }

    /**
     * Returns the port of the current request.
     *
     * @return null|string
     *   The host.
     */
    public static function getPort(): ?int
    {
        $port = self::getServerVar('HTTP_X_FORWARDED_PORT', null) ?? self::getServerVar('SERVER_PORT', null);
        return is_numeric($port)
            ? (int) $port
            : null;
    }

    /**
     * Returns the path of the current request.
     *
     * @return string
     *   The path.
     */
    public static function getPath(): string
    {
        $uri = self::getServerVar('REQUEST_URI');
        $query = self::getQueryString();

        $path = str_replace($query, '', $uri);

        return rtrim($path, '?');

        return $path;
    }

    /**
     * Returns the query string of the current request.
     *
     * @return string
     *   The query string.
     */
    public static function getQueryString(): string
    {
        return self::getServerVar('QUERY_STRING');
    }

    /**
     * Returns the parsed query string of the current request.
     *
     * @return array
     *   The parsed query string.
     */
    public static function getQueryVariables(): array
    {
        $query = self::getQueryString();

        if (empty($query)) {
            return [];
        }

        parse_str($query, $output);

        return $output ?? [];
    }

    /**
     * Returns the uploaded files array.
     *
     * @return array
     *   Uploaded file info.
     */
    public static function getUploadedFiles(): array
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

    /**
     * Returns information about a single uploaded file.
     *
     * @param string $inputName
     *   The name of the input.
     * @param string|array $path
     *   The path to the values.
     *   A string in simple cases, an array in case of nested inputs.
     *
     * @return array
     *   Information about the file, name, tmpName, error, size etc.
     */
    protected static function getUploadedFile(string $inputName, $path): array
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

    /**
     * Returns a given key from the global $_SERVER variable.
     *
     * @param string $name
     *   The key name.
     * @param mixed $default
     *   Default value to be returned if there is nothing there.
     *
     * @return mixed
     *   The value at the specified key.
     */
    protected static function getServerVar($name, $default = '')
    {
        return isset($_SERVER[$name])
            ? $_SERVER[$name]
            : $default;
    }
}
