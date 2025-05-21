<?php

namespace AdinanCenci\Psr17\Helper;

abstract class Inputs
{
    /**
     * Inserts a value into an array.
     *
     * Given the name of a nested input, inserts a value into an a array in the corresponding input name.
     *
     * @param array $array
     *   The array.
     * @param string $inputName
     *   The nested input name.
     * @param mixed $value
     *   The value to be inserted.
     *
     * @example
     *   Inputs::insertIntoArray($array, 'foo[bar]', 'something');
     *   Is the same as doing $array['foo']['bar'] = 'something';
     */
    public static function insertIntoArray(array &$array, string $inputName, $value)
    {
        $keys = self::splitInputName($inputName);
        Arrays::setValueAtEndOfPath($array, $keys, $value);
    }

    /**
     * Splits the name of a nested input name into its constituent parts.
     *
     * @param string $inputName
     *   The input name.
     *
     * @return string[]
     *   The constituent parts.
     *
     * @example
     *   Inputs::splitInputName('person[identification][name]')
     *   Return ['person', 'identification', 'name']
     */
    public static function splitInputName(string $inputName): array
    {
        $keys = preg_split('/[\[\]]{1,2}/', rtrim($inputName, ']'));
        return $keys;
    }
}
