<?php 
namespace AdinanCenci\Psr17\Helper;

abstract class Inputs 
{
    public static function insertIntoArray(array &$array, string $inputName, $value) 
    {
        $keys = self::splitInputName($inputName);
        Arrays::setValueAtEndOfPath($array, $keys, $value);
    }

    /**
     * @param string $inputName
     * @return string[]
     * 
     * @example 
     *   Input::splitInputName('person[identification][name]')
     *   Return ['person', 'identification', 'name']
     */
    public static function splitInputName(string $inputName) : array
    {
        $keys = preg_split('/[\[\]]{1,2}/', rtrim($inputName, ']'));
        return $keys;
    }
}
