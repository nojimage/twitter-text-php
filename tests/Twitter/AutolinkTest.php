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
 */
class Twitter_AutolinkTest extends PHPUnit_Framework_TestCase {

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
    $linked = Twitter_Autolink::create($text)
      ->setNoFollow(false)->setExternal(false)->setTarget('')
      ->setUsernameClass('tweet-url username')
      ->setListClass('tweet-url list-slug')
      ->setHashtagClass('tweet-url hashtag')
      ->setCashtagClass('tweet-url cashtag')
      ->setURLClass('')
      ->autoLinkUsernamesAndLists();
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
    $linked = Twitter_Autolink::create($text)
      ->setNoFollow(false)->setExternal(false)->setTarget('')
      ->setUsernameClass('tweet-url username')
      ->setListClass('tweet-url list-slug')
      ->setHashtagClass('tweet-url hashtag')
      ->setCashtagClass('tweet-url cashtag')
      ->setURLClass('')
      ->autoLinkUsernamesAndLists();
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
    $linked = Twitter_Autolink::create($text)
      ->setNoFollow(false)->setExternal(false)->setTarget('')
      ->setUsernameClass('tweet-url username')
      ->setListClass('tweet-url list-slug')
      ->setHashtagClass('tweet-url hashtag')
      ->setCashtagClass('tweet-url cashtag')
      ->setURLClass('')
      ->autoLinkHashtags();
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
    $linked = Twitter_Autolink::create($text)
      ->setNoFollow(false)->setExternal(false)->setTarget('')
      ->setUsernameClass('tweet-url username')
      ->setListClass('tweet-url list-slug')
      ->setHashtagClass('tweet-url hashtag')
      ->setCashtagClass('tweet-url cashtag')
      ->setURLClass('')
      ->autoLinkCashtags();
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
    $linked = Twitter_Autolink::create($text)
      ->setNoFollow(false)->setExternal(false)->setTarget('')
      ->setUsernameClass('tweet-url username')
      ->setListClass('tweet-url list-slug')
      ->setHashtagClass('tweet-url hashtag')
      ->setCashtagClass('tweet-url cashtag')
      ->setURLClass('')
      ->autoLinkURLs();
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
    $linked = Twitter_Autolink::create($text)
      ->setNoFollow(false)->setExternal(false)->setTarget('')
      ->setUsernameClass('tweet-url username')
      ->setListClass('tweet-url list-slug')
      ->setHashtagClass('tweet-url hashtag')
      ->setCashtagClass('tweet-url cashtag')
      ->setURLClass('')
      ->autoLink();
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

}

################################################################################
# vim:et:ft=php:nowrap:sts=2:sw=2:ts=2
