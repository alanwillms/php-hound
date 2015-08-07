<?php
namespace phphound\helper;

/**
 * Array helper.
 */
class ArrayHelper
{
    /**
     * Ensure the value is an array. If it is not, return an empty array.
     * @param Mixed $value value to be verified.
     * @return array value itself or an empty array.
     */
    public static function ensure($value)
    {
        if (empty($value) || !is_array($value)) {
            return [];
        }
        return $value;
    }
}
