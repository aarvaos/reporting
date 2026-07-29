<?php

namespace Aarvaos\Reporting\Tests\Unit;

use Aarvaos\Reporting\Logs\Log;
use Aarvaos\Reporting\Report;
use Aarvaos\Reporting\Tests\Stubs\CustomLog;
use Aarvaos\Reporting\Tests\Stubs\ExtendedLog;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Report::class)]
#[CoversClass(Log::class)]
class ReportTest extends TestCase
{
    #[Test]
    public function testLogging(): void
    {

        $report = new Report();

        $this->assertSame(0, $report->countLogs());
        $this->assertSame([], $report->getLogs());

        $report->log(0, 'Hi!');

        $this->assertSame(1, $report->countLogs());
        $this->assertEquals(['Hi!'], $report->getLogs());

        $report->log(1, 'Hello!');

        $this->assertSame(2, $report->countLogs());
        $this->assertEquals(['Hi!', 'Hello!'], $report->getLogs());

    }

    #[Test]
    public function testCount(): void
    {

        $this->assertSame(0, count(new Report()));
        $this->assertSame(3, count(
            (new Report())
                ->log(0, 'Hi!')
                ->log(-256, 'Hi -256!')
                ->log(777, 'Hi 777!')
        ));
        $this->assertSame(2, count(
            (new Report())
                ->log(0, 'Hi!')
                ->log(0, 'Hi!')
        ));

    }

    #[Test]
    public function testGetMaxLevel(): void
    {

        $this->assertNull((new Report())->getMaxLevel());
        $this->assertSame(0, (new Report())
            ->log(0, 'Hi!')
            ->getMaxLevel());
        $this->assertSame(777, (new Report())
            ->log(777, 'Hi 777!')
            ->getMaxLevel());
        $this->assertSame(-256, (new Report())
            ->log(-256, 'Hi -256!')
            ->getMaxLevel());

        $this->assertSame(0, (new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->getMaxLevel());
        $this->assertSame(0, (new Report())
            ->log(-256, 'Hi -256!')
            ->log(0, 'Hi!')
            ->getMaxLevel());

        $this->assertSame(777, (new Report())
            ->log(0, 'Hi!')
            ->log(777, 'Hi 777!')
            ->getMaxLevel());
        $this->assertSame(777, (new Report())
            ->log(777, 'Hi 777!')
            ->log(0, 'Hi!')
            ->getMaxLevel());

        $this->assertSame(777, (new Report())
            ->log(777, 'Hi 777!')
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->getMaxLevel());
        $this->assertSame(777, (new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->getMaxLevel());

    }

    #[Test]
    public function testGetMinLevel(): void
    {

        $this->assertNull((new Report())->getMinLevel());
        $this->assertSame(0, (new Report())
            ->log(0, 'Hi!')
            ->getMinLevel());
        $this->assertSame(777, (new Report())
            ->log(777, 'Hi 777!')
            ->getMinLevel());
        $this->assertSame(-256, (new Report())
            ->log(-256, 'Hi -256!')
            ->getMinLevel());

        $this->assertSame(-256, (new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->getMinLevel());
        $this->assertSame(-256, (new Report())
            ->log(-256, 'Hi -256!')
            ->log(0, 'Hi!')
            ->getMinLevel());

        $this->assertSame(0, (new Report())
            ->log(0, 'Hi!')
            ->log(777, 'Hi 777!')
            ->getMinLevel());
        $this->assertSame(0, (new Report())
            ->log(777, 'Hi 777!')
            ->log(0, 'Hi!')
            ->getMinLevel());

        $this->assertSame(-256, (new Report())
            ->log(777, 'Hi 777!')
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->getMinLevel());
        $this->assertSame(-256, (new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->getMinLevel());

    }

    #[Test]
    public function testGetSeverity(): void
    {

        $this->assertNull((new Report())->getSeverity());

        $this->assertSame(0, (new Report())
            ->log(0, 'Hi!')
            ->getSeverity());
        $this->assertSame(777, (new Report())
            ->log(777, 'Hi 777!')
            ->getSeverity());
        $this->assertSame(-256, (new Report())
            ->log(-256, 'Hi -256!')
            ->getSeverity());

        $this->assertSame(777, (new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->getSeverity());

        $this->assertSame(-256, (new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->getSeverity());

    }

    #[Test]
    public function testHasSeverityReached(): void
    {

        $this->assertNull((new Report())->hasSeverityReached(1));
        $this->assertNull((new Report())->hasSeverityReached(1, true));
        $this->assertNull((new Report())->hasSeverityReached(-1));
        $this->assertNull((new Report())->hasSeverityReached(-1, true));
        $this->assertNull((new Report())->hasSeverityReached(0));
        $this->assertNull((new Report())->hasSeverityReached(0, true));

        $this->assertTrue((new Report())
            ->log(0, 'Hi!')
            ->hasSeverityReached(0));
        $this->assertFalse((new Report())
            ->log(0, 'Hi!')
            ->hasSeverityReached(0, true));
        $this->assertTrue((new Report())
            ->log(0, 'Hi!')
            ->hasSeverityReached(-1));
        $this->assertTrue((new Report())
            ->log(0, 'Hi!')
            ->hasSeverityReached(-1, true));
        $this->assertFalse((new Report())
            ->log(0, 'Hi!')
            ->hasSeverityReached(1));
        $this->assertFalse((new Report())
            ->log(0, 'Hi!')
            ->hasSeverityReached(1, true));

        $this->assertTrue((new Report())
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(0));
        $this->assertTrue((new Report())
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(0, true));
        $this->assertTrue((new Report())
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(-256));
        $this->assertTrue((new Report())
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(-256, true));
        $this->assertTrue((new Report())
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(777));
        $this->assertFalse((new Report())
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(777, true));
        $this->assertTrue((new Report())
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(776));
        $this->assertTrue((new Report())
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(776, true));
        $this->assertFalse((new Report())
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(778));
        $this->assertFalse((new Report())
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(778, true));

        $this->assertFalse((new Report())
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(0));
        $this->assertFalse((new Report())
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(0, true));
        $this->assertFalse((new Report())
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(777));
        $this->assertFalse((new Report())
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(777, true));
        $this->assertTrue((new Report())
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(-256));
        $this->assertFalse((new Report())
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(-256, true));
        $this->assertTrue((new Report())
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(-257));
        $this->assertTrue((new Report())
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(-257, true));
        $this->assertFalse((new Report())
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(-255));
        $this->assertFalse((new Report())
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(-255, true));

        $this->assertTrue((new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(0));
        $this->assertTrue((new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(0, true));
        $this->assertTrue((new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(-256));
        $this->assertTrue((new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(-256, true));
        $this->assertTrue((new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(-255));
        $this->assertTrue((new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(-255, true));
        $this->assertTrue((new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(-257));
        $this->assertTrue((new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(-257, true));
        $this->assertTrue((new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(777));
        $this->assertFalse((new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(777, true));
        $this->assertTrue((new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(776));
        $this->assertTrue((new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(776, true));
        $this->assertFalse((new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(778));
        $this->assertFalse((new Report())
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(778, true));

    }

    #[Test]
    public function testHasSeverityReached_reversed(): void
    {

        $this->assertNull((new Report(true))->hasSeverityReached(1));
        $this->assertNull((new Report(true))->hasSeverityReached(1, true));
        $this->assertNull((new Report(true))->hasSeverityReached(-1));
        $this->assertNull((new Report(true))->hasSeverityReached(-1, true));
        $this->assertNull((new Report(true))->hasSeverityReached(0));
        $this->assertNull((new Report(true))->hasSeverityReached(0, true));

        $this->assertTrue((new Report(true))
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(777));
        $this->assertFalse((new Report(true))
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(777, true));
        $this->assertFalse((new Report(true))
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(776));
        $this->assertFalse((new Report(true))
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(776, true));
        $this->assertTrue((new Report(true))
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(778));
        $this->assertTrue((new Report(true))
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(778, true));

        $this->assertTrue((new Report(true))
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(-256));
        $this->assertFalse((new Report(true))
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(-256, true));
        $this->assertFalse((new Report(true))
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(-257));
        $this->assertFalse((new Report(true))
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(-257, true));
        $this->assertTrue((new Report(true))
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(-255));
        $this->assertTrue((new Report(true))
            ->log(-256, 'Hi -256!')
            ->hasSeverityReached(-255, true));

        $this->assertTrue((new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(0));
        $this->assertTrue((new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(0, true));
        $this->assertTrue((new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(777));
        $this->assertTrue((new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(777, true));
        $this->assertTrue((new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(-256));
        $this->assertFalse((new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(-256, true));
        $this->assertTrue((new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(778));
        $this->assertTrue((new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(778, true));
        $this->assertTrue((new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(776));
        $this->assertTrue((new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(776, true));
        $this->assertTrue((new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(-255, true));
        $this->assertTrue((new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(-255, true));
        $this->assertFalse((new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(-257, true));
        $this->assertFalse((new Report(true))
            ->log(0, 'Hi!')
            ->log(-256, 'Hi -256!')
            ->log(777, 'Hi 777!')
            ->hasSeverityReached(-257, true));

    }

    #[Test]
    public function testGetLogs(): void
    {

        $this->assertSame([], (new Report())->getLogs());
        $this->assertEquals(
            ['Hi!'],
            (new Report())
                ->log(0, 'Hi!')
                ->getLogs()
        );
        $this->assertEquals(
            ['Hi!', 'Hi -256!', 'Hi 777!'],
            (new Report())
                ->log(0, 'Hi!')
                ->log(-256, 'Hi -256!')
                ->log(777, 'Hi 777!')
                ->getLogs()
        );
        $this->assertEquals(
            ['Hi#1!', 'Hi -256!', 'Hi#2!', 'Hi#3!', 'Hi 777!'],
            (new Report())
                ->log(0, 'Hi#1!')
                ->log(-256, 'Hi -256!')
                ->log(0, 'Hi#2!')
                ->log(0, 'Hi#3!')
                ->log(777, 'Hi 777!')
                ->getLogs()
        );

    }

    #[Test]
    public function testGetLogs_level(): void
    {

        $this->assertSame([], (new Report())->getLogs(1));
        $this->assertSame(
            [],
            (new Report())
                ->log(0, 'Hi!')
                ->getLogs(1)
        );
        $this->assertEquals(
            ['Hi!'],
            (new Report())
                ->log(0, 'Hi!')
                ->getLogs(0)
        );
        $this->assertEquals(
            ['Hi -256!'],
            (new Report())
                ->log(0, 'Hi!')
                ->log(-256, 'Hi -256!')
                ->log(777, 'Hi 777!')
                ->getLogs(-256)
        );
        $this->assertEquals(
            ['Hi#1!', 'Hi#2!', 'Hi#3!'],
            (new Report())
                ->log(0, 'Hi#1!')
                ->log(-256, 'Hi -256!')
                ->log(0, 'Hi#2!')
                ->log(0, 'Hi#3!')
                ->log(777, 'Hi 777!')
                ->getLogs(0)
        );
        $this->assertSame(
            [
                $l1 = new Log(0, 'Hi#1!'),
                $l2 = new Log(0, 'Hi#2!'),
                $l3 = new Log(0, 'Hi#3!'),
            ],
            (new Report())
                ->logCustom($l1)
                ->logCustom(new Log(-256, 'Hi -256!'))
                ->logCustom($l2)
                ->logCustom($l3)
                ->logCustom(new Log(777, 'Hi 777!'))
                ->getLogs(0)
        );

        $this->assertEquals(
            [],
            (new Report())
                ->log(0, 'Hi#1!')
                ->log(777, 'Hi 777!')
                ->log(0, 'Hi#2!')
                ->log(-256, 'Hi -256!')
                ->log(0, 'Hi#3!')
                ->log(777, 'Hi 777#2!')
                ->getLogs([])
        );
        $this->assertEquals(
            ['Hi#1!', 'Hi#2!', 'Hi#3!'],
            (new Report())
                ->log(0, 'Hi#1!')
                ->log(777, 'Hi 777!')
                ->log(0, 'Hi#2!')
                ->log(-256, 'Hi -256!')
                ->log(0, 'Hi#3!')
                ->log(777, 'Hi 777#2!')
                ->getLogs([0])
        );
        $this->assertEquals(
            [],
            (new Report())
                ->log(0, 'Hi#1!')
                ->log(-256, 'Hi -256!')
                ->log(0, 'Hi#2!')
                ->log(0, 'Hi#3!')
                ->log(777, 'Hi 777!')
                ->getLogs([1, 2, 3])
        );
        $this->assertEquals(
            ['Hi#1!', 'Hi#2!', 'Hi#3!'],
            (new Report())
                ->log(0, 'Hi#1!')
                ->log(777, 'Hi 777!')
                ->log(0, 'Hi#2!')
                ->log(-256, 'Hi -256!')
                ->log(0, 'Hi#3!')
                ->log(777, 'Hi 777#2!')
                ->getLogs([-1, 0, 1])
        );
        $this->assertEquals(
            ['Hi 777!', 'Hi -256!', 'Hi 777#2!'],
            array_map('strval', (new Report())
                ->log(0, 'Hi#1!')
                ->log(777, 'Hi 777!')
                ->log(0, 'Hi#2!')
                ->log(-256, 'Hi -256!')
                ->log(0, 'Hi#3!')
                ->log(777, 'Hi 777#2!')
                ->getLogs([-256, 42, 777]))
        );
        $this->assertSame(
            [
                $l1 = new Log(777, 'Hi 777!'),
                $l2 = new Log(-256, 'Hi -256!'),
                $l3 = new Log(777, 'Hi 777#2!'),
            ],
            (new Report())
                ->logCustom(new Log(0, 'Hi#1!'))
                ->logCustom($l1)
                ->logCustom(new Log(0, 'Hi#2!'))
                ->logCustom($l2)
                ->logCustom(new Log(0, 'Hi#3!'))
                ->logCustom($l3)
                ->getLogs([-256, 42, 777])
        );

    }

    #[Test]
    public function testIterateLogs(): void
    {

        $report = (new Report())
            ->log(-3, 'message#1@-3')
            ->log(0, 'message#1@0')
            ->log(-1, 'message#1@-1')
            ->log(2, 'message#1@2')
            ->log(0, 'message#2@0')
            ->log(-1, 'message#2@-1')
            ->log(1, 'message#1@1')
            ->log(2, 'message#2@2');

        $this->assertEquals([
            0 => 'message#1@-3',
            1 => 'message#1@0',
            2 => 'message#1@-1',
            3 => 'message#1@2',
            4 => 'message#2@0',
            5 => 'message#2@-1',
            6 => 'message#1@1',
            7 => 'message#2@2',
        ], iterator_to_array($report->iterateLogs()));

        $this->assertEquals([
            0 => 'message#1@-3',
            2 => 'message#1@-1',
            5 => 'message#2@-1',
            1 => 'message#1@0',
            4 => 'message#2@0',
            6 => 'message#1@1',
            3 => 'message#1@2',
            7 => 'message#2@2',
        ], iterator_to_array($report->iterateLogs(Report::SORT_SEVERITY_ASC)));

        $this->assertEquals([
            3 => 'message#1@2',
            7 => 'message#2@2',
            6 => 'message#1@1',
            1 => 'message#1@0',
            4 => 'message#2@0',
            2 => 'message#1@-1',
            5 => 'message#2@-1',
            0 => 'message#1@-3',
        ], iterator_to_array($report->iterateLogs(Report::SORT_SEVERITY_DESC)));

        $this->assertEquals([], iterator_to_array($report->iterateLogs(above: 10)));

        $this->assertEquals([], iterator_to_array($report->iterateLogs(below: -10)));

        $this->assertEquals([], iterator_to_array($report->iterateLogs(above: 0, below: 0)));

        $this->assertEquals([], iterator_to_array($report->iterateLogs(above: -2, below: -2)));

        $this->assertEquals([
            0 => 'message#1@-3',
            1 => 'message#1@0',
            2 => 'message#1@-1',
            3 => 'message#1@2',
            4 => 'message#2@0',
            5 => 'message#2@-1',
            6 => 'message#1@1',
            7 => 'message#2@2',
        ], iterator_to_array($report->iterateLogs(above: -10, below: 10)));

        $this->assertEquals([
            3 => 'message#1@2',
            6 => 'message#1@1',
            7 => 'message#2@2',
        ], iterator_to_array($report->iterateLogs(above: 0)));

        $this->assertEquals([
            0 => 'message#1@-3',
            2 => 'message#1@-1',
            5 => 'message#2@-1',
        ], iterator_to_array($report->iterateLogs(below: 0)));

        $this->assertEquals([
            1 => 'message#1@0',
            2 => 'message#1@-1',
            4 => 'message#2@0',
            5 => 'message#2@-1',
            6 => 'message#1@1',
        ], iterator_to_array($report->iterateLogs(above: -2, below: 2)));

        $this->assertEquals([
            0 => 'message#1@-3',
            3 => 'message#1@2',
            7 => 'message#2@2',
        ], iterator_to_array($report->iterateLogs(above: 1, below: -1)));

        $this->assertEquals([
            1 => 'message#1@0',
            4 => 'message#2@0',
            6 => 'message#1@1',
        ], iterator_to_array($report->iterateLogs(above: -1, below: 2)));

        $this->assertEquals([
            1 => 'message#1@0',
            4 => 'message#2@0',
        ], iterator_to_array($report->iterateLogs(above: 0, in_above: true, below: 0, in_below: true)));

        $this->assertEquals([], iterator_to_array($report->iterateLogs(above: -2, in_above: true, below: -2, in_below: true)));

        $this->assertEquals([
            1 => 'message#1@0',
            2 => 'message#1@-1',
            4 => 'message#2@0',
            5 => 'message#2@-1',
            6 => 'message#1@1',
        ], iterator_to_array($report->iterateLogs(above: -1, in_above: true, below: 2)));

        $this->assertEquals([
            1 => 'message#1@0',
            3 => 'message#1@2',
            4 => 'message#2@0',
            6 => 'message#1@1',
            7 => 'message#2@2',
        ], iterator_to_array($report->iterateLogs(above: -1, below: 2, in_below: true)));

        $this->assertEquals([
            1 => 'message#1@0',
            2 => 'message#1@-1',
            3 => 'message#1@2',
            4 => 'message#2@0',
            5 => 'message#2@-1',
            6 => 'message#1@1',
            7 => 'message#2@2',
        ], iterator_to_array($report->iterateLogs(above: -1, in_above: true, below: 2, in_below: true)));

        $report = (new Report(true))
            ->log(-3, 'message#1@-3')
            ->log(0, 'message#1@0')
            ->log(-1, 'message#1@-1')
            ->log(2, 'message#1@2')
            ->log(0, 'message#2@0')
            ->log(-1, 'message#2@-1')
            ->log(1, 'message#1@1')
            ->log(2, 'message#2@2');

        $this->assertEquals([
            3 => 'message#1@2',
            7 => 'message#2@2',
            6 => 'message#1@1',
            1 => 'message#1@0',
            4 => 'message#2@0',
            2 => 'message#1@-1',
            5 => 'message#2@-1',
            0 => 'message#1@-3',
        ], iterator_to_array($report->iterateLogs(Report::SORT_SEVERITY_ASC)));

        $this->assertEquals([
            0 => 'message#1@-3',
            2 => 'message#1@-1',
            5 => 'message#2@-1',
            1 => 'message#1@0',
            4 => 'message#2@0',
            6 => 'message#1@1',
            3 => 'message#1@2',
            7 => 'message#2@2',
        ], iterator_to_array($report->iterateLogs(Report::SORT_SEVERITY_DESC)));

        $this->assertEquals([
            0 => 'message#1@-3',
            2 => 'message#1@-1',
            5 => 'message#2@-1',
        ], iterator_to_array($report->iterateLogs(above: 0)));

        $this->assertEquals([
            3 => 'message#1@2',
            6 => 'message#1@1',
            7 => 'message#2@2',
        ], iterator_to_array($report->iterateLogs(below: 0)));

        $this->assertEquals([
            0 => 'message#1@-3',
            3 => 'message#1@2',
            7 => 'message#2@2',
        ], iterator_to_array($report->iterateLogs(above: -2, below: 1)));

        $this->assertEquals([
            1 => 'message#1@0',
            4 => 'message#2@0',
            6 => 'message#1@1',
        ], iterator_to_array($report->iterateLogs(above: 2, below: -1)));

        $this->assertEquals([
            1 => 'message#1@0',
            3 => 'message#1@2',
            4 => 'message#2@0',
            6 => 'message#1@1',
            7 => 'message#2@2',
        ], iterator_to_array($report->iterateLogs(above: 2, in_above: true, below: -1)));

        $this->assertEquals([
            1 => 'message#1@0',
            2 => 'message#1@-1',
            4 => 'message#2@0',
            5 => 'message#2@-1',
            6 => 'message#1@1',
        ], iterator_to_array($report->iterateLogs(above: 2, below: -1, in_below: true)));

        $this->assertEquals([
            1 => 'message#1@0',
            2 => 'message#1@-1',
            3 => 'message#1@2',
            4 => 'message#2@0',
            5 => 'message#2@-1',
            6 => 'message#1@1',
            7 => 'message#2@2',
        ], iterator_to_array($report->iterateLogs(above: 2, in_above: true, below: -1, in_below: true)));

        $this->assertEquals([
            0 => 'message#1@-3',
            2 => 'message#1@-1',
            3 => 'message#1@2',
            5 => 'message#2@-1',
            7 => 'message#2@2',
        ], iterator_to_array($report->iterateLogs(above: -1, in_above: true, below: 2, in_below: true)));

        $report = (new Report(true))
            ->logCustom($l_n3_1 = new CustomLog('message#1@-3', -3))
            ->logCustom($l_p0_1 = new CustomLog('message#1@0', 0))
            ->logCustom($l_n1_1 = new CustomLog('message#1@-1', -1))
            ->logCustom($l_p2_1 = new CustomLog('message#1@2', 2))
            ->logCustom($l_p0_2 = new CustomLog('message#2@0', 0))
            ->logCustom($l_n1_2 = new CustomLog('message#2@-1', -1))
            ->logCustom($l_p1_1 = new CustomLog('message#1@1', 1))
            ->logCustom($l_p2_2 = new CustomLog('message#2@2', 2));

        $logs = iterator_to_array($report->iterateLogs(Report::SORT_SEVERITY_DESC, above: -1, in_above: true, below: 2, in_below: true));

        $this->assertSame([
            0 => $l_n3_1,
            2 => $l_n1_1,
            5 => $l_n1_2,
            3 => $l_p2_1,
            7 => $l_p2_2,
        ], $logs);

    }

    #[Test]
    public function testInvoke(): void
    {

        $report = new Report();

        $report(3, 'message@3');
        $report(new Log(-1, 'message@-1'));
        $report(new CustomLog('message@0', 0, 'CF***x'));

        $this->assertSame(3, $report());

        $this->assertTrue($report(-1));
        $this->assertTrue($report(-9000));
        $this->assertTrue($report(2));
        $this->assertTrue($report(3));
        $this->assertFalse($report(4));

    }

    #[Test]
    public function testInvoke_reversed(): void
    {

        $report = (new Report(true))
            ->log(2, 'message@3')
            ->log(-2, 'message@-1')
            ->log(0, 'message@0');

        $this->assertTrue($report(-2));
        $this->assertFalse($report(-9000));
        $this->assertTrue($report(2));
        $this->assertFalse($report(-3));
        $this->assertTrue($report(1));

    }

    #[Test]
    public function testInvoke_customLogClass(): void
    {

        $report = new Report(true, CustomLog::class);

        $report(3, 'message@3');
        $report(new Log(-1, 'message@-1'));
        $report(new CustomLog('message@0', 0, 'CF***x'));

        /** @var CustomLog $log0 */
        $log0 = $report->getLogs(0)[0];

        $this->assertSame(-1, $report());
        $this->assertSame('CF***x', $log0->getAuthor());

    }

    #[Test]
    public function testInvoke_error(): void
    {

        $this->expectException(\InvalidArgumentException::class);

        (new Report())(new Log(0, 'message'), 'anothermessage');

    }

    #[Test]
    public function testCustomLogClass(): void
    {

        $report = new Report(false, CustomLog::class);

        $report->log(42, 'Ultimate answer!', 'Douglas ADAM', "The Hitchhiker's Guide to the Galaxy", ['year' => 1978, 'chapter' => 27]);

        /** @var CustomLog $log */
        [$log] = $report->getLogs(42);

        $this->assertInstanceOf(CustomLog::class, $log);
        $this->assertSame('Ultimate answer!', $log->message);
        $this->assertSame('Douglas ADAM', $log->getAuthor());
        $this->assertSame(['year' => 1978, 'chapter' => 27], $log->getData());

        $this->assertInstanceOf(ExtendedLog::class, current((new Report(false, ExtendedLog::class))->log(0, 'message')->getLogs(0)));

    }

    #[Test]
    public function testCustomLogClass_nonExistentClass(): void
    {

        $this->expectException(\DomainException::class);

        new Report(false, '!This\Class\Does\Not\Exists!');

    }

    #[Test]
    public function testCustomLogClass_badClass(): void
    {

        $this->expectException(\DomainException::class);

        new Report(false, \stdClass::class);

    }

    #[Test]
    public function testIterator(): void
    {

        $report = (new Report())
            ->log(0, 'Message 1')
            ->log(5, 'Message 2')
            ->log(-1, 'Message 3');

        $iterated = [];

        /** @var Log $log */
        foreach ($report as $log) {

            $iterated[$log->level] = $log->message;
        }

        $this->assertSame([
            0 => 'Message 1',
            5 => 'Message 2',
            -1 => 'Message 3',
        ], $iterated);

    }
}
