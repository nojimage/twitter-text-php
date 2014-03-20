<?php

/**
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter.Text
 */

namespace Twitter\Text;

use Twitter\Text\Autolink;
use Twitter\Text\Extractor;
use Twitter\Text\HitHighlighter;
use Twitter\Text\Validator;
use Symfony\Component\Yaml\Yaml;

/**
 * Twitter Conformance TestCase
 *
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright 2014, Mike Cochrane, Nick Pope, Takashi Nojima
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter.Text
 * @property Autolink $linker
 * @property Extractor $extractor
 * @property HitHighlighter $highlighter
 * @property Validator $validator
 */
class ConformanceTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        parent::setUp();
        $this->linker = new Autolink();
        $this->linker->setNoFollow(false)->setExternal(false)->setTarget('')
            ->setUsernameClass('tweet-url username')
            ->setListClass('tweet-url list-slug')
            ->setHashtagClass('tweet-url hashtag')
            ->setCashtagClass('tweet-url cashtag')
            ->setURLClass('');
        $this->extractor = new Extractor();
    }

    protected function tearDown()
    {
        unset($this->linker);
        parent::tearDown();
    }

    /**
     * A helper function for providers.
     *
     * @param  string $type  The test to fetch data from.
     * @param  string $test  The test to fetch data for.
     * @return  array  The test data to provide.
     */
    protected function providerHelper($type, $test)
    {
        $data = Yaml::parse(DATA . '/' . $type . '.yml');
        return isset($data['tests'][$test]) ? $data['tests'][$test] : array();
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkUsernamesProvider
     */
    public function testAutoLinkUsernames($description, $text, $expected)
    {
        $linked = $this->linker->autoLinkUsernamesAndLists($text);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     *
     */
    public function autoLinkUsernamesProvider()
    {
        return $this->providerHelper('autolink', 'usernames');
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkListsProvider
     */
    public function testAutoLinkLists($description, $text, $expected)
    {
        $linked = $this->linker->autoLinkUsernamesAndLists($text);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     *
     */
    public function autoLinkListsProvider()
    {
        return $this->providerHelper('autolink', 'lists');
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkHashtagsProvider
     */
    public function testAutoLinkHashtags($description, $text, $expected)
    {
        $linked = $this->linker->autoLinkHashtags($text);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     *
     */
    public function autoLinkHashtagsProvider()
    {
        return $this->providerHelper('autolink', 'hashtags');
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkURLsProvider
     */
    public function testAutoLinkURLs($description, $text, $expected)
    {
        $linked = $this->linker->autoLinkURLs($text);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     *
     */
    public function autoLinkURLsProvider()
    {
        return $this->providerHelper('autolink', 'urls');
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkCashtagsProvider
     */
    public function testAutoLinkCashtags($description, $text, $expected)
    {
        $linked = $this->linker->autoLinkCashtags($text);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     *
     */
    public function autoLinkCashtagsProvider()
    {
        return $this->providerHelper('autolink', 'cashtags');
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkProvider
     */
    public function testAutoLinks($description, $text, $expected)
    {
        $linked = $this->linker->autoLink($text);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     *
     */
    public function autoLinkProvider()
    {
        return $this->providerHelper('autolink', 'all');
    }

    /**
     * @group conformance
     * @group Autolink
     * @dataProvider  autoLinkWithJSONProvider
     */
    public function testAutoLinkWithJSONByObj($description, $text, $jsonText, $expected)
    {
        $jsonObj = json_decode($jsonText);

        $linked = $this->linker->autoLinkWithJson($text, $jsonObj);
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

        $linked = $this->linker->autoLinkWithJson($text, $jsonArray);
        $this->assertSame($expected, $linked, $description);
    }

    /**
     *
     */
    public function autoLinkWithJSONProvider()
    {
        return $this->providerHelper('autolink', 'json');
    }

    /**
     * @group conformance
     * @group Extractor
     * @dataProvider  extractMentionedScreennamesProvider
     */
    public function testExtractMentionedScreennames($description, $text, $expected)
    {
        $extracted = $this->extractor->extractMentionedScreennames($text);
        $this->assertSame($expected, $extracted, $description);
    }

    /**
     * @group conformance
     * @group Extractor
     * @group deprecated
     * @dataProvider  extractMentionedScreennamesProvider
     */
    public function testExtractMentionedUsernames($description, $text, $expected)
    {
        $extracted = Extractor::create($text)->extractMentionedUsernames();
        $this->assertSame($expected, $extracted, $description);
    }

    /**
     *
     */
    public function extractMentionedScreennamesProvider()
    {
        return $this->providerHelper('extract', 'mentions');
    }

    /**
     * @group conformance
     * @group Extractor
     * @dataProvider  extractMentionsWithIndicesProvider
     */
    public function testExtractMentionedScreennamesWithIndices($description, $text, $expected)
    {
        $extracted = $this->extractor->extractMentionedScreennamesWithIndices($text);
        $this->assertSame($expected, $extracted, $description);
    }

    /**
     * @group conformance
     * @group Extractor
     * @group deprecated
     * @dataProvider  extractMentionsWithIndicesProvider
     */
    public function testExtractMentionedUsernamesWithIndices($description, $text, $expected)
    {
        $extracted = Extractor::create($text)->extractMentionedUsernamesWithIndices();
        $this->assertSame($expected, $extracted, $description);
    }

    /**
     *
     */
    public function extractMentionsWithIndicesProvider()
    {
        return $this->providerHelper('extract', 'mentions_with_indices');
    }

    /**
     * @group conformance
     * @group Extractor
     * @dataProvider  extractMentionsOrListsWithIndicesProvider
     */
    public function testExtractMentionsOrListsWithIndices($description, $text, $expected)
    {
        $extracted = $this->extractor->extractMentionsOrListsWithIndices($text);
        $this->assertSame($expected, $extracted, $description);
    }

    /**
     * @group conformance
     * @group Extractor
     * @group deprecated
     * @dataProvider  extractMentionsOrListsWithIndicesProvider
     */
    public function testExtractMentionedUsernamesOrListsWithIndices($description, $text, $expected)
    {
        $extracted = Extractor::create($text)->extractMentionedUsernamesOrListsWithIndices();
        $this->assertSame($expected, $extracted, $description);
    }

    /**
     *
     */
    public function extractMentionsOrListsWithIndicesProvider()
    {
        return $this->providerHelper('extract', 'mentions_or_lists_with_indices');
    }

    /**
     * @group conformance
     * @group Extractor
     * @dataProvider  extractReplyScreennameProvider
     */
    public function testExtractReplyScreenname($description, $text, $expected)
    {
        $extracted = $this->extractor->extractReplyScreenname($text);
        $this->assertSame($expected, $extracted, $description);
    }

    /**
     * @group conformance
     * @group Extractor
     * @group deprecated
     * @dataProvider  extractReplyScreennameProvider
     */
    public function testExtractRepliedUsernames($description, $text, $expected)
    {
        $extracted = Extractor::create($text)->extractRepliedUsernames();
        $this->assertSame($expected, $extracted, $description);
    }

    /**
     *
     */
    public function extractReplyScreennameProvider()
    {
        return $this->providerHelper('extract', 'replies');
    }

    /**
     * @group conformance
     * @group Extractor
     * @dataProvider  extractURLsProvider
     */
    public function testExtractURLs($description, $text, $expected)
    {
        $extracted = $this->extractor->extractURLs($text);
        $this->assertSame($expected, $extracted, $description);
    }

    /**
     *
     */
    public function extractURLsProvider()
    {
        return $this->providerHelper('extract', 'urls');
    }

    /**
     * @group conformance
     * @group Extractor
     * @dataProvider  extractURLsWithIndicesProvider
     */
    public function testExtractURLsWithIndices($description, $text, $expected)
    {
        $extracted = $this->extractor->extractURLsWithIndices($text);
        $this->assertSame($expected, $extracted, $description);
    }

    /**
     *
     */
    public function extractURLsWithIndicesProvider()
    {
        return $this->providerHelper('extract', 'urls_with_indices');
    }

    /**
     * @group conformance
     * @group Extractor
     * @dataProvider  extractHashtagsProvider
     */
    public function testExtractHashtags($description, $text, $expected)
    {
        $extracted = $this->extractor->extractHashtags($text);
        $this->assertSame($expected, $extracted, $description);
    }

    /**
     *
     */
    public function extractHashtagsProvider()
    {
        return $this->providerHelper('extract', 'hashtags');
    }

    /**
     * @group conformance
     * @group Extractor
     * @dataProvider  extractHashtagsWithIndicesProvider
     */
    public function testExtractHashtagsWithIndices($description, $text, $expected)
    {
        $extracted = $this->extractor->extractHashtagsWithIndices($text);
        $this->assertSame($expected, $extracted, $description);
    }

    /**
     *
     */
    public function extractHashtagsWithIndicesProvider()
    {
        return $this->providerHelper('extract', 'hashtags_with_indices');
    }

    /**
     * @group conformance
     * @group Extractor
     * @dataProvider  extractCashtagsProvider
     */
    public function testExtractCashtags($description, $text, $expected)
    {
        $extracted = $this->extractor->extractCashtags($text);
        $this->assertSame($expected, $extracted, $description);
    }

    /**
     *
     */
    public function extractCashtagsProvider()
    {
        return $this->providerHelper('extract', 'cashtags');
    }

    /**
     * @group conformance
     * @group Extractor
     * @dataProvider  extractCashtagsWithIndicesProvider
     */
    public function testExtractCashtagsWithIndices($description, $text, $expected)
    {
        $extracted = $this->extractor->extractCashtagsWithIndices($text);
        $this->assertSame($expected, $extracted, $description);
    }

    /**
     *
     */
    public function extractCashtagsWithIndicesProvider()
    {
        return $this->providerHelper('extract', 'cashtags_with_indices');
    }
}
