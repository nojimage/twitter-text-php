<?php

namespace Twitter\Text;

use Twitter\Text\Regex;

/**
 * test for Regex
 */
class RegexTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Twitter\Text\Regex::getRtlCharsMatcher
     */
    public function testGetRtlCharsMatcher()
    {
        $matcher = Regex::getRtlCharsMatcher();
        $this->assertStringStartsWith('/[', $matcher);
        $this->assertStringEndsWith(']/iu', $matcher);

        $matcherCached = Regex::getRtlCharsMatcher();
        $this->assertSame($matcher, $matcherCached);
    }
}
