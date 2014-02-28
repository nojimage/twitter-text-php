<?php
/**
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter
 */
use Symfony\Component\Yaml\Yaml;

/**
 * Twitter Autolink Class Unit Tests
 *
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter
 * @property Twiiter_Autolink $linker
 */
class Twitter_AutolinkTest extends PHPUnit_Framework_TestCase {

  protected function setUp() {
    parent::setUp();
    $this->linker = new Twitter_Autolink();
  }

  protected function tearDown() {
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
  protected function providerHelper($test) {
    $data = Yaml::parse(DATA.'/autolink.yml');
    return isset($data['tests'][$test]) ? $data['tests'][$test] : array();
  }

  /**
   * @dataProvider  autoLinkUsernamesProvider
   */
  public function testAutolinkUsernames($description, $text, $expected) {
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
   * @dataProvider  autoLinkUsernamesProvider
   */
  public function testAddLinksToUsernames($description, $text, $expected) {
    $linked = Twitter_Autolink::create($text)
      ->setNoFollow(false)->setExternal(false)->setTarget('')
      ->setUsernameClass('tweet-url username')
      ->setListClass('tweet-url list-slug')
      ->setHashtagClass('tweet-url hashtag')
      ->setCashtagClass('tweet-url cashtag')
      ->setURLClass('')
      ->addLinksToUsernamesAndLists(true);
    $this->assertSame($expected, $linked, $description);
  }

  /**
   *
   */
  public function autoLinkUsernamesProvider() {
    return $this->providerHelper('usernames');
  }

  /**
   * @dataProvider  autoLinkListsProvider
   */
  public function testAutoLinkLists($description, $text, $expected) {
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
   * @dataProvider  autoLinkListsProvider
   */
  public function testAddLinksToLists($description, $text, $expected) {
    $linked = Twitter_Autolink::create($text)
      ->setNoFollow(false)->setExternal(false)->setTarget('')
      ->setUsernameClass('tweet-url username')
      ->setListClass('tweet-url list-slug')
      ->setHashtagClass('tweet-url hashtag')
      ->setCashtagClass('tweet-url cashtag')
      ->setURLClass('')
      ->addLinksToUsernamesAndLists(true);
    $this->assertSame($expected, $linked, $description);
  }

  /**
   *
   */
  public function autoLinkListsProvider() {
    return $this->providerHelper('lists');
  }

  /**
   * @dataProvider  autoLinkHashtagsProvider
   */
  public function testAutoLinkHashtags($description, $text, $expected) {
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
   * @dataProvider  autoLinkHashtagsProvider
   */
  public function testAddLinksToHashtags($description, $text, $expected) {
    $linked = Twitter_Autolink::create($text)
      ->setNoFollow(false)->setExternal(false)->setTarget('')
      ->setUsernameClass('tweet-url username')
      ->setListClass('tweet-url list-slug')
      ->setHashtagClass('tweet-url hashtag')
      ->setCashtagClass('tweet-url cashtag')
      ->setURLClass('')
      ->addLinksToHashtags(true);
    $this->assertSame($expected, $linked, $description);
  }

  /**
   *
   */
  public function autoLinkHashtagsProvider() {
    return $this->providerHelper('hashtags');
  }

  /**
   * @dataProvider  autoLinkCashtagsProvider
   */
  public function testAutoLinkCashtags($description, $text, $expected) {
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
   * @dataProvider  autoLinkCashtagsProvider
   */
  public function testAddLinksToCashtags($description, $text, $expected) {
    $linked = Twitter_Autolink::create($text)
      ->setNoFollow(false)->setExternal(false)->setTarget('')
      ->setUsernameClass('tweet-url username')
      ->setListClass('tweet-url list-slug')
      ->setHashtagClass('tweet-url hashtag')
      ->setCashtagClass('tweet-url cashtag')
      ->setURLClass('')
      ->addLinksToCashtags(true);
    $this->assertSame($expected, $linked, $description);
  }

  /**
   *
   */
  public function autoLinkCashtagsProvider() {
    return $this->providerHelper('cashtags');
  }

  /**
   * @dataProvider  autoLinkURLsProvider
   */
  public function testAutoLinkURLs($description, $text, $expected) {
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
   * @dataProvider  autoLinkURLsProvider
   */
  public function testAddLinksToURLs($description, $text, $expected) {
    $linked = Twitter_Autolink::create($text)
      ->setNoFollow(false)->setExternal(false)->setTarget('')
      ->setUsernameClass('tweet-url username')
      ->setListClass('tweet-url list-slug')
      ->setHashtagClass('tweet-url hashtag')
      ->setCashtagClass('tweet-url cashtag')
      ->setURLClass('')
      ->addLinksToURLs(true);
    $this->assertSame($expected, $linked, $description);
  }

  /**
   *
   */
  public function autoLinkURLsProvider() {
    return $this->providerHelper('urls');
  }

  /**
   * @dataProvider  autoLinkProvider
   */
  public function testAutoLinks($description, $text, $expected) {
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
   * @dataProvider  autoLinkProvider
   */
  public function testAddLinks($description, $text, $expected) {
    $linked = Twitter_Autolink::create($text)
      ->setNoFollow(false)->setExternal(false)->setTarget('')
      ->setUsernameClass('tweet-url username')
      ->setListClass('tweet-url list-slug')
      ->setHashtagClass('tweet-url hashtag')
      ->setCashtagClass('tweet-url cashtag')
      ->setURLClass('')
      ->addLinks();
    $this->assertSame($expected, $linked, $description);
  }

  /**
   *
   */
  public function autoLinkProvider() {
    return $this->providerHelper('all');
  }

  /**
   * @dataProvider  autoLinkWithJSONProvider
   */
  public function testAutoLinkWithJSONByObj($description, $text, $jsonText, $expected) {
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
   * @dataProvider  autoLinkWithJSONProvider
   */
  public function testAutoLinkWithJSONByArray($description, $text, $jsonText, $expected) {
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
  public function autoLinkWithJSONProvider() {
    return $this->providerHelper('json');
  }

  /**
   * Check the addLinks method pass to legacy methods on loose mode
   *
   * @covers Twitter_Autolink::addLinks
   */
  public function testAddLinksWithLooseOption() {
    $this->linker = $this->getMock('Twitter_Autolink', array(
        'autoLinkURLs',
        'autoLinkHashtags',
        'autoLinkCashtags',
        'autoLinkUsernamesAndLists',
    ), array(
        'some tweet #hashtag http://example.com'
    ));

    $this->linker->expects($this->never())->method('autoLinkURLs');
    $this->linker->expects($this->never())->method('autoLinkHashtags');
    $this->linker->expects($this->never())->method('autoLinkCashtags');
    $this->linker->expects($this->never())->method('autoLinkUsernamesAndLists');
    $this->linker->addLinks(true);
  }

}

################################################################################
# vim:et:ft=php:nowrap:sts=2:sw=2:ts=2
