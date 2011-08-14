<?php
/**
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter
 */

/**
 * Twitter Validation Class Unit Tests
 *
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter
 */
class Twitter_ValidationTest extends PHPUnit_Framework_TestCase {

  /**
   * A helper function for providers.
   *
   * @param  string  $test  The test to fetch data for.
   *
   * @return  array  The test data to provide.
   */
  protected function providerHelper($test) {
    $data = Spyc::YAMLLoad(DATA.'/validate.yml');
    return isset($data['tests'][$test]) ? $data['tests'][$test] : array();
  }

  /**
   * @dataProvider  validateTweetProvider
   */
  public function testValidateTweet($description, $text, $expected) {
    $validated = Twitter_Validation::create($text)->validateTweet();
    $this->assertSame($expected, $validated, $description);
  }

  /**
   *
   */
  public function validateTweetProvider() {
    return $this->providerHelper('tweets');
  }

  /**
   * @dataProvider  validateUsernameProvider
   */
  public function testValidateUsername($description, $text, $expected) {
    $validated = Twitter_Validation::create($text)->validateUsername();
    $this->assertSame($expected, $validated, $description);
  }

  /**
   *
   */
  public function validateUsernameProvider() {
    return $this->providerHelper('usernames');
  }

  /**
   * @dataProvider  validateListProvider
   */
  public function testValidateList($description, $text, $expected) {
    $validated = Twitter_Validation::create($text)->validateList();
    $this->assertSame($expected, $validated, $description);
  }

  /**
   *
   */
  public function validateListProvider() {
    return $this->providerHelper('lists');
  }

  /**
   * @dataProvider  validateHashtagProvider
   */
  public function testValidateHashtag($description, $text, $expected) {
    $validated = Twitter_Validation::create($text)->validateHashtag();
    $this->assertSame($expected, $validated, $description);
  }

  /**
   *
   */
  public function validateHashtagProvider() {
    return $this->providerHelper('hashtags');
  }

  /**
   * @dataProvider  validateURLProvider
   */
  public function testValidateURL($description, $text, $expected) {
    $validated = Twitter_Validation::create($text)->validateURL();
    $this->assertSame($expected, $validated, $description);
  }

  /**
   *
   */
  public function validateURLProvider() {
    return $this->providerHelper('urls');
  }

}

################################################################################
# vim:et:ft=php:nowrap:sts=2:sw=2:ts=2
