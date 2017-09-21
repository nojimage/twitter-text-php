<?php

/**
 * @author     Mike Cochrane <mikec@mikenz.geek.nz>
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter.Text
 */

namespace Twitter\Text;

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
abstract class Regex
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

        # Invalid Characters:
        #   0xFFFE,0xFEFF # BOM
        #   0xFFFF        # Special
        #   0x202A-0x202E # Directional change
        $tmp['invalid_characters'] = '\x{202a}-\x{202e}\x{feff}\x{fffe}\x{ffff}';

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

        # Expression to match RTL characters.
        #
        #   0x0600-0x06FF Arabic
        #   0x0750-0x077F Arabic Supplement
        #   0x08A0-0x08FF Arabic Extended-A
        #   0x0590-0x05FF Hebrew
        #   0xFB50-0xFDFF Arabic Presentation Forms-A
        #   0xFE70-0xFEFF Arabic Presentation Forms-B
        $tmp['rtl_chars'] = '\x{0600}-\x{06ff}\x{0750}-\x{077f}\x{08a0}-\x{08ff}\x{0590}-\x{05ff}\x{fb50}-\x{fdff}\x{fe70}-\x{feff}';

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
        $tmp['hashtag_letters_numerals_set'] = '[' . $tmp['hashtag_letters'] .  $tmp['hashtag_numerals'] . $tmp['hashtag_special_chars'] . ']';
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

        $tmp['valid_url_preceding_chars'] = '(?:[^A-Z0-9_@＠\$#＃\.' . $tmp['invalid_characters'] . ']|^)';

        $tmp['domain_valid_chars'] = '0-9a-z' . $tmp['latin_accents'];
        $tmp['valid_subdomain'] = '(?>(?:[' . $tmp['domain_valid_chars'] . '][' . $tmp['domain_valid_chars'] . '\-_]*)?[' . $tmp['domain_valid_chars'] . ']\.)';
        $tmp['valid_domain_name'] = '(?:(?:[' . $tmp['domain_valid_chars'] . '][' . $tmp['domain_valid_chars'] . '\-]*)?[' . $tmp['domain_valid_chars'] . ']\.)';
        $tmp['domain_valid_unicode_chars'] = '[^\p{P}\p{Z}\p{C}' . $tmp['invalid_characters'] . $tmp['spaces'] . ']';

        $gTLD = '삼성|닷컴|닷넷|香格里拉|餐厅|食品|飞利浦|電訊盈科|集团|通販|购物|谷歌|诺基亚|联通|网络|网站|网店|网址|组织机构|移动|珠宝|点看|游戏|淡马锡|机构|書籍|时尚|新闻|政府|政务|手表|手机|我爱你|慈善|微博|广东|工行|家電|娱乐|天主教|大拿|大众汽车|在线|嘉里大酒店|嘉里|商标|商店|商城|公益|公司|八卦|健康|信息|佛山|企业|中文网|中信|世界|ポイント|ファッション|セール|ストア|コム|グーグル|クラウド|みんな|คอม|संगठन|नेट|कॉम|همراه|موقع|موبايلي|كوم|كاثوليك|عرب|شبكة|بيتك|بازار|العليان|ارامكو|اتصالات|ابوظبي|קום|сайт|рус|орг|онлайн|москва|ком|католик|дети|zuerich|zone|zippo|zip|zero|zara|zappos|yun|youtube|you|yokohama|yoga|yodobashi|yandex|yamaxun|yahoo|yachts|xyz|xxx|xperia|xin|xihuan|xfinity|xerox|xbox|wtf|wtc|wow|world|works|work|woodside|wolterskluwer|wme|winners|wine|windows|win|williamhill|wiki|wien|whoswho|weir|weibo|wedding|wed|website|weber|webcam|weatherchannel|weather|watches|watch|warman|wanggou|wang|walter|walmart|wales|vuelos|voyage|voto|voting|vote|volvo|volkswagen|vodka|vlaanderen|vivo|viva|vistaprint|vista|vision|visa|virgin|vip|vin|villas|viking|vig|video|viajes|vet|versicherung|vermögensberatung|vermögensberater|verisign|ventures|vegas|vanguard|vana|vacations|ups|uol|uno|university|unicom|uconnect|ubs|ubank|tvs|tushu|tunes|tui|tube|trv|trust|travelersinsurance|travelers|travelchannel|travel|training|trading|trade|toys|toyota|town|tours|total|toshiba|toray|top|tools|tokyo|today|tmall|tkmaxx|tjx|tjmaxx|tirol|tires|tips|tiffany|tienda|tickets|tiaa|theatre|theater|thd|teva|tennis|temasek|telefonica|telecity|tel|technology|tech|team|tdk|tci|taxi|tax|tattoo|tatar|tatamotors|target|taobao|talk|taipei|tab|systems|symantec|sydney|swiss|swiftcover|swatch|suzuki|surgery|surf|support|supply|supplies|sucks|style|study|studio|stream|store|storage|stockholm|stcgroup|stc|statoil|statefarm|statebank|starhub|star|staples|stada|srt|srl|spreadbetting|spot|spiegel|space|soy|sony|song|solutions|solar|sohu|software|softbank|social|soccer|sncf|smile|smart|sling|skype|sky|skin|ski|site|singles|sina|silk|shriram|showtime|show|shouji|shopping|shop|shoes|shiksha|shia|shell|shaw|sharp|shangrila|sfr|sexy|sex|sew|seven|ses|services|sener|select|seek|security|secure|seat|search|scot|scor|scjohnson|science|schwarz|schule|school|scholarships|schmidt|schaeffler|scb|sca|sbs|sbi|saxo|save|sas|sarl|sapo|sap|sanofi|sandvikcoromant|sandvik|samsung|samsclub|salon|sale|sakura|safety|safe|saarland|ryukyu|rwe|run|ruhr|rugby|rsvp|room|rogers|rodeo|rocks|rocher|rmit|rip|rio|ril|rightathome|ricoh|richardli|rich|rexroth|reviews|review|restaurant|rest|republican|report|repair|rentals|rent|ren|reliance|reit|reisen|reise|rehab|redumbrella|redstone|red|recipes|realty|realtor|realestate|read|raid|radio|racing|qvc|quest|quebec|qpon|pwc|pub|prudential|pru|protection|property|properties|promo|progressive|prof|productions|prod|pro|prime|press|praxi|pramerica|post|porn|politie|poker|pohl|pnc|plus|plumbing|playstation|play|place|pizza|pioneer|pink|ping|pin|pid|pictures|pictet|pics|piaget|physio|photos|photography|photo|phone|philips|phd|pharmacy|pfizer|pet|pccw|pay|passagens|party|parts|partners|pars|paris|panerai|panasonic|pamperedchef|page|ovh|ott|otsuka|osaka|origins|orientexpress|organic|org|orange|oracle|open|ooo|onyourside|online|onl|ong|one|omega|ollo|oldnavy|olayangroup|olayan|okinawa|office|off|observer|obi|nyc|ntt|nrw|nra|nowtv|nowruz|now|norton|northwesternmutual|nokia|nissay|nissan|ninja|nikon|nike|nico|nhk|ngo|nfl|nexus|nextdirect|next|news|newholland|new|neustar|network|netflix|netbank|net|nec|nba|navy|natura|nationwide|name|nagoya|nadex|nab|mutuelle|mutual|museum|mtr|mtpc|mtn|msd|movistar|movie|mov|motorcycles|moto|moscow|mortgage|mormon|mopar|montblanc|monster|money|monash|mom|moi|moe|moda|mobily|mobile|mobi|mma|mls|mlb|mitsubishi|mit|mint|mini|mil|microsoft|miami|metlife|merckmsd|meo|menu|men|memorial|meme|melbourne|meet|media|med|mckinsey|mcdonalds|mcd|mba|mattel|maserati|marshalls|marriott|markets|marketing|market|map|mango|management|man|makeup|maison|maif|madrid|macys|luxury|luxe|lupin|lundbeck|ltda|ltd|lplfinancial|lpl|love|lotto|lotte|london|lol|loft|locus|locker|loans|loan|lixil|living|live|lipsy|link|linde|lincoln|limo|limited|lilly|like|lighting|lifestyle|lifeinsurance|life|lidl|liaison|lgbt|lexus|lego|legal|lefrak|leclerc|lease|lds|lawyer|law|latrobe|latino|lat|lasalle|lanxess|landrover|land|lancome|lancia|lancaster|lamer|lamborghini|ladbrokes|lacaixa|kyoto|kuokgroup|kred|krd|kpn|kpmg|kosher|komatsu|koeln|kiwi|kitchen|kindle|kinder|kim|kia|kfh|kerryproperties|kerrylogistics|kerryhotels|kddi|kaufen|juniper|juegos|jprs|jpmorgan|joy|jot|joburg|jobs|jnj|jmp|jll|jlc|jio|jewelry|jetzt|jeep|jcp|jcb|java|jaguar|iwc|iveco|itv|itau|istanbul|ist|ismaili|iselect|irish|ipiranga|investments|intuit|international|intel|int|insure|insurance|institute|ink|ing|info|infiniti|industries|immobilien|immo|imdb|imamat|ikano|iinet|ifm|ieee|icu|ice|icbc|ibm|hyundai|hyatt|hughes|htc|hsbc|how|house|hotmail|hotels|hoteles|hot|hosting|host|hospital|horse|honeywell|honda|homesense|homes|homegoods|homedepot|holiday|holdings|hockey|hkt|hiv|hitachi|hisamitsu|hiphop|hgtv|hermes|here|helsinki|help|healthcare|health|hdfcbank|hdfc|hbo|haus|hangout|hamburg|hair|guru|guitars|guide|guge|gucci|guardian|group|grocery|gripe|green|gratis|graphics|grainger|gov|got|gop|google|goog|goodyear|goodhands|goo|golf|goldpoint|gold|godaddy|gmx|gmo|gmbh|gmail|globo|global|gle|glass|glade|giving|gives|gifts|gift|ggee|george|genting|gent|gea|gdn|gbiz|garden|gap|games|game|gallup|gallo|gallery|gal|fyi|futbol|furniture|fund|fun|fujixerox|fujitsu|ftr|frontier|frontdoor|frogans|frl|fresenius|free|fox|foundation|forum|forsale|forex|ford|football|foodnetwork|food|foo|fly|flsmidth|flowers|florist|flir|flights|flickr|fitness|fit|fishing|fish|firmdale|firestone|fire|financial|finance|final|film|fido|fidelity|fiat|ferrero|ferrari|feedback|fedex|fast|fashion|farmers|farm|fans|fan|family|faith|fairwinds|fail|fage|extraspace|express|exposed|expert|exchange|everbank|events|eus|eurovision|etisalat|esurance|estate|esq|erni|ericsson|equipment|epson|epost|enterprises|engineering|engineer|energy|emerck|email|education|edu|edeka|eco|eat|earth|dvr|dvag|durban|dupont|duns|dunlop|duck|dubai|dtv|drive|download|dot|doosan|domains|doha|dog|dodge|doctor|docs|dnp|diy|dish|discover|discount|directory|direct|digital|diet|diamonds|dhl|dev|design|desi|dentist|dental|democrat|delta|deloitte|dell|delivery|degree|deals|dealer|deal|dds|dclk|day|datsun|dating|date|data|dance|dad|dabur|cyou|cymru|cuisinella|csc|cruises|cruise|crs|crown|cricket|creditunion|creditcard|credit|courses|coupons|coupon|country|corsica|coop|cool|cookingchannel|cooking|contractors|contact|consulting|construction|condos|comsec|computer|compare|company|community|commbank|comcast|com|cologne|college|coffee|codes|coach|clubmed|club|cloud|clothing|clinique|clinic|click|cleaning|claims|cityeats|city|citic|citi|citadel|cisco|circle|cipriani|church|chrysler|chrome|christmas|chloe|chintai|cheap|chat|chase|channel|chanel|cfd|cfa|cern|ceo|center|ceb|cbs|cbre|cbn|cba|catholic|catering|cat|casino|cash|caseih|case|casa|cartier|cars|careers|career|care|cards|caravan|car|capitalone|capital|capetown|canon|cancerresearch|camp|camera|cam|calvinklein|call|cal|cafe|cab|bzh|buzz|buy|business|builders|build|bugatti|budapest|brussels|brother|broker|broadway|bridgestone|bradesco|box|boutique|bot|boston|bostik|bosch|boots|booking|book|boo|bond|bom|bofa|boehringer|boats|bnpparibas|bnl|bmw|bms|blue|bloomberg|blog|blockbuster|blanco|blackfriday|black|biz|bio|bingo|bing|bike|bid|bible|bharti|bet|bestbuy|best|berlin|bentley|beer|beauty|beats|bcn|bcg|bbva|bbt|bbc|bayern|bauhaus|basketball|baseball|bargains|barefoot|barclays|barclaycard|barcelona|bar|bank|band|bananarepublic|banamex|baidu|baby|azure|axa|aws|avianca|autos|auto|author|auspost|audio|audible|audi|auction|attorney|athleta|associates|asia|asda|arte|art|arpa|army|archi|aramco|arab|aquarelle|apple|app|apartments|aol|anz|anquan|android|analytics|amsterdam|amica|amfam|amex|americanfamily|americanexpress|alstom|alsace|ally|allstate|allfinanz|alipay|alibaba|alfaromeo|akdn|airtel|airforce|airbus|aigo|aig|agency|agakhan|africa|afl|afamilycompany|aetna|aero|aeg|adult|ads|adac|actor|active|aco|accountants|accountant|accenture|academy|abudhabi|abogado|able|abc|abbvie|abbott|abb|abarth|aarp|aaa|onion';
        $ccTLD = '한국|香港|澳門|新加坡|台灣|台湾|中國|中国|გე|ไทย|ලංකා|ഭാരതം|ಭಾರತ|భారత్|சிங்கப்பூர்|இலங்கை|இந்தியா|ଭାରତ|ભારત|ਭਾਰਤ|ভাৰত|ভারত|বাংলা|भारोत|भारतम्|भारत|ڀارت|پاکستان|مليسيا|مصر|قطر|فلسطين|عمان|عراق|سورية|سودان|تونس|بھارت|بارت|ایران|امارات|المغرب|السعودية|الجزائر|الاردن|հայ|қаз|укр|срб|рф|мон|мкд|ею|бел|бг|ελ|zw|zm|za|yt|ye|ws|wf|vu|vn|vi|vg|ve|vc|va|uz|uy|us|um|uk|ug|ua|tz|tw|tv|tt|tr|tp|to|tn|tm|tl|tk|tj|th|tg|tf|td|tc|sz|sy|sx|sv|su|st|ss|sr|so|sn|sm|sl|sk|sj|si|sh|sg|se|sd|sc|sb|sa|rw|ru|rs|ro|re|qa|py|pw|pt|ps|pr|pn|pm|pl|pk|ph|pg|pf|pe|pa|om|nz|nu|nr|np|no|nl|ni|ng|nf|ne|nc|na|mz|my|mx|mw|mv|mu|mt|ms|mr|mq|mp|mo|mn|mm|ml|mk|mh|mg|mf|me|md|mc|ma|ly|lv|lu|lt|ls|lr|lk|li|lc|lb|la|kz|ky|kw|kr|kp|kn|km|ki|kh|kg|ke|jp|jo|jm|je|it|is|ir|iq|io|in|im|il|ie|id|hu|ht|hr|hn|hm|hk|gy|gw|gu|gt|gs|gr|gq|gp|gn|gm|gl|gi|gh|gg|gf|ge|gd|gb|ga|fr|fo|fm|fk|fj|fi|eu|et|es|er|eh|eg|ee|ec|dz|do|dm|dk|dj|de|cz|cy|cx|cw|cv|cu|cr|co|cn|cm|cl|ck|ci|ch|cg|cf|cd|cc|ca|bz|by|bw|bv|bt|bs|br|bq|bo|bn|bm|bl|bj|bi|bh|bg|bf|be|bd|bb|ba|az|ax|aw|au|at|as|ar|aq|ao|an|am|al|ai|ag|af|ae|ad|ac';

        $tmp['valid_gTLD'] = '(?:(?:' . $gTLD . ')(?=[^0-9a-z@]|$))';
        $tmp['valid_ccTLD'] = '(?:(?:' . $ccTLD . ')(?=[^0-9a-z@]|$))';
        $tmp['valid_special_ccTLD'] = '(?:(?:' . 'co|tv' . ')(?=[^0-9a-z@]|$))';
        $tmp['valid_punycode'] = '(?:xn--[0-9a-z]+)';

        $tmp['valid_domain'] = '(?:'                                            // subdomains + domain + TLD
            . $tmp['valid_subdomain'] . '+' . $tmp['valid_domain_name']         // e.g. www.twitter.com, foo.co.jp, bar.co.uk
            . '(?:' . $tmp['valid_gTLD'] . '|' . $tmp['valid_ccTLD'] . '|' . $tmp['valid_punycode'] . '))'
            . '|(?:'                                                                // domain + gTLD | some ccTLD
            . $tmp['valid_domain_name']                                         // e.g. twitter.com
            . '(?:' . $tmp['valid_gTLD'] . '|' . $tmp['valid_punycode'] . '|' . $tmp['valid_special_ccTLD'] . ')'
            . ')'
            . '|(?:(?:(?<=http:\/\/)|(?<=https:\/\/))'
            . '(?:'
            . '(?:' . $tmp['valid_domain_name'] . $tmp['valid_ccTLD'] . ')' // protocol + domain + ccTLD
            . '|(?:'                                                                // protocol + unicode domain + TLD
            . $tmp['domain_valid_unicode_chars'] . '+\.'
            . '(?:' . $tmp['valid_gTLD'] . '|' . $tmp['valid_ccTLD'] . ')'
            . ')'
            . ')'
            . ')'
            . '|(?:'                                                                // domain + ccTLD + '/'
            . $tmp['valid_domain_name'] . $tmp['valid_ccTLD'] . '(?=\/)'            // e.g. t.co/
            . ')';
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

        # These URL validation pattern strings are based on the ABNF from RFC 3986
        $tmp['validate_url_unreserved'] = '[a-z\p{Cyrillic}0-9\-._~]';
        $tmp['validate_url_pct_encoded'] = '(?:%[0-9a-f]{2})';
        $tmp['validate_url_sub_delims'] = '[!$&\'()*+,;=]';
        $tmp['validate_url_pchar'] = '(?:' . $tmp['validate_url_unreserved'] . '|' . $tmp['validate_url_pct_encoded'] . '|' . $tmp['validate_url_sub_delims'] . '|[:\|@])'; #/iox

        $tmp['validate_url_userinfo'] = '(?:' . $tmp['validate_url_unreserved'] . '|' . $tmp['validate_url_pct_encoded'] . '|' . $tmp['validate_url_sub_delims'] . '|:)*'; #/iox

        $tmp['validate_url_dec_octet'] = '(?:[0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])'; #/i
        $tmp['validate_url_ipv4'] = '(?:' . $tmp['validate_url_dec_octet'] . '(?:\.' . $tmp['validate_url_dec_octet'] . '){3})'; #/iox
        # Punting on real IPv6 validation for now
        $tmp['validate_url_ipv6'] = '(?:\[[a-f0-9:\.]+\])'; #/i
        # Also punting on IPvFuture for now
        $tmp['validate_url_ip'] = '(?:' . $tmp['validate_url_ipv4'] . '|' . $tmp['validate_url_ipv6'] . ')'; #/iox
        # This is more strict than the rfc specifies
        $tmp['validate_url_subdomain_segment'] = '(?:[a-z0-9](?:[a-z0-9_\-]*[a-z0-9])?)'; #/i
        $tmp['validate_url_domain_segment'] = '(?:[a-z0-9](?:[a-z0-9\-]*[a-z0-9])?)'; #/i
        $tmp['validate_url_domain_tld'] = '(?:[a-z](?:[a-z0-9\-]*[a-z0-9])?)'; #/i
        $tmp['validate_url_domain'] = '(?:(?:' . $tmp['validate_url_subdomain_segment'] . '\.)*(?:' . $tmp['validate_url_domain_segment'] . '\.)' . $tmp['validate_url_domain_tld'] . ')'; #/iox

        $tmp['validate_url_host'] = '(?:' . $tmp['validate_url_ip'] . '|' . $tmp['validate_url_domain'] . ')'; #/iox
        # Unencoded internationalized domains - this doesn't check for invalid UTF-8 sequences
        $tmp['validate_url_unicode_subdomain_segment'] = '(?:(?:[a-z0-9]|[^\x00-\x7f])(?:(?:[a-z0-9_\-]|[^\x00-\x7f])*(?:[a-z0-9]|[^\x00-\x7f]))?)'; #/ix
        $tmp['validate_url_unicode_domain_segment'] = '(?:(?:[a-z0-9]|[^\x00-\x7f])(?:(?:[a-z0-9\-]|[^\x00-\x7f])*(?:[a-z0-9]|[^\x00-\x7f]))?)'; #/ix
        $tmp['validate_url_unicode_domain_tld'] = '(?:(?:[a-z]|[^\x00-\x7f])(?:(?:[a-z0-9\-]|[^\x00-\x7f])*(?:[a-z0-9]|[^\x00-\x7f]))?)'; #/ix
        $tmp['validate_url_unicode_domain'] = '(?:(?:' . $tmp['validate_url_unicode_subdomain_segment'] . '\.)*(?:' . $tmp['validate_url_unicode_domain_segment'] . '\.)' . $tmp['validate_url_unicode_domain_tld'] . ')'; #/iox

        $tmp['validate_url_unicode_host'] = '(?:' . $tmp['validate_url_ip'] . '|' . $tmp['validate_url_unicode_domain'] . ')'; #/iox

        $tmp['validate_url_port'] = '[0-9]{1,5}';

        $re['validate_url_unicode_authority'] = '/'
            . '(?:(' . $tmp['validate_url_userinfo'] . ')@)?' #  $1 userinfo
            . '(' . $tmp['validate_url_unicode_host'] . ')'   #  $2 host
            . '(?::(' . $tmp['validate_url_port'] . '))?'     #  $3 port
            . '/iux';

        $re['validate_url_authority'] = '/'
            . '(?:(' . $tmp['validate_url_userinfo'] . ')@)?' #  $1 userinfo
            . '(' . $tmp['validate_url_host'] . ')'           #  $2 host
            . '(?::(' . $tmp['validate_url_port'] . '))?'     #  $3 port
            . '/ix';

        $re['validate_url_scheme'] = '/(?:[a-z][a-z0-9+\-.]*)/i';
        $re['validate_url_path'] = '/(\/' . $tmp['validate_url_pchar'] . '*)*/iu';
        $re['validate_url_query'] = '/(' . $tmp['validate_url_pchar'] . '|\/|\?)*/iu';
        $re['validate_url_fragment'] = '/(' . $tmp['validate_url_pchar'] . '|\/|\?)*/iu';

        # Modified version of RFC 3986 Appendix B
        $re['validate_url_unencoded'] = '/^' #  Full URL
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
            . ')?$/iux';

        $re['invalid_characters'] = '/[' . $tmp['invalid_characters'] . ']/u';

        $re['rtl_chars'] = '/[' . $tmp['rtl_chars'] . ']/iu';

        # Flag that initialization is complete:
        $initialized = true;
    }
}

# Cause regular expressions to be initialized as soon as this file is loaded:
Regex::__static();
