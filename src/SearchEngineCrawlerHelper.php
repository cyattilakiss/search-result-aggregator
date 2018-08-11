<?php

namespace cyattilakiss\SearchResultAggregator;

class SearchEngineCrawlerHelper
{
    /**
     * Replaces all the keys to it's values which matches the following pattern "%key%" in a string
     *
     * @param $key_value_array
     * @param string $string
     * @return string
     */
    public static function replaceKeysToValuesInString($key_value_array, $string)
    {
        if ($key_value_array) {
            foreach ($key_value_array as $key => $value) {
                $string = str_replace("%$key%", $value, $string);
            }
        }

        return $string;
    }
}