<?php

namespace cyattilakiss\SearchResultAggregator;

class SearchEngineCrawler
{
    private $config = array();

    public function __construct()
    {
        $this->config = require __DIR__ . '\config\config.php';
    }

    /**
     * Returns the aggregated results from all the enabled search engines for the requested query
     *
     * @param $query
     * @param string $language
     * @param int $num
     * @param int $page
     * @return array
     */
    public function getResults($query, $language = 'en', $num = 10, $page = 1)
    {
        $final_results = array();

        // Get the enabled search engines and their crawlers
        if ($enabled_crawler_objects = $this->getEnabledCrawlerObject()) {
            $results = array();

            // Iterate through the search engines and get the query results
            foreach ($enabled_crawler_objects as $search_engine => $enabled_crawler_object) {
                try {
                    $results[$search_engine] = $enabled_crawler_object->getResults($query, $language, $num, $page);
                } catch (SearchEngineCrawlerException $e) {
                    // Should be logged since one of the crawlers has a bubbled up exception which is critical
                }
            }

            if ($results) {
                // Iterate through all the enabled search engines
                foreach ($results as $search_engine => $search_results) {
                    // Iterate through all the results got from a search engine crawler
                    foreach ($search_results as $search_result) {
                        if ($final_results) {
                            // Search for duplicated url-s
                            $key = array_search($search_result['url'], array_column($final_results, 'url'));
                            if ($key !== false) {
                                $final_results[$key]['source'][] = $search_engine;
                                continue;
                            }
                        }

                        // In case the result is not duplicated add to the list
                        $final_results[] = array(
                            'title' => $search_result['title'],
                            'url' => $search_result['url'],
                            'source' => array($search_engine)
                        );
                    }
                }
            }
        }

        return $final_results;
    }

    /**
     * Returns an assoc array with the enabled and instantiated crawler objects
     *
     * @return array
     */
    private function getEnabledCrawlerObject()
    {
        $enabled_crawler_objects = array();

        if ($this->config['google']['enabled']) {
            $enabled_crawler_objects['google'] = new GoogleCrawler($this->config);
        }

        if ($this->config['yahoo']['enabled']) {
            $enabled_crawler_objects['yahoo'] = new YahooCrawler($this->config);
        }

        if ($this->config['bing']['enabled']) {
            $enabled_crawler_objects['bing'] = new BingCrawler($this->config);
        }

        return $enabled_crawler_objects;
    }
}