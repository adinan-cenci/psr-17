<?php

namespace AdinanCenci\Psr17\Helper;

abstract class Arrays
{
    /**
     * Given an array, return all keys leading to each value contained in it.
     *
     * @param array $array
     *   The array.
     * @param array|null $prefix
     *   The parent keys.
     * @param array|null $root
     *   The entire graph.
     *   It accomulates at each iteration.
     *
     * @return (string|int)[]
     *   All the path to every value in the array.
     *
     * @example
     *   $array = ['foo' => ['bar' => 'baz']];
     *   Arrays::getAllPaths($array)
     *   will return [ ['foo', 'bar'] ]
     */
    public static function getAllPaths(array $array, ?array $prefix = null, ?array &$root = null): array
    {
        if (is_null($root)) {
            $root = [];
        }

        foreach ($array as $key => $value) {
            $path = $prefix
                ? $prefix
                : [];
            $path[] = $key;

            if (!is_array($value)) {
                $root[] = $path;
            }

            if (is_array($value)) {
                self::getAllPaths($value, $path, $root);
            }
        }

        return $root;
    }

    /**
     * Given an $array, return the value at the end of the $path of keys.
     *
     * Returns null if there is nothing there.
     *
     * @param array $array
     *   The array.
     * @param string[] $path
     *   List of keys.
     *
     * @return mixed
     *   The value at the end.
     *
     * @example
     *   $array = ['foo' => ['bar' => 'baz']];
     *   Array::getValueAtEndOfPath($array, ['foo', 'bar'])
     *   Will return the value at $array['foo']['bar'].
     */
    public static function getValueAtEndOfPath(array $array, array $path)
    {
        foreach ($path as $p) {
            if (!isset($array[$p])) {
                return null;
            }
            $array = $array[$p];
        }

        return $array;
    }

    /**
     * Given an $array, set a $value at the end of the given $path of keys.
     *
     * @param array $array
     *   The array.
     * @param string[] $path
     *   List of keys.
     * @param mixed $value
     *   The value to add.
     *
     * @return void
     *
     * @example
     *   Arrays::setValueAtEndOfPath($array, ['foo', 'bar'], 'baz')
     *   Same as $array['foo']['bar'] = 'baz'
     */
    public static function setValueAtEndOfPath(array &$array, array $path, $value): void
    {
        $last = array_pop($path);

        foreach ($path as $key) {
            if ($key === '') {
                $array = &$array[];
            } else {
                $array = &$array[$key];
            }
        }

        if ($last === '') {
            $array[] = $value;
        } else {
            $array[$last] = $value;
        }
    }
}
