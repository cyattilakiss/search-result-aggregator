<?php

use PHPUnit\Framework\TestCase;
use \cyattilakiss\SearchResultAggregator\GoogleCrawler;

class GoogleCrawlerTest extends TestCase
{
    /**
     * @dataProvider getResultsFromListElementItemProvider
     *
     * @param $item
     * @param $expected
     */
    public function testGetResultsFromListElement($item, $expected)
    {
        $google_crawler = new GoogleCrawler(array());
        $this->assertSame($expected, $this->invokeMethod($google_crawler, 'getResultsFromListElement', array($item)));
    }

    public function getResultsFromListElementItemProvider()
    {
        return [
            'simple structure expects array' =>['<h3 class="r"><a href="href teszt url">a text</a></h3>', array('title' => 'a text', 'url' => 'href teszt url') ],
            'simple structure empty title and url  expects array' =>['<h3 class="r"><a href=""></a></h3>', array('title' => '', 'url' => '') ],
            'complex structure  expects array' =>['<div><div><h3 class="r"><div><a href="href teszt url">a text</a></div></h3></div></div>', array('title' => 'a text', 'url' => 'href teszt url') ],
            'complex structure with 2 applicable list element  expects array' =>['<div><div><h3 class="r"><div><a href="href teszt url">a text</a></div></h3></div></div><h3 class="r"><div><a href="href teszt url 2">a text 2</a></div></h3>', array('title' => 'a text', 'url' => 'href teszt url') ],
            'simple structure with 0 applicable list element expects false' =>['<h3 class="bad-class"><a href="href teszt url">a text</a></h3>', false],
            'empty structure expects false' =>[-1, false]
        ];
    }

    public function testGetResultListElementGenerator()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testGetRequiredInformationFromHtmlBody()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testGetUrl()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}