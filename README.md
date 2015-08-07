# PHP Hound

[![Build Status](https://travis-ci.org/alanwillms/php-hound.svg?branch=master)](https://travis-ci.org/alanwillms/php-hound)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alanwillms/php-hound/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alanwillms/php-hound/?branch=master)
[![Packagist Version](https://img.shields.io/packagist/v/alanwillms/php-hound.svg)](https://packagist.org/packages/alanwillms/php-hound)
[![Total Downloads](https://img.shields.io/packagist/dt/alanwillms/php-hound.svg)](https://packagist.org/packages/alanwillms/php-hound)

**This is a work in progress!**

PHP Hound runs a set of quality assurance tools for PHP and reduce results to
a single beautiful report.

It currently supports:

* [PHPCodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
* [PHPCopyPasteDetector](https://github.com/sebastianbergmann/phpcpd)
* [PHPMessDetector](https://github.com/phpmd/phpmd)

# Installation

This tool requires [Composer](https://getcomposer.org). If you have it, you can
install PHP Hound running:

```bash
composer global require alanwillms/php-hound
```

It's **important** that you make sure `~/.composer/bin` directory is in your
`PATH`.

# Command line usage

```bash
php-hound /some/directory # run for specific directory
php-hound /some/file.php # run for specific file
```
