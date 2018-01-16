<?php

/**
 * @author    Takashi Nojima
 * @copyright Copyright 2018, Takashi Nojima
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package   Twitter.Text
 */

namespace Twitter\Text;

use PHPUnit\Framework\TestCase;
use Twitter\Text\Configuration;

/**
 * Twitter Text Configuration Unit Tests
 *
 * @author    Takashi Nojima
 * @copyright Copyright 2018, Takashi Nojima
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package   Twitter.Text
 */
class ConfigurationTest extends TestCase
{

    /**
     * @var Configuration
     */
    private $config;

    /**
     * Set up fixtures
     *
     * @return void
     */
    protected function setUp()
    {
        $this->config = new Configuration;
    }

    /**
     * Tears down fixtures
     *
     * @return void
     */
    protected function tearDown()
    {
        unset($this->config);
    }

    /**
     * read configration file from twitter-text
     *
     * @param string $version 'v1' or 'v2'
     * @return string
     */
    private function readConfigJson($version = 'v2')
    {
        return file_get_contents(CONFIG . "/$version.json");
    }

    /**
     * get configration array from twitter-text
     *
     * @param string $version 'v1' or 'v2'
     * @return array
     */
    private function getConfigration($version = 'v2')
    {
        return json_decode($this->readConfigJson($version), true);
    }

    /**
     * test for construct
     *
     * @return void
     */
    public function testConstruct()
    {
        $this->assertSame(2, $this->config->version);
    }

    /**
     * test for construct
     *
     * @return void
     */
    public function testConstructWithConfiguration()
    {
        $input = $this->getConfigration('v1');
        $config = new Configuration($input);

        $this->assertSame(1, $config->version);
        $this->assertSame(140, $config->maxWeightedTweetLength);
        $this->assertSame(1, $config->scale);
        $this->assertSame(1, $config->defaultWeight);
        $this->assertSame(23, $config->transformedURLLength);
        $this->assertSame(array(), $config->ranges);
    }

    /**
     * test for toArray
     *
     * @return void
     */
    public function testToArray()
    {
        $config = $this->getConfigration();
        $this->assertSame($config, $this->config->toArray());
    }

    /**
     * test for Configuration::fromJson
     *
     * @return void
     */
    public function testCreateFromJson()
    {
        $v2Config = Configuration::fromJson($this->readConfigJson('v2'));
        $this->assertSame($this->getConfigration('v2'), $v2Config->toArray());

        $v1Config = Configuration::fromJson($this->readConfigJson('v1'));
        $this->assertSame($this->getConfigration('v1'), $v1Config->toArray());
    }

    /**
     * test for Configuration::v1
     *
     * @return void
     */
    public function testV1Configuration()
    {
        $config = Configuration::v1();

        $this->assertSame(1, $config->version);
        $this->assertSame(140, $config->maxWeightedTweetLength);
        $this->assertSame(1, $config->scale);
        $this->assertSame(1, $config->defaultWeight);
        $this->assertSame(23, $config->transformedURLLength);
        $this->assertSame(array(), $config->ranges);
    }

    /**
     * test for getScaledMaxWeightedTweetLength
     *
     * @return void
     */
    public function testGetScaledMaxWeightedTweetLength()
    {
        $this->assertSame(28000, $this->config->getScaledMaxWeightedTweetLength());
    }

    /**
     * test for getScaledTransformedUrlWeight
     *
     * @return void
     */
    public function testGetScaledTransformedURLLength()
    {
        $this->assertSame(2300, $this->config->getScaledTransformedURLLength());
    }
}
