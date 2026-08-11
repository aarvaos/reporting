# _aArvaos_ - Reporting

[![Packagist](https://img.shields.io/packagist/v/aarvaos/reporting)](https://packagist.org/packages/aarvaos/reporting)
[![CI](https://github.com/aarvaos/reporting/actions/workflows/ci.yml/badge.svg)](https://github.com/aarvaos/reporting/actions/workflows/ci.yml)
[![PHP Version min](https://img.shields.io/badge/php-8.5-777BB4)](https://www.php.net/releases/8.5)
[![PHP Version latest](https://img.shields.io/badge/php-%3E%3D8.1-777BB4)](https://www.php.net/releases/8.1)
[![PHPStan level 10](https://img.shields.io/badge/PHPStan-level%2010-blue)](https://github.com/aarvaos/reporting/actions/workflows/ci.yml)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A very light, handy and easy-to-use library to log and track all kind of elements you want to report.

## Concept

Whenever you need to keep track of anything happening in your code so you can audit at a later stage, you may use a `Aarvaos\Reporting\Report` object!

## Install

Use *Composer* `composer require aarvaos/reporting` and you're done!

## Use

Simple use case demo:

```php

const NEUTRAL = 0;
const SERIOUS = 10;
const CRITICAL = 25;

$report = new \Aarvaos\Reporting\Report();

$report->log(1, "Something light happened.");
$report->log(SERIOUS, "Something serious happened!");
$report->log(-4, "Something negligible happened...");

$report->getSeverity(); // 10
$report->hasSeverityReached(9); // true
$report->hasSeverityReached(CRITICAL); // false
```

Of course you can also retrieve the recorded elements:

```php
$report->getLogs(); // [Log<1, ...>, Log<10, ...>, Log<-4, ...>]

foreach($report->iterateLogs() as $log) {

    // $log = Log<1, ...>,
    // $log = Log<10, ...>,
    // $log = Log<-4, ...>.

}
```

... And even filter and sort:

```php
$report->getLogs(-4); // [Log<-4, ...>]

foreach($report->iterateLogs(\Aarvaos\Reporting\Report::SORT_SEVERITY_ASC, below: 5) as $log) {

    // $log = Log<-4, ...>,
    // $log = Log<1, ...>.

}
```
