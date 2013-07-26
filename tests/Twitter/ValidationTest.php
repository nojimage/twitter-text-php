<?php
/**
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter
 */
use Symfony\Component\Yaml\Yaml;

/**
 * Twitter Validation Class Unit Tests
 *
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter
 * @property   Twitter_Validation $validator
 */
class Twitter_ValidationTest extends PHPUnit_Framework_TestCase {

  protected function setUp() {
    parent::setUp();
    $this->validator = new Twitter_Validation();
  }

  protected function tearDown() {
    unset($this->validator);
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
    $data = Yaml::parse(DATA.'/validate.yml');
    return isset($data['tests'][$test]) ? $data['tests'][$test] : array();
  }

  public function testConfiglationFromArray() {
    $validator = Twitter_Validation::create('', array(
        'short_url_length' => 22,
        'short_url_length_https' => 23,
    ));
    $this->assertSame(22, $validator->getShortUrlLength());
    $this->assertSame(23, $validator->getShortUrlLengthHttps());
  }

  public function testConfiglationFromObject() {
    $conf = new stdClass();
    $conf->short_url_length = 22;
    $conf->short_url_length_https = 23;
    $validator = Twitter_Validation::create('', $conf);
    $this->assertSame(22, $validator->getShortUrlLength());
    $this->assertSame(23, $validator->getShortUrlLengthHttps());
  }

  /**
   * @dataProvider  isValidTweetTextProvider
   */
  public function testIsValidTweetText($description, $text, $expected) {
    $validated = $this->validator->isValidTweetText($text);
    $this->assertSame($expected, $validated, $description);
  }

  /**
   * @dataProvider  isValidTweetTextProvider
   */
  public function testValidateTweet($description, $text, $expected) {
    $validated = Twitter_Validation::create($text)->validateTweet();
    $this->assertSame($expected, $validated, $description);
  }

  /**
   *
   */
  public function isValidTweetTextProvider() {
    return $this->providerHelper('tweets');
  }

  /**
   * @dataProvider  isValidUsernameProvider
   */
  public function testIsValidUsername($description, $text, $expected) {
    $validated = $this->validator->isValidUsername($text);
    $this->assertSame($expected, $validated, $description);
  }

  /**
   * @dataProvider  isValidUsernameProvider
   */
  public function testValidateUsername($description, $text, $expected) {
    $validated = Twitter_Validation::create($text)->validateUsername();
    $this->assertSame($expected, $validated, $description);
  }

  /**
   *
   */
  public function isValidUsernameProvider() {
    return $this->providerHelper('usernames');
  }

  /**
   * @dataProvider  isValidListProvider
   */
  public function testIsValidList($description, $text, $expected) {
    $validated = $this->validator->isValidList($text);
    $this->assertSame($expected, $validated, $description);
  }

  /**
   * @dataProvider  isValidListProvider
   */
  public function testValidateList($description, $text, $expected) {
    $validated = Twitter_Validation::create($text)->validateList();
    $this->assertSame($expected, $validated, $description);
  }

  /**
   *
   */
  public function isValidListProvider() {
    return $this->providerHelper('lists');
  }

  /**
   * @dataProvider  isValidHashtagProvider
   */
  public function testIsValidHashtag($description, $text, $expected) {
    $validated =$this->validator->isValidHashtag($text);
    $this->assertSame($expected, $validated, $description);
  }

  /**
   * @dataProvider  isValidHashtagProvider
   */
  public function testValidateHashtag($description, $text, $expected) {
    $validated = Twitter_Validation::create($text)->validateHashtag();
    $this->assertSame($expected, $validated, $description);
  }

  /**
   *
   */
  public function isValidHashtagProvider() {
    return $this->providerHelper('hashtags');
  }

  /**
   * @dataProvider  isValidURLProvider
   */
  public function testIsValidURL($description, $text, $expected) {
    $validated = $this->validator->isValidURL($text);
    $this->assertSame($expected, $validated, $description);
  }

  /**
   * @dataProvider  isValidURLProvider
   */
  public function testValidateURL($description, $text, $expected) {
    $validated = Twitter_Validation::create($text)->validateURL();
    $this->assertSame($expected, $validated, $description);
  }

  /**
   *
   */
  public function isValidURLProvider() {
    return $this->providerHelper('urls');
  }

  /**
   * @dataProvider  isValidURLWithoutProtocolProvider
   */
  public function testIsValidURLWithoutProtocol($description, $text, $expected) {
    $validated = $this->validator->isValidURL($text, true, false);
    $this->assertSame($expected, $validated, $description);
  }

  /**
   *
   */
  public function isValidURLWithoutProtocolProvider() {
    return $this->providerHelper('urls_without_protocol');
  }

  /**
   * @dataProvider  getTweetLengthProvider
   */
  public function testGetTweetLength($description, $text, $expected) {
    $validated = $this->validator->getTweetLength($text);
    $this->assertSame($expected, $validated, $description);
  }

  /**
   * @dataProvider  getTweetLengthProvider
   */
  public function testGetLength($description, $text, $expected) {
    $validated = Twitter_Validation::create($text)->getLength();
    $this->assertSame($expected, $validated, $description);
  }

  /**
   *
   */
  public function getTweetLengthProvider() {
    return $this->providerHelper('lengths');
  }

}

################################################################################
# vim:et:ft=php:nowrap:sts=2:sw=2:ts=2
