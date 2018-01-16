<?php

/**
 * @author    Takashi Nojima
 * @copyright Copyright 2018, Takashi Nojima
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package   Twitter.Text
 */

namespace Twitter\Text;

/**
 * Twitter Text Parser
 *
 * @author    Takashi Nojima
 * @copyright Copyright 2018, Takashi Nojima
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package   Twitter.Text
 */
class Parser
{

    /**
     * @var Configuration
     */
    private $config;

    /**
     * Create a Parser
     *
     * @param Configuration $config
     * @return Parser
     */
    public static function create(Configuration $config = null)
    {
        return new self($config);
    }

    /**
     * construct
     *
     * @param Configuration $config
     */
    public function __construct(Configuration $config = null)
    {
        if ($config === null) {
            $config = new Configuration();
        }

        $this->config = $config;
    }

    /**
     * Parses a given tweet text with the weighted character count configuration
     *
     * @param string $tweet which is to be parsed
     * @return ParseResults
     */
    public function parseTweet($tweet)
    {
        if ($tweet === null || strlen($tweet) === 0) {
            return new ParseResults;
        }

        $normalizedTweet = StringUtils::normalizeFromNFC($tweet);
        $normalizedtweetLength = StringUtils::strlen($normalizedTweet);

        $defaultWeight = $this->config->defaultWeight;
        $maxWeightedTweetLength = $this->config->getScaledMaxWeightedTweetLength();
        $transformedUrlWeight = $this->config->getScaledTransformedURLLength();
        $ranges = $this->config->ranges;

        $extractor = new Extractor();
        $urlEntities = $extractor->extractURLsWithIndices($normalizedTweet);

        $hasInvalidCharacters = false;
        $weightedCount = 0;
        $offset = 0;
        $displayOffset = 0;
        $validOffset = 0;

        while ($offset < $normalizedtweetLength) {
            $charWeight = $defaultWeight;
            $matchedUrlEntityIdx = false;

            foreach ($urlEntities as $idx => $urlEntity) {
                $urlStart = $urlEntity['indices'][0];
                $urlEnd = $urlEntity['indices'][1];

                if ($offset === $urlStart) {
                    $urlLength = $urlEnd - $urlStart;

                    $weightedCount += $transformedUrlWeight;
                    $offset += $urlLength;
                    $displayOffset += $urlLength;
                    if ($weightedCount <= $maxWeightedTweetLength) {
                        $validOffset += $urlLength;
                    }

                    $matchedUrlEntityIdx = $idx;
                    break;
                }
            }

            if ($matchedUrlEntityIdx !== false) {
                unset($urlEntities[$matchedUrlEntityIdx]);
                continue;
            }

            if ($offset < $normalizedtweetLength) {
                $char = StringUtils::substr($normalizedTweet, $offset, 1);
                $codePoint = StringUtils::ord($char);

                foreach ($ranges as $range) {
                    if ($this->inRange($codePoint, $range)) {
                        $charWeight = $range['weight'];
                        break;
                    }
                }

                $weightedCount += $charWeight;

                $hasInvalidCharacters = $hasInvalidCharacters || $this->hasInvalidCharacters($char);
                $charCount = StringUtils::strlen($char);
                $charWidth = $this->isSurrogatePair($char) ? 2 : 1;
                $offset += $charCount;
                $displayOffset += $charWidth;

                if (!$hasInvalidCharacters && $weightedCount <= $maxWeightedTweetLength) {
                    $validOffset += $charWidth;
                }
            }
        }

        $scaledWeightedLength = $weightedCount / $this->config->scale;
        $permillage = $scaledWeightedLength * 1000 / $this->config->maxWeightedTweetLength;
        $isValid = !$hasInvalidCharacters && $weightedCount <= $maxWeightedTweetLength;

        $normalizedTweetOffset = StringUtils::strlen($tweet) - $normalizedtweetLength;
        $displayTextRange = array(0, $displayOffset + $normalizedTweetOffset - 1);
        $validTextRange = array(0, $validOffset + $normalizedTweetOffset - 1);

        return new ParseResults($scaledWeightedLength, $permillage, $isValid, $displayTextRange, $validTextRange);
    }

    /**
     * check codepoint in range
     *
     * @param int $codePoint
     * @param array $range
     * @return boolean
     */
    private function inRange($codePoint, array $range)
    {
        return ($codePoint >= $range['start'] && $codePoint <= $range['end']);
    }

    /**
     * check has invalid characters
     *
     * @param string $char
     * @return bool
     */
    private function hasInvalidCharacters($char)
    {
        return preg_match(Regex::getInvalidCharactersMatcher(), $char);
    }

    /**
     * is surrogate pair char
     *
     * @param string $char
     * @return bool
     */
    private function isSurrogatePair($char)
    {
        return preg_match('/[\\x{10000}-\\x{10FFFF}]/u', $char);
    }
}
