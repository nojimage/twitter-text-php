<?php

/**
 * functions for Test
 *
 * @author     Mike Cochrane <mikec@mikenz.geek.nz>
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 */

/**
 *
 */
function pretty_format($a)
{
    if (is_bool($a)) {
        return $a ? 'true' : 'false';
    }
    if (is_string($a)) {
        return "'${a}'";
    }
    return preg_replace(array(
        "/\n/", '/ +\[/', '/ +\)/', '/Array +\(/', '/(?<!\() \[/', '/\[([^]]+)\]/',
        '/"(\d+)"/', '/(?<=^| )\((?= )/', '/(?<= )\)(?=$| )/',
        ), array(
        ' ', ' [', ' )', '(', ', [', '"$1"', '$1', '[', ']',
        ), print_r($a, true));
}

/**
 *
 */
function output_preamble()
{
    global $browser;
    if (!$browser) {
        return;
    }
    echo <<<EOHTML
<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<title>Twitter Text (PHP Edition) Library » Conformance</title>
<style>
body { font-family: sans-serif; font-size: 12px; }
.pass { color: #090; }
.fail { color: #f00; }
</style>
</head>
<body>
EOHTML;
}

/**
 *
 */
function output_closing()
{
    global $browser;
    if (!$browser) {
        return;
    }
    echo <<<EOHTML
</body>
</html>
EOHTML;
}

/**
 *
 */
function output_h1($text)
{
    global $browser;
    if ($browser) {
        echo '<h1>' . $text . '</h1>';
    } else {
        echo "\033[1m" . $text . "\033[0m" . PHP_EOL;
        echo str_repeat('=', mb_strlen($text)) . PHP_EOL;
    }
    echo PHP_EOL;
}

/**
 *
 */
function output_h2($text)
{
    global $browser;
    if ($browser) {
        echo '<h2>' . $text . '</h2>';
    } else {
        echo "\033[1m" . $text . "\033[0m" . PHP_EOL;
        echo str_repeat('-', mb_strlen($text)) . PHP_EOL;
    }
    echo PHP_EOL;
}

/**
 *
 */
function output_h3($text)
{
    global $browser;
    if ($browser) {
        echo '<h3>' . $text . '</h3>';
    } else {
        echo "\033[1m" . $text . "\033[0m" . PHP_EOL;
    }
    echo PHP_EOL;
}

/**
 *
 */
function output_skip_test()
{
    global $browser;
    $text = 'Skipping Test...';
    if ($browser) {
        echo '<p>' . $text . '</p>';
    } else {
        echo "   \033[1;35m" . $text . "\033[0m" . PHP_EOL;
    }
    echo PHP_EOL;
}
