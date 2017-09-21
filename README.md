# Twitter Text (PHP Edition) #

A library of PHP classes that provide auto-linking and extraction of usernames,
lists, hashtags and URLs from tweets.  Originally created from twitter-text-rb
and twitter-text-java projects by Matt Sanford and ported to PHP by Mike
Cochrane, this library has been improved and made more complete by Nick Pope.

<p align="center">
    <a href="https://travis-ci.org/nojimage/twitter-text-php" target="_blank">
        <img alt="Build Status" src="https://img.shields.io/travis/nojimage/twitter-text-php/master.svg?style=flat-square">
    </a>
    <a href="https://codecov.io/gh/nojimage/twitter-text-php" target="_blank">
        <img alt="Codecov" src="https://img.shields.io/codecov/c/github/nojimage/twitter-text-php.svg?style=flat-square">
    </a>
    <a href="https://packagist.org/packages/nojimage/twitter-text-php" target="_blank">
        <img alt="Latest Stable Version" src="https://img.shields.io/packagist/v/nojimage/twitter-text-php.svg?style=flat-square">
    </a>
</p>

## Features ##

### Autolink ##

 - Add links to all matching Twitter usernames (no account verification).
 - Add links to all user lists (of the form @username/list-name).
 - Add links to all valid hashtags.
 - Add links to all URLs.
 - Support for international character sets.

### Extractor ###

 - Extract mentioned Twitter usernames (from anywhere in the tweet).
 - Extract replied to Twitter usernames (from start of the tweet).
 - Extract all user lists (of the form @username/list-name).
 - Extract all valid hashtags.
 - Extract all URLs.
 - Support for international character sets.

### Hit Highlighter ###

 - Highlight text specifed by a range by surrounding with a tag.
 - Support for highlighting when tweet has already been autolinked.
 - Support for international character sets.

### Validation ###

 - Validate different twitter text elements.
 - Support for international character sets.

## Examples ##

For examples, please see `tests/example.php` which you can view in a browser or
run from the command line.

## Conformance ##

You'll need the test data which is in YAML format from the following
repository:

    https://github.com/twitter/twitter-text

    https://github.com/symfony/Yaml

Both requirements already included in `composer.json`, so you should just need to run:

    curl -s https://getcomposer.org/installer | php
    php composer.phar install

There are a couple of options for testing conformance:

- Run `phpunit` in from the root folder of the project.

## Thanks & Contributions ##

The bulk of this library is from the heroic efforts of:

 - Matt Sanford (https://github.com/mzsanford): For the orignal Ruby and Java implementions.
 - Mike Cochrane (https://github.com/mikenz): For the initial PHP code.
 - Nick Pope (https://github.com/ngnpope): For the bulk of the maintenance work to date.
 - Takashi Nojima (https://github.com/nojimage): For ongoing maintenance work.
