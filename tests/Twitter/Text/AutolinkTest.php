<?php

/**
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter.Text
 */

namespace Twitter\Text;

use Twitter\Text\Autolink;
use Symfony\Component\Yaml\Yaml;

/**
 * Twitter Autolink Class Unit Tests
 *
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter.Text
 * @property Autolink $linker
 */
class AutolinkTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        parent::setUp();
        $this->linker = new Autolink();
    }

    protected function tearDown()
    {
        unset($this->linker);
        parent::tearDown();
    }

    /**
     * A helper function for providers.
     *
     * @param  string  $test  The test to fetch data for.
     *
     * @return  array  The test data to provide.
     */
    protected function providerHelper($test)
    {
        $data = Yaml::parse(DATA . '/autolink.yml');
        return isset($data['tests'][$test]) ? $data['tests'][$test] : array();
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkUsernamesProvider
     */
    public function testAutolinkUsernames($description, $text, $expected)
    {
        $linked = $this->linker
            ->setNoFollow(false)->setExternal(false)->setTarget('')
            ->setUsernameClass('tweet-url username')
            ->setListClass('tweet-url list-slug')
            ->setHashtagClass('tweet-url hashtag')
            ->setCashtagClass('tweet-url cashtag')
            ->setURLClass('')
            ->autoLinkUsernamesAndLists($text);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     *
     */
    public function autoLinkUsernamesProvider()
    {
        return $this->providerHelper('usernames');
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkListsProvider
     */
    public function testAutoLinkLists($description, $text, $expected)
    {
        $linked = $this->linker
            ->setNoFollow(false)->setExternal(false)->setTarget('')
            ->setUsernameClass('tweet-url username')
            ->setListClass('tweet-url list-slug')
            ->setHashtagClass('tweet-url hashtag')
            ->setCashtagClass('tweet-url cashtag')
            ->setURLClass('')
            ->autoLinkUsernamesAndLists($text);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     *
     */
    public function autoLinkListsProvider()
    {
        return $this->providerHelper('lists');
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkHashtagsProvider
     */
    public function testAutoLinkHashtags($description, $text, $expected)
    {
        $linked = $this->linker
            ->setNoFollow(false)->setExternal(false)->setTarget('')
            ->setUsernameClass('tweet-url username')
            ->setListClass('tweet-url list-slug')
            ->setHashtagClass('tweet-url hashtag')
            ->setCashtagClass('tweet-url cashtag')
            ->setURLClass('')
            ->autoLinkHashtags($text);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     *
     */
    public function autoLinkHashtagsProvider()
    {
        return $this->providerHelper('hashtags');
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkCashtagsProvider
     */
    public function testAutoLinkCashtags($description, $text, $expected)
    {
        $linked = $this->linker
            ->setNoFollow(false)->setExternal(false)->setTarget('')
            ->setUsernameClass('tweet-url username')
            ->setListClass('tweet-url list-slug')
            ->setHashtagClass('tweet-url hashtag')
            ->setCashtagClass('tweet-url cashtag')
            ->setURLClass('')
            ->autoLinkCashtags($text);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     *
     */
    public function autoLinkCashtagsProvider()
    {
        return $this->providerHelper('cashtags');
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkURLsProvider
     */
    public function testAutoLinkURLs($description, $text, $expected)
    {
        $linked = $this->linker
            ->setNoFollow(false)->setExternal(false)->setTarget('')
            ->setUsernameClass('tweet-url username')
            ->setListClass('tweet-url list-slug')
            ->setHashtagClass('tweet-url hashtag')
            ->setCashtagClass('tweet-url cashtag')
            ->setURLClass('')
            ->autoLinkURLs($text);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     *
     */
    public function autoLinkURLsProvider()
    {
        return $this->providerHelper('urls');
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkProvider
     */
    public function testAutoLinks($description, $text, $expected)
    {
        $linked = $this->linker
            ->setNoFollow(false)->setExternal(false)->setTarget('')
            ->setUsernameClass('tweet-url username')
            ->setListClass('tweet-url list-slug')
            ->setHashtagClass('tweet-url hashtag')
            ->setCashtagClass('tweet-url cashtag')
            ->setURLClass('')
            ->autoLink($text);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     *
     */
    public function autoLinkProvider()
    {
        return $this->providerHelper('all');
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkWithJSONProvider
     */
    public function testAutoLinkWithJSONByObj($description, $text, $jsonText, $expected)
    {
        $jsonObj = json_decode($jsonText);

        $linked = $this->linker
            ->setNoFollow(false)->setExternal(false)->setTarget('')
            ->setUsernameClass('tweet-url username')
            ->setListClass('tweet-url list-slug')
            ->setHashtagClass('tweet-url hashtag')
            ->setCashtagClass('tweet-url cashtag')
            ->setURLClass('')
            ->autoLinkWithJson($text, $jsonObj);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkWithJSONProvider
     */
    public function testAutoLinkWithJSONByArray($description, $text, $jsonText, $expected)
    {
        $jsonArray = json_decode($jsonText, true);

        $linked = $this->linker
            ->setNoFollow(false)->setExternal(false)->setTarget('')
            ->setUsernameClass('tweet-url username')
            ->setListClass('tweet-url list-slug')
            ->setHashtagClass('tweet-url hashtag')
            ->setCashtagClass('tweet-url cashtag')
            ->setURLClass('')
            ->autoLinkWithJson($text, $jsonArray);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     *
     */
    public function autoLinkWithJSONProvider()
    {
        return $this->providerHelper('json');
    }

}
