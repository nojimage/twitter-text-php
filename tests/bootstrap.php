<?php

# Set up error reporting:
if (!defined('E_DEPRECATED')) define('E_DEPRECATED', 8192);
error_reporting(E_ALL | E_STRICT | E_DEPRECATED);

# Set default timezone to hide warnings:
date_default_timezone_set('Europe/London');

# Set encoding for manipulation of multi byte strings:
mb_internal_encoding('UTF-8');

# Set up path variables.
$ROOT = dirname(dirname(__FILE__));
$DATA = $ROOT.'/vendor/twitter/twitter-text-conformance';

if (!defined('DATA')) {
    define('DATA', $DATA);
}

$loader = require __DIR__ . "/../vendor/autoload.php";
$loader->addPsr4('Nojimage\\Twitter\\', __DIR__);

################################################################################
# vim:et:ft=php:nowrap:sts=2:sw=2:ts=2
