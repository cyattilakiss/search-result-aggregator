<?php

namespace cyattilakiss\SearchResultAggregator;

use Symfony\Component\DomCrawler\Crawler;

class GoogleCrawler implements SearchEngineCrawlerInterface
{
    use SearchEngineCrawlerTrait;

    const URL_STRUCTURE = 'https://www.google.com/search?q=%query%&hl=%language%&num=%num%&start=%start%';

    private $config = array();

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param string $query
     * @param string $language
     * @param int $num
     * @param int $page
     * @return array
     * @throws SearchEngineCrawlerException
     */
    public function getResults($query, $language = 'en', $num = 10, $page = 1)
    {
        // set up url
        $url = $this->getUrl($query, $language, $num, $page);

        // get html by url with user agent
        $html_body = $this->getHtmlByUrl($url, $this->getRandomUserAgent($this->config['user_agents_txt']));

        // parse html and get the required data
        return ($this->getRequiredInformationFromHtmlBody($html_body));
    }

    /**
     * Creates the url for Google search with the query parameters
     *
     * @param string    $query
     * @param string    $language
     * @param int       $num
     * @param int       $page
     * @return string
     */
    private function getUrl($query, $language, $num, $page)
    {
        // prepare the query key value pairs (keys must be the same as in the self::URL_STRUCTURE)
        $key_value_array = array(
            'query' => $query,
            'language' => $language,
            'num' => $num,
            'start' => ($page - 1) * $num
        );

        return SearchEngineCrawlerHelper::replaceKeysToValuesInString($key_value_array, self::URL_STRUCTURE);
    }

    /**
     * @param $html_body
     * @return array
     */
    private function getRequiredInformationFromHtmlBody($html_body)
    {
        $formatted_data_array = array();

        foreach ($this->getResultListElementGenerator($html_body) as $item) {
            if ($temp_array = $this->getResultsFromListElement($item)) {
                $formatted_data_array[] = $temp_array;
            }
        }

        return $formatted_data_array;
    }

    /**
     * Search for the list elements in the html body
     *
     * @param $html_body
     * @return Crawler
     */
    private function getResultListElementGenerator($html_body)
    {
        $crawler = new Crawler($html_body);
        $filter = 'div.g';
        return $crawler->filter($filter);
    }

    /**
     * Get requested information ['title', 'url'] from a list element of the google search result list
     *
     * @param $item
     * @return array|bool
     */
    private function getResultsFromListElement($item)
    {
        $crawler = new Crawler($item);
        $node = $crawler->filter('h3.r a')->first();

        try {
            return array(
                'title' => $node->text(),
                'url' => $node->attr('href')
            );
        } catch (\InvalidArgumentException $e) {
            // Should be logged since it's an indicator for a change in the search engine html output
            return false;
        }
    }
}