<?php

if (!function_exists('snake_to_studly_case')) {
    /**
     * Converts a snake_case string to a StudlyCase string 
     * @param string $snakeCaseString
     * 
     * @return string
     */
    function snake_to_studly_case(string $snakeCaseString): string
    {
        return array_reduce(explode('_', $snakeCaseString), function ($studlyCaseString, $subString) {
            return $studlyCaseString . ucfirst($subString);
        }, '');
    }
}

if (!function_exists('snake_to_camel_case')) {

    /**
     * Converts a snake_case string to a camelCase string 
     * @param string $snakeCaseString
     * 
     * @return string
     */
    function snake_to_camel_case(string $snakeCaseString): string
    {
        return lcfirst(snake_to_studly_case($snakeCaseString));
    }
}
