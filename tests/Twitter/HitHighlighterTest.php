<?php
/**
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter
 */
use Symfony\Component\Yaml\Yaml;

/**
 * Twitter HitHighlighter Class Unit Tests
 *
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter
 * @property   Twitter_HitHighlighter $highlighter
 */
class Twitter_HitHighlighterTest extends PHPUnit_Framework_TestCase {

  protected function setUp() {
    parent::setUp();
    $this->highlighter = new Twitter_HitHighlighter();
  }

  protected function tearDown() {
    unset($this->highlighter);
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
    $data = Yaml::parse(DATA.'/hit_highlighting.yml');
    return isset($data['tests'][$test]) ? $data['tests'][$test] : array();
  }

  /**
   * @dataProvider  highlightProvider
   */
  public function testHighlight($description, $text, $hits, $expected) {
    $extracted = $this->highlighter->highlight($text, $hits);
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   * @dataProvider  highlightProvider
   */
  public function testAddHitHighlighting($description, $text, $hits, $expected) {
    $extracted = Twitter_HitHighlighter::create($text)->addHitHighlighting($hits);
    $this->assertSame($expected, $extracted, $description);
  }

  /**
   *
   */
  public function highlightProvider() {
    return array_merge($this->providerHelper('plain_text'), $this->providerHelper('with_links'));
  }

}

################################################################################
# vim:et:ft=php:nowrap:sts=2:sw=2:ts=2
