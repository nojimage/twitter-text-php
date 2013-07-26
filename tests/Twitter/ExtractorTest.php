<?php
/**
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter
 */
use Symfony\Component\Yaml\Yaml;

/**
 * Twitter Extractor Class Unit Tests
 *
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter
 * @param      Twitter_Extractor $extractor
 */
class Twitter_ExtractorTest extends PHPUnit_Framework_TestCase {

  protected function setUp() {
    parent::setUp();
    $this->extractor = Twitter_Extractor::create();
  }

  protected function tearDown() {
    unset($this->extractor);
    parent::tearDown();
  }

  /**
   * A helper function for providers.
   *
   * @param  string  $test  The test to fetch data for.
   *
   * @return  array  The test data to provide.
   */
  protected function providerHelper($test) {
    $data = Yaml::parse(DATA.'/extract.yml');
    return isset($data['tests'][$test]) ? $data['tests'][$test] : array();
  }

  /**
   * @dataProvider  extractMentionedScreennamesProvider
   */
  public function testExtractMentionedScreennames($description, $text, $expected) {
    $extracted = $this->extractor->extractMentionedScreennames($text);
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   * @dataProvider  extractMentionedScreennamesProvider
   */
  public function testExtractMentionedUsernames($description, $text, $expected) {
    $extracted = Twitter_Extractor::create($text)->extractMentionedUsernames();
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   *
   */
  public function extractMentionedScreennamesProvider() {
    return $this->providerHelper('mentions');
  }

  /**
   * @dataProvider  extractReplyScreennameProvider
   */
  public function testExtractReplyScreenname($description, $text, $expected) {
    $extracted = $this->extractor->extractReplyScreenname($text);
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   * @dataProvider  extractReplyScreennameProvider
   */
  public function testExtractRepliedUsernames($description, $text, $expected) {
    $extracted = Twitter_Extractor::create($text)->extractRepliedUsernames();
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   *
   */
  public function extractReplyScreennameProvider() {
    return $this->providerHelper('replies');
  }

  /**
   * @dataProvider  extractURLsProvider
   */
  public function testExtractURLs($description, $text, $expected) {
    $extracted = $this->extractor->extractURLs($text);
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   *
   */
  public function extractURLsProvider() {
    return $this->providerHelper('urls');
  }

  /**
   * @dataProvider  extractHashtagsProvider
   */
  public function testExtractHashtags($description, $text, $expected) {
    $extracted = $this->extractor->extractHashtags($text);
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   *
   */
  public function extractHashtagsProvider() {
    return $this->providerHelper('hashtags');
  }

  /**
   * @dataProvider  extractHashtagsWithIndicesProvider
   */
  public function testExtractHashtagsWithIndices($description, $text, $expected) {
    $extracted = $this->extractor->extractHashtagsWithIndices($text);
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   *
   */
  public function extractHashtagsWithIndicesProvider() {
    return $this->providerHelper('hashtags_with_indices');
  }

  /**
   * @dataProvider  extractCashtagsProvider
   */
  public function testExtractCashtags($description, $text, $expected) {
    $extracted = $this->extractor->extractCashtags($text);
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   *
   */
  public function extractCashtagsProvider() {
    return $this->providerHelper('cashtags');
  }

  /**
   * @dataProvider  extractCashtagsWithIndicesProvider
   */
  public function testExtractCashtagsWithIndices($description, $text, $expected) {
    $extracted = $this->extractor->extractCashtagsWithIndices($text);
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   *
   */
  public function extractCashtagsWithIndicesProvider() {
    return $this->providerHelper('cashtags_with_indices');
  }

  /**
   * @dataProvider  extractURLsWithIndicesProvider
   */
  public function testExtractURLsWithIndices($description, $text, $expected) {
    $extracted = $this->extractor->extractURLsWithIndices($text);
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   *
   */
  public function extractURLsWithIndicesProvider() {
    return $this->providerHelper('urls_with_indices');
  }

  /**
   * @dataProvider  extractMentionedScreennamesWithIndicesProvider
   */
  public function testExtractMentionedScreennamesWithIndices($description, $text, $expected) {
    $extracted = $this->extractor->extractMentionedScreennamesWithIndices($text);
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   * @dataProvider  extractMentionedScreennamesWithIndicesProvider
   */
  public function testExtractMentionedUsernamesWithIndices($description, $text, $expected) {
    $extracted = Twitter_Extractor::create($text)->extractMentionedUsernamesWithIndices();
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   *
   */
  public function extractMentionedScreennamesWithIndicesProvider() {
    return $this->providerHelper('mentions_with_indices');
  }

  /**
   * @dataProvider  extractMentionsOrListsWithIndicesProvider
   */
  public function testExtractMentionsOrListsWithIndices($description, $text, $expected) {
    $extracted = $this->extractor->extractMentionsOrListsWithIndices($text);
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   * @dataProvider  extractMentionsOrListsWithIndicesProvider
   */
  public function testExtractMentionedUsernamesOrListsWithIndices($description, $text, $expected) {
    $extracted = Twitter_Extractor::create($text)->extractMentionedUsernamesOrListsWithIndices();
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   *
   */
  public function extractMentionsOrListsWithIndicesProvider() {
    return $this->providerHelper('mentions_or_lists_with_indices');
  }

  public function testExtractURLsWithoutProtocol() {
    $extracted = Twitter_Extractor::create('text: example.com http://foobar.example.com')->extractUrlWithoutProtocol(false)->extractURLs();
    $this->assertSame(array('http://foobar.example.com'), $extracted, 'Unextract url without protocol');
  }

  public function testExtractURLsWithIndicesWithoutProtocol() {
    $extracted = Twitter_Extractor::create('text: example.com')->extractUrlWithoutProtocol(false)->extractURLsWithIndices();
    $this->assertSame(array(), $extracted, 'Unextract url without protocol');
  }
}

################################################################################
# vim:et:ft=php:nowrap:sts=2:sw=2:ts=2
