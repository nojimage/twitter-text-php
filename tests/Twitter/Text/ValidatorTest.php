<?php

/**
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter.Text
 */

namespace Twitter\Text;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use Twitter\Text\Validator;

/**
 * Twitter Validator Class Unit Tests
 *
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter.Text
 * @property   Validator $validator
 */
class ValidatorTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();
        $this->validator = new Validator();
    }

    protected function tearDown()
    {
        unset($this->validator);
        parent::tearDown();
    }

    /**
     * A helper function for providers.
     *
     * @param string  $test  The test to fetch data for.
     *
     * @return array  The test data to provide.
     */
    protected function providerHelper($test)
    {
        $data = Yaml::parse(DATA . '/validate.yml');
        return isset($data['tests'][$test]) ? $data['tests'][$test] : array();
    }

    /**
     * @group Validation
     */
    public function testConfiglationFromArray()
    {
        $validator = Validator::create('', array(
                'short_url_length' => 22,
                'short_url_length_https' => 23,
        ));
        $this->assertSame(22, $validator->getShortUrlLength());
        $this->assertSame(23, $validator->getShortUrlLengthHttps());
    }

    /**
     * @group Validation
     */
    public function testConfiglationFromObject()
    {
        $conf = new \stdClass();
        $conf->short_url_length = 22;
        $conf->short_url_length_https = 23;
        $validator = Validator::create('', $conf);
        $this->assertSame(22, $validator->getShortUrlLength());
        $this->assertSame(23, $validator->getShortUrlLengthHttps());
    }
}
