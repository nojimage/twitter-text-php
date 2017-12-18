<?php

/**
 * @author     Mike Cochrane <mikec@mikenz.geek.nz>
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter.Text
 */

namespace Twitter\Text;

use Twitter\Text\TldLists;

/**
 * Twitter Regex Abstract Class
 *
 * Used by subclasses that need to parse tweets.
 *
 * Originally written by {@link http://github.com/mikenz Mike Cochrane}, this
 * is based on code by {@link http://github.com/mzsanford Matt Sanford} and
 * heavily modified by {@link http://github.com/ngnpope Nick Pope}.
 *
 * @author     Mike Cochrane <mikec@mikenz.geek.nz>
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter
 */
class Regex
{

    /**
     * Contains all generated regular expressions.
     *
     * @var  string  The regex patterns.
     */
    protected static $patterns = array();

    /**
     * The tweet to be used in parsing.  This should be populated by the
     * constructor of all subclasses.
     *
     * @var  string
     */
    protected $tweet = '';

    /**
     * Invalid Characters
     *
     * 0xFFFE,0xFEFF # BOM
     * 0xFFFF        # Special
     * 0x202A-0x202E # Directional change
     */
    private static $invalidCharacters = '\x{202a}-\x{202e}\x{feff}\x{fffe}\x{ffff}';

    /**
     * Expression to match RTL characters.
     *
     * 0x0600-0x06FF Arabic
     * 0x0750-0x077F Arabic Supplement
     * 0x08A0-0x08FF Arabic Extended-A
     * 0x0590-0x05FF Hebrew
     * 0xFB50-0xFDFF Arabic Presentation Forms-A
     * 0xFE70-0xFEFF Arabic Presentation Forms-B
     *
     * @var string
     */
    private static $rtlChars = '\x{0600}-\x{06ff}\x{0750}-\x{077f}\x{08a0}-\x{08ff}\x{0590}-\x{05ff}\x{fb50}-\x{fdff}\x{fe70}-\x{feff}';

    # These URL validation pattern strings are based on the ABNF from RFC 3986
    private static $validateUrlUnreserved = '[a-z\p{Cyrillic}0-9\-._~]';
    private static $validateUrlPctEncoded = '(?:%[0-9a-f]{2})';
    private static $validateUrlSubDelims = '[!$&\'()*+,;=]';
    private static $validateUrlIpv4 = '(?:(?:[0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(?:\.(?:[0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])){3})';
    private static $validateUrlIpv6 = '(?:\[[a-f0-9:\.]+\])';
    private static $validateUrlPort = '[0-9]{1,5}';

    /**
     * This constructor is used to populate some variables.
     *
     * @param  string  $tweet  The tweet to parse.
     */
    protected function __construct($tweet = null)
    {
        $this->tweet = $tweet;
    }

    /**
     * Emulate a static initialiser while PHP doesn't have one.
     */
    public static function __static()
    {
        # Check whether we have initialized the regular expressions:
        static $initialized = false;
        if ($initialized) {
            return;
        }
        # Get a shorter reference to the regular expression array:
        $re = & self::$patterns;
        # Initialise local storage arrays:
        $tmp = array();

        # Expression to match whitespace characters.
        #
        #   0x0009-0x000D  Cc # <control-0009>..<control-000D>
        #   0x0020         Zs # SPACE
        #   0x0085         Cc # <control-0085>
        #   0x00A0         Zs # NO-BREAK SPACE
        #   0x1680         Zs # OGHAM SPACE MARK
        #   0x180E         Zs # MONGOLIAN VOWEL SEPARATOR
        #   0x2000-0x200A  Zs # EN QUAD..HAIR SPACE
        #   0x2028         Zl # LINE SEPARATOR
        #   0x2029         Zp # PARAGRAPH SEPARATOR
        #   0x202F         Zs # NARROW NO-BREAK SPACE
        #   0x205F         Zs # MEDIUM MATHEMATICAL SPACE
        #   0x3000         Zs # IDEOGRAPHIC SPACE
        $tmp['spaces'] = '\x{0009}-\x{000D}\x{0020}\x{0085}\x{00a0}\x{1680}\x{180E}\x{2000}-\x{200a}\x{2028}\x{2029}\x{202f}\x{205f}\x{3000}';

        # Expression to match at and hash sign characters:
        $tmp['at_signs'] = '@＠';
        $tmp['hash_signs'] = '#＃';

        # Expression to match latin accented characters.
        #
        #   0x00C0-0x00D6
        #   0x00D8-0x00F6
        #   0x00F8-0x00FF
        #   0x0100-0x024f
        #   0x0253-0x0254
        #   0x0256-0x0257
        #   0x0259
        #   0x025b
        #   0x0263
        #   0x0268
        #   0x026f
        #   0x0272
        #   0x0289
        #   0x028b
        #   0x02bb
        #   0x0300-0x036f
        #   0x1e00-0x1eff
        #
        # Excludes 0x00D7 - multiplication sign (confusable with 'x').
        # Excludes 0x00F7 - division sign.
        $tmp['latin_accents'] = '\x{00c0}-\x{00d6}\x{00d8}-\x{00f6}\x{00f8}-\x{00ff}';
        $tmp['latin_accents'] .= '\x{0100}-\x{024f}\x{0253}-\x{0254}\x{0256}-\x{0257}';
        $tmp['latin_accents'] .= '\x{0259}\x{025b}\x{0263}\x{0268}\x{026f}\x{0272}\x{0289}\x{028b}\x{02bb}\x{0300}-\x{036f}\x{1e00}-\x{1eff}';

        $tmp['hashtag_letters'] = '\p{L}\p{M}';
        $tmp['hashtag_numerals'] = '\p{Nd}';
        # Hashtag special chars
        #
        #   _      underscore
        #   0x200c ZERO WIDTH NON-JOINER (ZWNJ)
        #   0x200d ZERO WIDTH JOINER (ZWJ)
        #   0xa67e CYRILLIC KAVYKA
        #   0x05be HEBREW PUNCTUATION MAQAF
        #   0x05f3 HEBREW PUNCTUATION GERESH
        #   0x05f4 HEBREW PUNCTUATION GERSHAYIM
        #   0xff5e FULLWIDTH TILDE
        #   0x301c WAVE DASH
        #   0x309b KATAKANA-HIRAGANA VOICED SOUND MARK
        #   0x309c KATAKANA-HIRAGANA SEMI-VOICED SOUND MARK
        #   0x30a0 KATAKANA-HIRAGANA DOUBLE HYPHEN
        #   0x30fb KATAKANA MIDDLE DOT
        #   0x3003 DITTO MARK
        #   0x0f0b TIBETAN MARK INTERSYLLABIC TSHEG
        #   0x0f0c TIBETAN MARK DELIMITER TSHEG BSTAR
        #   0x00b7 MIDDLE DOT
        $tmp['hashtag_special_chars'] = '_\x{200c}\x{200d}\x{a67e}\x{05be}\x{05f3}\x{05f4}\x{ff5e}\x{301c}\x{309b}\x{309c}\x{30a0}\x{30fb}\x{3003}\x{0f0b}\x{0f0c}\x{00b7}';
        $tmp['hashtag_letters_numerals_set'] = '[' . $tmp['hashtag_letters'] . $tmp['hashtag_numerals'] . $tmp['hashtag_special_chars'] . ']';
        $tmp['hashtag_letters_set'] = '[' . $tmp['hashtag_letters'] . ']';
        $tmp['hashtag_boundary'] = '(?:\A|\x{fe0e}|\x{fe0f}|[^&' . $tmp['hashtag_letters'] . $tmp['hashtag_numerals'] . $tmp['hashtag_special_chars'] . '])';
        $tmp['hashtag'] = '(' . $tmp['hashtag_boundary'] . ')(#|\x{ff03})(?!\x{fe0f}|\x{20e3})(' . $tmp['hashtag_letters_numerals_set'] . '*' . $tmp['hashtag_letters_set'] . $tmp['hashtag_letters_numerals_set'] . '*)';

        $re['valid_hashtag'] = '/' . $tmp['hashtag'] . '(?=(.*|$))/iu';
        $re['end_hashtag_match'] = '/\A(?:[' . $tmp['hash_signs'] . ']|:\/\/)/u';

        # XXX: PHP doesn't have Ruby's $' (dollar apostrophe) so we have to capture
        #      $after in the following regular expression.  Note that we only use a
        #      look-ahead capture here and don't append $after when we return.
        $tmp['valid_mention_preceding_chars'] = '([^a-zA-Z0-9_!#\$%&*@＠\/]|^|(?:^|[^a-z0-9_+~.-])RT:?)';
        $re['valid_mentions_or_lists'] = '/' . $tmp['valid_mention_preceding_chars'] . '([' . $tmp['at_signs'] . '])([a-z0-9_]{1,20})(\/[a-z][a-z0-9_\-]{0,24})?(?=(.*|$))/iu';
        $re['valid_reply'] = '/^(?:[' . $tmp['spaces'] . '])*[' . $tmp['at_signs'] . ']([a-z0-9_]{1,20})(?=(.*|$))/iu';
        $re['end_mention_match'] = '/\A(?:[' . $tmp['at_signs'] . ']|[' . $tmp['latin_accents'] . ']|:\/\/)/iu';

        # URL related hash regex collection

        $tmp['valid_url_preceding_chars'] = '(?:[^A-Z0-9_@＠\$#＃' . static::$invalidCharacters . ']|^)';

        $tmp['domain_valid_chars'] = '0-9a-z' . $tmp['latin_accents'];
        $tmp['valid_subdomain'] = '(?>(?:[' . $tmp['domain_valid_chars'] . '][' . $tmp['domain_valid_chars'] . '\-_]*)?[' . $tmp['domain_valid_chars'] . ']\.)';
        $tmp['valid_domain_name'] = '(?:(?:[' . $tmp['domain_valid_chars'] . '][' . $tmp['domain_valid_chars'] . '\-]*)?[' . $tmp['domain_valid_chars'] . ']\.)';
        $tmp['domain_valid_unicode_chars'] = '[^\p{P}\p{Z}\p{C}' . static::$invalidCharacters . $tmp['spaces'] . ']';

        $tmp['valid_gTLD'] = TldLists::getValidGTLD();
        $tmp['valid_ccTLD'] = TldLists::getValidCcTLD();
        $tmp['valid_special_ccTLD'] = '(?:(?:' . 'co|tv' . ')(?=[^0-9a-z@]|$))';
        $tmp['valid_punycode'] = '(?:xn--[0-9a-z]+)';

        $tmp['valid_domain'] = ''
            // subdomains + domain + TLD
            // e.g. www.twitter.com, foo.co.jp, bar.co.uk
            . '(?:' . $tmp['valid_subdomain'] . '+' . $tmp['valid_domain_name']
            . '(?:' . $tmp['valid_gTLD'] . '|' . $tmp['valid_ccTLD'] . '|' . $tmp['valid_punycode'] . '))'
            // domain + gTLD | protocol + unicode domain + gTLD
            . '|(?:'
            . '(?:'
            . $tmp['valid_domain_name'] . '|(?:(?<=http:\/\/|https:\/\/)' . $tmp['domain_valid_unicode_chars'] . '+\.)'
            . ')'
            . $tmp['valid_gTLD']
            . ')'
            // domain + gTLD | some ccTLD
            // e.g. twitter.com
            . '|(?:' . $tmp['valid_domain_name'] . $tmp['valid_punycode'] . ')'
            . '|(?:' . $tmp['valid_domain_name'] . $tmp['valid_special_ccTLD'] . ')'
            // protocol + domain + ccTLD | protocol + unicode domain + ccTLD
            . '|(?:(?<=http:\/\/|https:\/\/)'
            . '(?:' . $tmp['valid_domain_name'] . '|' . $tmp['domain_valid_unicode_chars'] . '+\.)'
            . $tmp['valid_ccTLD'] . ')'
            // domain + ccTLD + '/'
            // e.g. t.co/
            . '|(?:' . $tmp['valid_domain_name'] . $tmp['valid_ccTLD'] . '(?=\/))';
        # Used by the extractor:
        $re['valid_ascii_domain'] = '/' . $tmp['valid_subdomain'] . '*' . $tmp['valid_domain_name'] . '(?:' . $tmp['valid_gTLD'] . '|' . $tmp['valid_ccTLD'] . '|' . $tmp['valid_punycode'] . ')/iu';

        # Used by the extractor for stricter t.co URL extraction:
        $re['valid_tco_url'] = '/^https?:\/\/t\.co\/[a-z0-9]+/iu';

        # Used by the extractor to filter out unwanted URLs:
        $re['invalid_short_domain'] = '/\A' . $tmp['valid_domain_name'] . $tmp['valid_ccTLD'] . '\Z/iu';
        $re['valid_special_short_domain'] = '/\A' . $tmp['valid_domain_name'] . $tmp['valid_special_ccTLD'] . '\Z/iu';
        $re['invalid_url_without_protocol_preceding_chars'] = '/[\-_.\/]\z/iu';

        $tmp['valid_port_number'] = '[0-9]+';

        $tmp['valid_general_url_path_chars'] = '[a-z\p{Cyrillic}0-9!\*;:=\+\,\.\$\/%#\[\]\-_~&|@' . $tmp['latin_accents'] . ']';
        # Allow URL paths to contain up to two nested levels of balanced parentheses:
        # 1. Used in Wikipedia URLs, e.g. /Primer_(film)
        # 2. Used in IIS sessions, e.g. /S(dfd346)/
        # 3. Used in Rdio URLs like /track/We_Up_(Album_Version_(Edited))/
        $tmp['valid_url_balanced_parens'] = '(?:\('
            . '(?:' . $tmp['valid_general_url_path_chars'] . '+'
            . '|'
            // allow one nested level of balanced parentheses
            . '(?:'
            . $tmp['valid_general_url_path_chars'] . '*'
            . '\(' . $tmp['valid_general_url_path_chars'] . '+' . '\)'
            . $tmp['valid_general_url_path_chars'] . '*'
            . ')'
            . ')'
            . '\))';
        # Valid end-of-path characters (so /foo. does not gobble the period).
        # 1. Allow =&# for empty URL parameters and other URL-join artifacts.
        $tmp['valid_url_path_ending_chars'] = '[a-z\p{Cyrillic}0-9=_#\/\+\-' . $tmp['latin_accents'] . ']|(?:' . $tmp['valid_url_balanced_parens'] . ')';
        $tmp['valid_url_path'] = '(?:(?:'
            . $tmp['valid_general_url_path_chars'] . '*(?:'
            . $tmp['valid_url_balanced_parens'] . ' '
            . $tmp['valid_general_url_path_chars'] . '*)*'
            . $tmp['valid_url_path_ending_chars'] . ')|(?:@'
            . $tmp['valid_general_url_path_chars'] . '+\/))';

        $tmp['valid_url_query_chars'] = '[a-z0-9!?\*\'\(\);:&=\+\$\/%#\[\]\-_\.,~|@]';
        $tmp['valid_url_query_ending_chars'] = '[a-z0-9_&=#\/\-]';

        $re['valid_url'] = '/(?:'                           # $1 Complete match (preg_match() already matches everything.)
            . '(' . $tmp['valid_url_preceding_chars'] . ')' # $2 Preceding characters
            . '('                                           # $3 Complete URL
            . '(https?:\/\/)?'                              # $4 Protocol (optional)
            . '(' . $tmp['valid_domain'] . ')'              # $5 Domain(s)
            . '(?::(' . $tmp['valid_port_number'] . '))?'   # $6 Port number (optional)
            . '(\/' . $tmp['valid_url_path'] . '*)?'        # $7 URL Path
            . '(\?' . $tmp['valid_url_query_chars'] . '*' . $tmp['valid_url_query_ending_chars'] . ')?' # $8 Query String
            . ')'
            . ')/iux';

        $tmp['cash_signs'] = '\$';
        $tmp['cashtag'] = '[a-z]{1,6}(?:[._][a-z]{1,2})?';
        $re['valid_cashtag'] = '/(^|[' . $tmp['spaces'] . '])([' . $tmp['cash_signs'] . '])(' . $tmp['cashtag'] . ')(?=($|\s|[[:punct:]]))/iu';
        $re['end_cashtag_match'] = '/\A(?:[' . $tmp['cash_signs'] . ']|:\/\/)/u';

        # Flag that initialization is complete:
        $initialized = true;
    }

    /**
     * Get invalid characters matcher
     *
     * @staticvar string $regexp
     * @return string
     */
    public static function getInvalidCharactersMatcher()
    {
        static $regexp = null;

        if ($regexp === null) {
            $regexp = '/[' . static::$invalidCharacters . ']/u';
        }

        return $regexp;
    }

    /**
     * Get RTL characters matcher
     *
     * @staticvar string $regexp
     * @return string
     */
    public static function getRtlCharsMatcher()
    {
        static $regexp = null;

        if ($regexp === null) {
            $regexp = '/[' . static::$rtlChars . ']/iu';
        }

        return $regexp;
    }

    /**
     * Get url matcher
     *
     * @staticvar string $regexp
     * @return string
     */
    public static function getValidateUrlUnencodedMatcher()
    {
        static $regexp = null;

        if ($regexp === null) {
            # Modified version of RFC 3986 Appendix B
            $regexp = '/\A' #  Full URL
                . '(?:'
                . '([^:\/?#]+):\/\/' #  $1 Scheme
                . ')?'
                . '([^\/?#]*)'       #  $2 Authority
                . '([^?#]*)'         #  $3 Path
                . '(?:'
                . '\?([^#]*)'        #  $4 Query
                . ')?'
                . '(?:'
                . '\#(.*)'           #  $5 Fragment
                . ')?\z/iux';
        }

        return $regexp;
    }

    /**
     * Get valid url ip
     *
     * @return string matcher
     */
    private static function getValidateUrlIp()
    {
        return '(?:' . static::$validateUrlIpv4 . '|' . static::$validateUrlIpv6 . ')'; #/iox
    }

    /**
     * Get valid url domain
     *
     * @return string matcher
     */
    private static function getValidateUrlDomain()
    {
        $subdomain = '(?:[a-z0-9](?:[a-z0-9_\-]*[a-z0-9])?)'; #/i
        $domain = '(?:[a-z0-9](?:[a-z0-9\-]*[a-z0-9])?)'; #/i
        $tld = '(?:[a-z](?:[a-z0-9\-]*[a-z0-9])?)'; #/i

        return '(?:(?:' . $subdomain . '\.)*(?:' . $domain . '\.)' . $tld . ')'; #/iox
    }

    /**
     * Get valid url host
     *
     * @return string matcher
     */
    private static function getValidateUrlHost()
    {
        return '(?:' . static::getValidateUrlIp() . '|' . static::getValidateUrlDomain() . ')'; #/iox
    }

    /**
     * Get valid url unicode domain
     *
     * @return string matcher
     */
    private static function getValidateUrlUnicodeDomain()
    {
        $subdomain = '(?:(?:[a-z0-9]|[^\x00-\x7f])(?:(?:[a-z0-9_\-]|[^\x00-\x7f])*(?:[a-z0-9]|[^\x00-\x7f]))?)'; #/ix
        $domain = '(?:(?:[a-z0-9]|[^\x00-\x7f])(?:(?:[a-z0-9\-]|[^\x00-\x7f])*(?:[a-z0-9]|[^\x00-\x7f]))?)'; #/ix
        $tld = '(?:(?:[a-z]|[^\x00-\x7f])(?:(?:[a-z0-9\-]|[^\x00-\x7f])*(?:[a-z0-9]|[^\x00-\x7f]))?)'; #/ix

        return '(?:(?:' . $subdomain . '\.)*(?:' . $domain . '\.)' . $tld . ')'; #/iox
    }

    /**
     * Get valid url unicode host
     *
     * @return string matcher
     */
    private static function getValidateUrlUnicodeHost()
    {
        return '(?:' . static::getValidateUrlIp() . '|' . static::getValidateUrlUnicodeDomain() . ')'; #/iox
    }

    /**
     * Get valid url userinfo
     *
     * @return string matcher
     */
    private static function getValidateUrlUserinfo()
    {
        return '(?:' . static::$validateUrlUnreserved
            . '|' . static::$validateUrlPctEncoded
            . '|' . static::$validateUrlSubDelims
            . '|:)*'; #/iox
    }

    /**
     * Get url unicode authority matcher
     *
     * Unencoded internationalized domains - this doesn't check for invalid UTF-8 sequences
     *
     * @staticvar string $regexp
     * @return string
     */
    public static function getValidateUrlUnicodeAuthorityMatcher()
    {
        static $regexp = null;

        if ($regexp === null) {
            $regexp = '/'
                    . '(?:(' . static::getValidateUrlUserinfo() . ')@)?' #  $1 userinfo
                    . '(' . static::getValidateUrlUnicodeHost() . ')'    #  $2 host
                    . '(?::(' . static::$validateUrlPort . '))?'         #  $3 port
                    . '/iux';
        }

        return $regexp;
    }

    /**
     * Get url authority matcher
     *
     * This is more strict than the rfc specifies
     *
     * @staticvar string $regexp
     * @return string
     */
    public static function getValidateUrlAuthorityMatcher()
    {
        static $regexp = null;

        if ($regexp === null) {
            $regexp = '/'
                    . '(?:(' . static::getValidateUrlUserinfo() . ')@)?' #  $1 userinfo
                    . '(' . static::getValidateUrlHost() . ')'           #  $2 host
                    . '(?::(' . static::$validateUrlPort . '))?'         #  $3 port
                    . '/ix';
        }

        return $regexp;
    }

    /**
     * Get url scheme matcher
     *
     * @staticvar string $regexp
     * @return string
     */
    public static function getValidateUrlSchemeMatcher()
    {
        static $regexp = null;

        if ($regexp === null) {
            $regexp = '/(?:[a-z][a-z0-9+\-.]*)/i';
        }

        return $regexp;
    }

    /**
     * Get valid url charactors
     *
     * @return string matcher
     */
    private static function getValidateUrlPchar()
    {
        return '(?:' . static::$validateUrlUnreserved
            . '|' . static::$validateUrlPctEncoded
            . '|' . static::$validateUrlSubDelims
            . '|[:\|@])'; #/iox
    }

    /**
     * Get url path matcher
     *
     * @staticvar string $regexp
     * @return string
     */
    public static function getValidateUrlPathMatcher()
    {
        static $regexp = null;

        if ($regexp === null) {
            $regexp = '/(\/' . static::getValidateUrlPchar() . '*)*/iu';
        }

        return $regexp;
    }

    /**
     * Get url query matcher
     *
     * @staticvar string $regexp
     * @return string
     */
    public static function getValidateUrlQueryMatcher()
    {
        static $regexp = null;

        if ($regexp === null) {
            $regexp = '/(' . static::getValidateUrlPchar() . '|\/|\?)*/iu';
        }

        return $regexp;
    }

    /**
     * Get url flagment matcher
     *
     * @staticvar string $regexp
     * @return string
     */
    public static function getValidateUrlFragmentMatcher()
    {
        static $regexp = null;

        if ($regexp === null) {
            $regexp = '/(' . static::getValidateUrlPchar() . '|\/|\?)*/iu';
        }

        return $regexp;
    }
}

# Cause regular expressions to be initialized as soon as this file is loaded:
Regex::__static();
