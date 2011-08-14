<?php
/**
 * @author     Mike Cochrane <mikec@mikenz.geek.nz>
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter
 */

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
abstract class Twitter_Regex {

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
   * This constructor is used to populate some variables.
   *
   * @param  string  $tweet  The tweet to parse.
   */
  protected function __construct($tweet) {
    $this->tweet = $tweet;
  }

  /**
   * Emulate a static initialiser while PHP doesn't have one.
   */
  public static function __static() {
    # Check whether we have initialized the regular expressions:
    static $initialized = false;
    if ($initialized) return;
    # Get a shorter reference to the regular expression array:
    $re =& self::$patterns;
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
    $re['spaces'] = '[\x{0009}-\x{000D}\x{0020}\x{0085}\x{00a0}\x{1680}\x{180E}\x{2000}-\x{200a}\x{2028}\x{2029}\x{202f}\x{205f}\x{3000}]';
    #$re['spaces'] = '[\x09-\x0D\x20\x85\xA0]|\xe1\x9a\x80|\xe1\xa0\x8e|\xe2\x80[\x80-\x8a\xa8\xa9\xaf\xdf]|\xe3\x80\x80';

    # Expression to match at sign characters:
    $tmp['at_signs'] = '@＠';

    # Expression to match latin accented characters.
    #
    #   0x00C0-0x00D6
    #   0x00D8-0x00F6
    #   0x00F8-0x00FF
    #   0x015F
    #
    # Excludes 0x00D7 - multiplication sign (confusable with 'x').
    # Excludes 0x00F7 - division sign.
    $tmp['latin_accents'] = '\x{00c0}-\x{00d6}\x{00d8}-\x{00f6}\x{00f8}-\x{00ff}\x{015f}';

    $re['extract_mentions'] = '/(^|[^a-z0-9_])['.$tmp['at_signs'].']([a-z0-9_]{1,20})(['.$tmp['at_signs'].$tmp['latin_accents'].']?)/iu';
    ####$re['extract_mentions'] = '/(^|[^a-z0-9_])'.$re['at_signs'].'([a-z0-9_]{1,20})(?=(.|$))/iu';
    $re['extract_mentions_or_lists'] = '/(^|[^a-z0-9_])['.$tmp['at_signs'].']([a-z0-9_]{1,20})(\/[a-z][a-z0-9_\-]{0,24})?(?=(.|$))/iu';
    $re['extract_reply'] = '/^(?:'.$re['spaces'].')*['.$tmp['at_signs'].']([a-z0-9_]{1,20})/iu';
    $re['list_name'] = '/[a-z][a-z0-9_\-\x{0080}-\x{00ff}]{0,24}/iu';
    $re['end_screen_name_match'] = '/^(?:['.$tmp['at_signs'].']|['.$tmp['latin_accents'].']|:\/\/)/iu';

    # Expression to match non-latin characters.
    #
    # Cyrillic (Russian, Ukranian, ...):
    #
    #   0x0400-0x04FF Cyrillic
    #   0x0500-0x0527 Cyrillic Supplement
    #   0x2DE0-0x2DFF Cyrillic Extended A
    #   0xA640-0xA69F Cyrillic Extended B
    #
    # Hangul (Korean):
    #
    #   0x1100-0x11FF Hangul Jamo
    #   0x3130-0x3185 Hangul Compatibility Jamo
    #   0xA960-0xA97F Hangul Jamo Extended A
    #   0xAC00-0xD7AF Hangul Syllables
    #   0xD7B0-0xD7FF Hangul Jamo Extended B
    #   0xFFA1-0xFFDC Half-Width Hangul
    $tmp['non_latin_hashtag_chars'] = '\x{0400}-\x{04ff}\x{0500}-\x{0527}\x{2de0}-\x{2dff}\x{a640}-\x{a69f}\x{1100}-\x{11ff}\x{3130}-\x{3185}\x{a960}-\x{a97f}\x{ac00}-\x{d7af}\x{d7b0}-\x{d7ff}\x{ffa1}-\x{ffdc}';

    # Expression to match other characters.
    #
    #   0x30A1-0x30FA   Katakana (Full-Width)
    #   0x30FC-0x30FE   Katakana (Full-Width)
    #   0xFF10-0xFF19   Latin (Full-Width)
    #   0xFF21-0xFF3A   Latin (Full-Width)
    #   0xFF41-0xFF5A   Latin (Full-Width)
    #   0x3041-0x3096   Hiragana
    #   0x3099-0x309E   Hiragana
    #   0x3400-0x4DBF   Kanji (CJK Extension A)
    #   0x4E00-0x9FFF   Kanji (Unified)
    #   0x20000-0x2A6DF Kanji (CJK Extension B)
    #   0x2A700-0x2B73F Kanji (CJK Extension C)
    #   0x2B740-0x2B81F Kanji (CJK Extension D)
    #   0x2F800-0x2FA1F Kanji (CJK supplement)
    #   0x3005          Kanji (CJK supplement)
    #   0x303B          Kanji (CJK supplement)
    $tmp['cj_hashtag_characters'] = '\x{30A1}-\x{30FA}\x{30FC}-\x{30FE}\x{FF10}-\x{FF19}\x{FF21}-\x{FF3A}\x{FF41}-\x{FF5A}\x{3041}-\x{3096}\x{3099}-\x{309E}\x{3400}-\x{4DBF}\x{4E00}-\x{9FFF}\x{020000}-\x{02a6df}\x{02a700}-\x{02b73f}\x{02b740}-\x{02b81f}\x{02f800}-\x{02fa1f}\x{3005}\x{303B}';

    $tmp['hashtag_boundary'] = '(?:^|$|'.$re['spaces'].'|「|」|。|、|\.|!|\?|！|？|,)';
    $tmp['hashtag_alpha'] = '[a-z_'.$tmp['latin_accents'].$tmp['non_latin_hashtag_chars'].$tmp['cj_hashtag_characters'].']';
    $tmp['hashtag_alphanumeric'] = '[a-z0-9_'.$tmp['latin_accents'].$tmp['non_latin_hashtag_chars'].$tmp['cj_hashtag_characters'].']';
    $tmp['hashtag'] = '('.$tmp['hashtag_boundary'].')(#|＃)('.$tmp['hashtag_alphanumeric'].'*'.$tmp['hashtag_alpha'].$tmp['hashtag_alphanumeric'].'*)(?='.$tmp['hashtag_boundary'].')';

    $re['auto_link_hashtags'] = '/'.$tmp['hashtag'].'/iu';

    $re['auto_link_usernames_or_lists'] = '/([^a-z0-9_\/]|^|RT:?)(['.$tmp['at_signs'].']+)([a-z0-9_]{1,20})(\/[a-z][-_a-z0-9\x{0080}-\x{00ff}]{0,24})?(['.$tmp['at_signs'].$tmp['latin_accents'].']?)/iu';
    ####$re['auto_link_usernames_or_lists'] = '/([^a-z0-9_]|^|RT:?)(['.$tmp['at_signs'].']+)([a-z0-9_]{1,20})(\/[a-z][a-z0-9_\-]{0,24})?/iu';
    $re['auto_link_emoticon'] = '/(8\-\#|8\-E|\+\-\(|\`\@|\`O|\&lt;\|:~\(|\}:o\{|:\-\[|\&gt;o\&lt;|X\-\/|\[:-\]\-I\-|\/\/\/\/Ö\\\\\\\\\|\(\|:\|\/\)|∑:\*\)|\( \| \))/iu';

    # URL related hash regex collection

    $tmp['valid_preceding_chars'] = '(?:[^-\/"\':!=A-Z0-9_'.$tmp['at_signs'].']|^|\:)';

    $tmp['domain_exclude_part'] = '[:punct:][:space:][:blank:]\x{00a0}';

    $tmp['valid_subdomain'] = '(?:[^'.$tmp['domain_exclude_part'].'](?:[_-]|[^'.$tmp['domain_exclude_part'].'])*)?[^'.$tmp['domain_exclude_part'].']\.';
    $tmp['valid_domain_name'] = '(?:[^'.$tmp['domain_exclude_part'].'](?:[-]|[^'.$tmp['domain_exclude_part'].'])*)?[^'.$tmp['domain_exclude_part'].']';
    $tmp['valid_domain'] = $tmp['valid_subdomain'].'*'.$tmp['valid_domain_name'].'\.(?:xn--[a-z0-9]{2,}|[a-z]{2,})(?::[0-9]+)?';

    $tmp['valid_general_url_path_chars'] = '[a-z0-9!\*\';:=\+\,\$\/%#\[\]\-_~|'.$tmp['latin_accents'].']';
    $tmp['wikipedia_disambiguation'] = '(?:\('.$tmp['valid_general_url_path_chars'].'+\))';
    $tmp['valid_url_path_chars'] = '(?:'.$tmp['wikipedia_disambiguation'].'|@'.$tmp['valid_general_url_path_chars'].'+\/|[\.,]'.$tmp['valid_general_url_path_chars'].'?|'.$tmp['valid_general_url_path_chars'].'+)';

    $tmp['valid_url_path_ending_chars'] = '[a-z0-9=_#\/\+\-'.$tmp['latin_accents'].']|'.$tmp['wikipedia_disambiguation'];
    $tmp['valid_url_query_chars'] = '[a-z0-9!\*\'\(\);:&=\+\$\/%#\[\]\-_\.,~|]';
    $tmp['valid_url_query_ending_chars'] = '[a-z0-9_&=#\/]';

    $re['valid_url'] = '/(?:'                    # $1 Complete match (preg_match already matches everything.)
      . '('.$tmp['valid_preceding_chars'].')'    # $2 Preceding character
      . '('                                      # $3 Complete URL
      . '(https?:\/\/)'                          # $4 Protocol (or www)
      . '('.$tmp['valid_domain'].')'             # $5 Domain(s) (and port)
      . '(\/(?:'                                 # $6 URL Path
      . $tmp['valid_url_path_chars'].'+'.$tmp['valid_url_path_ending_chars'].'|'
      # XXX ???. $tmp['valid_url_path_chars'].'+'.$tmp['valid_url_path_ending_chars'].'?|'
      . $tmp['valid_url_path_ending_chars']
      . ')?)?'
      . '(\?'.$tmp['valid_url_query_chars'].'*'  # $7 Query String
      . $tmp['valid_url_query_ending_chars'].')?'
      . ')'
      . ')/iux';

     # These URL validation pattern strings are based on the ABNF from RFC 3986
    $tmp['validate_url_unreserved'] = '[a-z0-9\-._~]';
    $tmp['validate_url_pct_encoded'] = '(?:%[0-9a-f]{2})';
    $tmp['validate_url_sub_delims'] = '[!$&\'()*+,;=]';
    $tmp['validate_url_pchar'] = '(?:'.$tmp['validate_url_unreserved'].'|'.$tmp['validate_url_pct_encoded'].'|'.$tmp['validate_url_sub_delims'].'|:|@)'; #/iox

    $tmp['validate_url_userinfo'] = '(?:'.$tmp['validate_url_unreserved'].'|'.$tmp['validate_url_pct_encoded'].'|'.$tmp['validate_url_sub_delims'].'|:)*'; #/iox

    $tmp['validate_url_dec_octet'] = '(?:[0-9]|(?:[1-9][0-9])|(?:1[0-9]{2})|(?:2[0-4][0-9])|(?:25[0-5]))'; #/i
    $tmp['validate_url_ipv4'] = '(?:'.$tmp['validate_url_dec_octet'].'(?:\.'.$tmp['validate_url_dec_octet'].'){3})'; #/iox

    # Punting on real IPv6 validation for now
    $tmp['validate_url_ipv6'] = '(?:\[[a-f0-9:\.]+\])'; #/i

    # Also punting on IPvFuture for now
    $tmp['validate_url_ip'] = '(?:'.$tmp['validate_url_ipv4'].'|'.$tmp['validate_url_ipv6'].')'; #/iox

    # This is more strict than the rfc specifies
    $tmp['validate_url_subdomain_segment'] = '(?:[a-z0-9](?:[a-z0-9_\-]*[a-z0-9])?)'; #/i
    $tmp['validate_url_domain_segment'] = '(?:[a-z0-9](?:[a-z0-9\-]*[a-z0-9])?)'; #/i
    $tmp['validate_url_domain_tld'] = '(?:[a-z](?:[a-z0-9\-]*[a-z0-9])?)'; #/i
    $tmp['validate_url_domain'] = '(?:(?:'.$tmp['validate_url_subdomain_segment'].'\.)*(?:'.$tmp['validate_url_domain_segment'].'\.)'.$tmp['validate_url_domain_tld'].')'; #/iox

    $tmp['validate_url_host'] = '(?:'.$tmp['validate_url_ip'].'|'.$tmp['validate_url_domain'].')'; #/iox

    # Unencoded internationalized domains - this doesn't check for invalid UTF-8 sequences
    $tmp['validate_url_unicode_subdomain_segment'] = '(?:(?:[a-z0-9]|[^\x00-\x7f])(?:(?:[a-z0-9_\-]|[^\x00-\x7f])*(?:[a-z0-9]|[^\x00-\x7f]))?)'; #/ix
    $tmp['validate_url_unicode_domain_segment'] = '(?:(?:[a-z0-9]|[^\x00-\x7f])(?:(?:[a-z0-9\-]|[^\x00-\x7f])*(?:[a-z0-9]|[^\x00-\x7f]))?)'; #/ix
    $tmp['validate_url_unicode_domain_tld'] = '(?:(?:[a-z]|[^\x00-\x7f])(?:(?:[a-z0-9\-]|[^\x00-\x7f])*(?:[a-z0-9]|[^\x00-\x7f]))?)'; #/ix
    $tmp['validate_url_unicode_domain'] = '(?:(?:'.$tmp['validate_url_unicode_subdomain_segment'].'\.)*(?:'.$tmp['validate_url_unicode_domain_segment'].'\.)'.$tmp['validate_url_unicode_domain_tld'].')'; #/iox

    $tmp['validate_url_unicode_host'] = '(?:'.$tmp['validate_url_ip'].'|'.$tmp['validate_url_unicode_domain'].')'; #/iox

    $tmp['validate_url_port'] = '[0-9]{1,5}';

    $re['validate_url_unicode_authority'] = '/'
      .'(?:('.$tmp['validate_url_userinfo'].')@)?' #  $1 userinfo
      .'('.$tmp['validate_url_unicode_host'].')'   #  $2 host
      .'(?::('.$tmp['validate_url_port'].'))?'     #  $3 port
      .'/iux';

    $re['validate_url_authority'] = '/'
      .'(?:('.$tmp['validate_url_userinfo'].')@)?' #  $1 userinfo
      .'('.$tmp['validate_url_host'].')'           #  $2 host
      .'(?::('.$tmp['validate_url_port'].'))?'     #  $3 port
      .'/ix';

    $re['validate_url_scheme'] = '/(?:[a-z][a-z0-9+\-.]*)/i';
    $re['validate_url_path'] = '/(\/'.$tmp['validate_url_pchar'].'*)*/i';
    $re['validate_url_query'] = '/('.$tmp['validate_url_pchar'].'|\/|\?)*/i';
    $re['validate_url_fragment'] = '/('.$tmp['validate_url_pchar'].'|\/|\?)*/i';

    # Modified version of RFC 3986 Appendix B
    $re['validate_url_unencoded'] = '/^' #  Full URL
      .'(?:'
      .'([^:\/?#]+):'  #  $1 Scheme
      .')'
      .'(?:\/\/'
      .'([^\/?#]*)'    #  $2 Authority
      .')'
      .'([^?#]*)'     #  $3 Path
      .'(?:'
      .'\?([^#]*)'    #  $4 Query
      .')?'
      .'(?:'
      .'\#(.*)'       #  $5 Fragment
      .')?$/iux';

    # Invalid Characters:
    #   0xFFFE,0xFEFF # BOM
    #   0xFFFF        # Special
    #   0x202A-0x202E # Directional change
    $re['invalid_characters'] = '/[\x{202a}-\x{202e}\x{feff}\x{fffe}\x{ffff}]/u';

    # Flag that initialization is complete:
    $initialized = true;
  }

}

# Cause regular expressions to be initialized as soon as this file is loaded:
Twitter_Regex::__static();

################################################################################
# vim:et:ft=php:nowrap:sts=2:sw=2:ts=2
