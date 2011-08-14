<?php
/**
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter
 */

require_once 'Regex.php';

/**
 * Twitter Validator Class
 *
 * Performs "validation" on tweets.
 *
 * Originally written by {@link http://github.com/mikenz Mike Cochrane}, this
 * is based on code by {@link http://github.com/mzsanford Matt Sanford} and
 * heavily modified by {@link http://github.com/ngnpope Nick Pope}.
 *
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter
 */
class Twitter_Validation extends Twitter_Regex {

  /**
   *
   */
  const MAX_LENGTH = 140;

  /**
   * Provides fluent method chaining.
   *
   * @param  string  $tweet  The tweet to be validated.
   *
   * @see  __construct()
   *
   * @return  Twitter_Validation
   */
  public static function create($tweet) {
    return new self($tweet);
  }

  /**
   * Reads in a tweet to be parsed and validates it.
   *
   * @param  string  $tweet  The tweet to validate.
   */
  public function __construct($tweet) {
      parent::__construct($tweet);
  }

  /**
   *
   */
  public function validateTweet() {
    $length = mb_strlen($this->tweet);
    if (!$this->tweet || !$length) return false;
    if ($length > self::MAX_LENGTH) return false;
    if (preg_match(self::$patterns['invalid_characters'], $this->tweet)) return false;
    return true;
  }

  /**
   *
   */
  public function validateUsername() {
    $length = mb_strlen($this->tweet);
    if (!$this->tweet || !$length) return false;
    $extracted = Twitter_Extractor::create($this->tweet)->extractMentionedUsernames();
    return count($extracted) === 1 && $extracted[0] === substr($this->tweet, 1);
  }

  /**
   *
   */
  public function validateList() {
    $length = mb_strlen($this->tweet);
    if (!$this->tweet || !$length) return false;
    preg_match(self::$patterns['auto_link_usernames_or_lists'], $this->tweet, $matches);
    return isset($matches) && $matches[1] === '' && $matches[4] && mb_strlen($matches[4]);
  }

  /**
   *
   */
  public function validateHashtag() {
    $length = mb_strlen($this->tweet);
    if (!$this->tweet || !$length) return false;
    $extracted = Twitter_Extractor::create($this->tweet)->extractHashtags();
    return count($extracted) === 1 && $extracted[0] === substr($this->tweet, 1);
  }

  /**
   *
   */
  public function validateURL($unicode = true) {
    $length = mb_strlen($this->tweet);
    if (!$this->tweet || !$length) return false;
    preg_match(self::$patterns['validate_url_unencoded'], $this->tweet, $matches);
    $match = array_shift($matches);
    if (!$matches || $match !== $this->tweet) return false;
    list($scheme, $authority, $path, $query, $fragment) = array_pad($matches, 5, '');
    # Check scheme:
    if (!preg_match(self::$patterns['validate_url_scheme'], $scheme)) return false;
    if (!preg_match('/^https?$/i', $scheme)) return false;
    # Check path:
    if (!preg_match(self::$patterns['validate_url_path'], $path)) return false;
    # Check query:
    if (!preg_match(self::$patterns['validate_url_query'], $query)) return false;
    # Check fragment:
    if ($fragment) {
      if (!preg_match(self::$patterns['validate_url_fragment'], $fragment)) return false;
    }
    # Check authority:
    if ($unicode) {
      if (!preg_match(self::$patterns['validate_url_unicode_authority'], $authority)) return false;
    } else {
      if (!preg_match(self::$patterns['validate_url_authority'], $authority)) return false;
    }
    return true;
  }

}

################################################################################
# vim:et:ft=php:nowrap:sts=2:sw=2:ts=2
