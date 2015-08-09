# PHP Hound

[![Build Status](https://travis-ci.org/alanwillms/php-hound.svg?branch=master)](https://travis-ci.org/alanwillms/php-hound)
[![Code Climate](https://codeclimate.com/github/alanwillms/php-hound/badges/gpa.svg)](https://codeclimate.com/github/alanwillms/php-hound)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alanwillms/php-hound/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alanwillms/php-hound/?branch=master)
[![Test Coverage](https://codeclimate.com/github/alanwillms/php-hound/badges/coverage.svg)](https://codeclimate.com/github/alanwillms/php-hound/coverage)
[![Packagist Version](https://img.shields.io/packagist/v/alanwillms/php-hound.svg)](https://packagist.org/packages/alanwillms/php-hound)
[![Total Downloads](https://img.shields.io/packagist/dt/alanwillms/php-hound.svg)](https://packagist.org/packages/alanwillms/php-hound)

**This is a work in progress!**

PHP Hound runs a set of quality assurance tools for PHP and reduce results to
a single beautiful report.

It currently supports:

* [PHPCodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer): code style and
  identation according to [PSR-2](http://www.php-fig.org/psr/psr-2/).
* [PHPCopyPasteDetector](https://github.com/sebastianbergmann/phpcpd):
  duplicated code detection.
* [PHPMessDetector](https://github.com/phpmd/phpmd): checks for complex, unused,
  broken or unclear code.

## Installation

PHP Hound can be installed through [Composer](https://getcomposer.org).

### Local installation

To install it locally, run the following command:

```bash
composer require alanwillms/php-hound
```

Then you can execute PHP Hound by running `./vendor/bin/php-hound`.

### Global installation

You can install PHP Hound globally with:

```bash
composer global require alanwillms/php-hound
```

Then you can add `~/.composer/bin` directory to your `PATH`, so you can
simply type `php-hound` to run it from anywhere. If you want to do this,
add the following to your `~/.profile` (or `~/.bashrc`) file:

```bash
export PATH="$HOME/.composer/vendor/bin:$PATH"
```

If you want to apply the changes to your current terminal session, run
`source ~/.profile` (or `source ~/bashrc`).

## Command line usage
Basic usage:

```bash
# Analyze current directory files
php-hound

# Analyze "informed/directory/" files
php-hound informed/directory/

# Analyze "script.php" file
php-hound script.php
```

You can run `php-hound --help` to display a list of all available options.

```
php-hound [optional arguments] [path to be analysed]

Optional Arguments:

    --help
        Prints a usage statement

    -i <directory>, --ignore <directory>
        Ignore a comma-separated list of directories. Directories called
        "vendor", "tests", "features" and "spec", will be ignored by default.

    -v, --version
        Prints installed version
```
