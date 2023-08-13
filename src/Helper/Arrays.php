<?php 
namespace AdinanCenci\Psr17\Helper;

abstract class Arrays 
{
    /**
     * Given an array, it returns ass the key paths to each value contained in it.
     * 
     * @param array $array
     * @param array|null $prefix
     * @param array|null $root
     * 
     * @return (string|int)[]
     * 
     * @example
     *  Arrays::getAllPaths(['foo' => 'bar'])
     *  will return [ ['foo'] ]
     */
    public static function getAllPaths(array $array, ?array $prefix = null, ?array &$root = null) : array
    {
        if (is_null($root)) {
            $root = [];
        }
    
        foreach ($array as $key => $value) {
            $path = $prefix ? $prefix : [];
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
     * Given an $array, it will return the value at the end of the $path of keys.
     * It returns null if there is nothing there.
     * 
     * @param array $array
     * @param string[] path
     * 
     * @return mixed
     * 
     * @example
     *  Array::getValueAtEndOfPath(['foo' => 'bar'], ['foo'])
     *  Will return 'bar'
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
     * Given an $array, it will set a $value at the end of a $path of keys.
     * 
     * @param array $array
     * @param string[] $path
     * @param mixed $value
     * 
     * @return void
     * 
     * @example
     *   Arrays::setValueAtEndOfPath($array, ['foo', 'bar'], 'baz')
     *   Will set $array['foo']['bar'] as 'baz'
     */
    public static function setValueAtEndOfPath(array &$array, array $path, $value) : void
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
