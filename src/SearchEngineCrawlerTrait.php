<?php

namespace cyattilakiss\SearchResultAggregator;

use cyattilakiss\SearchResultAggregator\SearchEngineCrawlerException as SearchEngineCrawlerException;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

trait SearchEngineCrawlerTrait
{
    /**
     * Returns a random user agent from a file
     *
     * @param $user_agent_file
     * @return mixed
     * @throws \cyattilakiss\SearchResultAggregator\SearchEngineCrawlerException
     */
    private function getRandomUserAgent($user_agent_file) {

        if (!$file_contents_array = file($user_agent_file, FILE_SKIP_EMPTY_LINES)) {
            throw new SearchEngineCrawlerException('The user agents file is missing or empty');
        }

        return $file_contents_array[mt_rand(0, count($file_contents_array) - 1)];
    }

    /**
     * Returns the html body using an URL and a user agent
     *
     * @param $url
     * @param $user_agent
     * @return string
     * @throws \cyattilakiss\SearchResultAggregator\SearchEngineCrawlerException
     */
    private function getHtmlByUrl($url, $user_agent){

        $headers = array('User-Agent' => $user_agent);
        $client = new Client();
        $request = $client->get($url, $headers);

        try {
            $response = $request->send();
            return $response->getBody(true);
        } catch (ClientErrorResponseException $e) {
            throw new SearchEngineCrawlerException('Exception during crawling the HTML for url: ' . $url . ' Exception: ' . $e->getResponse()->getBody(true));
        }
    }
}