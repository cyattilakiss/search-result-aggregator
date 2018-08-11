<?php

namespace cyattilakiss\SearchResultAggregator;

interface SearchEngineCrawlerInterface
{
    /**
     * Send an HTTP request.
     *
     * @param string $query     query for the search engine search
     * @param string $language  ISO 639-1 Code
     * @param int    $num       number of results on a page
     * @param int    $page      pagination page number
     *
     * @return array            formatted associative array [['title' => 'string, 'url' => 'string', 'source' => []]]
     * @throws \cyattilakiss\SearchResultAggregator\SearchEngineCrawlerException
     */
    function getResults($query, $language, $num, $page);
}