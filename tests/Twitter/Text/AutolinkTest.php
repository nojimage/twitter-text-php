<?php

/**
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter.Text
 */

namespace Twitter\Text;

use PHPUnit\Framework\TestCase;
use Twitter\Text\Autolink;

/**
 * Twitter Autolink Class Unit Tests
 *
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter.Text
 * @property Autolink $linker
 */
class AutolinkTest extends TestCase
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

    public function testCreate()
    {
        $linker = Autolink::create();
        $this->assertInstanceOf('Twitter\\Text\\AutoLink', $linker);
    }

    public function testAutolinkWithEmoji()
    {
        $text = "@ummjackson 🤡 https://i.imgur.com/I32CQ81.jpg";
        $linkedText = $this->linker->autoLink($text);

        // @codingStandardsIgnoreStart
        $expected = '@<a class="tweet-url username" href="https://twitter.com/ummjackson" rel="external nofollow" target="_blank">ummjackson</a> 🤡 <a href="https://i.imgur.com/I32CQ81.jpg" rel="external nofollow" target="_blank">https://i.imgur.com/I32CQ81.jpg</a>';
        // @codingStandardsIgnoreEnd

        $this->assertSame($expected, $linkedText);
    }
}
