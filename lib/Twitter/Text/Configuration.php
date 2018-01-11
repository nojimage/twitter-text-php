<?php

/**
 * @author    Takashi Nojima
 * @copyright Copyright 2018, Takashi Nojima
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package   Twitter.Text
 */

namespace Twitter\Text;

/**
 * Twitter Text Configuration
 *
 * @author    Takashi Nojima
 * @copyright Copyright 2018, Takashi Nojima
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package   Twitter.Text
 *
 * @property int $version
 * @property int $maxWeightedTweetLength
 * @property int $scale
 * @property int $defaultWeight
 * @property int $transformedURLLength
 * @property array $ranges
 */
class Configuration
{

    /**
     * default configuration from v2.json
     *
     * @var array
     */
    static private $defaultConfigraion = array(
        'version' => 2,
        'maxWeightedTweetLength' => 280,
        'scale' => 100,
        'defaultWeight' => 200,
        'transformedURLLength' => 23,
        'ranges' => array(
            array(
                'start' => 0,
                'end' => 4351,
                'weight' => 100
            ),
            array(
                'start' => 8192,
                'end' => 8205,
                'weight' => 100
            ),
            array(
                'start' => 8208,
                'end' => 8223,
                'weight' => 100
            ),
            array(
                'start' => 8242,
                'end' => 8247,
                'weight' => 100
            )
        )
    );

    /**
     * @var array
     */
    private $config = array();

    /**
     * construct
     *
     * @param array $config
     */
    public function __construct(array $config = null)
    {
        if ($config === null) {
            $config = static::$defaultConfigraion;
        }

        $this->config = $config;
    }

    /**
     * property accessor
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->config[$name]) ? $this->config[$name] : null;
    }

    /**
     * convert to array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->config;
    }

    /**
     * Create configration from json string
     *
     * @param string $json as configration
     * @return Configuration
     */
    public static function fromJson($json)
    {
        return new Configuration(json_decode($json, true));
    }

    /**
     * maxWeightedTweetLength * scale
     *
     * @return int
     */
    public function getScaledMaxWeightedTweetLength()
    {
        return $this->maxWeightedTweetLength * $this->scale;
    }

    /**
     * transformedURLLength * scale
     *
     * @return int
     */
    public function getScaledTransformedURLLength()
    {
        return $this->transformedURLLength * $this->scale;
    }
}
