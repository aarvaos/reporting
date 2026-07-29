# _aArvaos_ - Reporting

A very light, handy and easy-to-use library to log and track all kind of elements you want to report.

## Concept

Whenever you need to keep track of anything happening in your code so you can audit at a later stage, you may use a `aarvaos\Report` object!

## Install

*Composer incoming!*

## Use

Simple use case demo:

```php

const NEUTRAL = 0;
const LIGHT = 1;
const NORMAL = 5;
const BAD = 8;
const VERY_BAD = 9;
const SERIOUS = 10;
const CRITICAL = 25;

$report = new \aarvaos\Report();

$report->log(LIGHT, "Something light happened.");
$report->log(SERIOUS, "Something serious happened!");
$report->log(-4, "Something negligible happened...");

$report->getSeverity(); // 10
$report->hasSeverityReached(VERY_BAD); // true
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

foreach($report->iterateLogs(\aarvaos\Report::SORT_SEVERITY_ASC, below: NORMAL) as $log) {

    // $log = Log<-4, ...>,
    // $log = Log<1, ...>.

}
```
