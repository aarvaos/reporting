<?php

namespace Aarvaos\Reporting\Tests\Unit;

use Aarvaos\Reporting\Log;
use Aarvaos\Reporting\Report;
use Aarvaos\Reporting\Tests\Stubs\CustomLog;
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

        $logs = [];

        $report = new Report();

        $this->assertSame(0, $report->countLogs());
        $this->assertSame([], $report->getLogs());
        $this->assertSame($logs, $report->getLogs());

        $logs[] = $report->log(0, 'Hi!');

        $this->assertSame(1, $report->countLogs());
        $this->assertEquals([new Log(0, 'Hi!')], $report->getLogs());

        $logs[] = $report->log(-1, 'Hello!');

        $this->assertSame(2, $report->countLogs());
        $this->assertEquals([new Log(0, 'Hi!'), new Log(-1, 'Hello!')], $report->getLogs());

        $logs[] = $report->log(46, new \DateTime('2019-06-28'));

        $this->assertSame(3, $report->countLogs());
        $this->assertEquals([new Log(0, 'Hi!'), new Log(-1, 'Hello!'), new Log(46, new \DateTime('2019-06-28'))], $report->getLogs());

        $logs[] = new Log(-11, 'Bonjour');
        $report->addLog(end($logs));
        $logs[] = new CustomLog('', 0, 'CF***x', 'IDE', ['version' => '0.9-alpha', 'id' => .9]);
        $report->addLog(end($logs));

        $this->assertSame(5, $report->countLogs());
        $this->assertEquals([new Log(0, 'Hi!'), new Log(-1, 'Hello!'), new Log(46, new \DateTime('2019-06-28')), new Log(-11, 'Bonjour'), new CustomLog('', 0, 'CF***x', 'IDE', ['version' => '0.9-alpha', 'id' => .9])], $report->getLogs());

        $this->assertSame($logs, $report->getLogs());

    }

    #[Test]
    public function testCount(): void
    {

        $this->assertSame(0, count(new Report()));
        $this->assertSame(3, count(
            (new Report())
                ->addLog(new Log(0, 'Hi!'))
                ->addLog(new Log(-256, 'Hi -256!'))
                ->addLog(new Log(777, 'Hi 777!'))
        ));
        $this->assertSame(2, count(
            (new Report())
                ->addLog(new Log(0, 'Hi!'))
                ->addLog(new Log(0, 'Hi!'))
        ));

    }

    #[Test]
    public function testGetMaxLevel(): void
    {

        $this->assertNull((new Report())->getMaxLevel());
        $this->assertSame(0, (new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->getMaxLevel());
        $this->assertSame(777, (new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->getMaxLevel());
        $this->assertSame(-256, (new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->getMaxLevel());

        $this->assertSame(0, (new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->getMaxLevel());
        $this->assertSame(0, (new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(0, 'Hi!'))
            ->getMaxLevel());

        $this->assertSame(777, (new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->getMaxLevel());
        $this->assertSame(777, (new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->addLog(new Log(0, 'Hi!'))
            ->getMaxLevel());

        $this->assertSame(777, (new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->getMaxLevel());
        $this->assertSame(777, (new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->getMaxLevel());

    }

    #[Test]
    public function testGetMinLevel(): void
    {

        $this->assertNull((new Report())->getMinLevel());
        $this->assertSame(0, (new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->getMinLevel());
        $this->assertSame(777, (new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->getMinLevel());
        $this->assertSame(-256, (new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->getMinLevel());

        $this->assertSame(-256, (new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->getMinLevel());
        $this->assertSame(-256, (new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(0, 'Hi!'))
            ->getMinLevel());

        $this->assertSame(0, (new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->getMinLevel());
        $this->assertSame(0, (new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->addLog(new Log(0, 'Hi!'))
            ->getMinLevel());

        $this->assertSame(-256, (new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->getMinLevel());
        $this->assertSame(-256, (new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->getMinLevel());

    }

    #[Test]
    public function testGetSeverity(): void
    {

        $this->assertNull((new Report())->getSeverity());

        $this->assertSame(0, (new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->getSeverity());
        $this->assertSame(777, (new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->getSeverity());
        $this->assertSame(-256, (new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->getSeverity());

        $this->assertSame(777, (new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->getSeverity());

        $this->assertSame(-256, (new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
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
            ->addLog(new Log(0, 'Hi!'))
            ->hasSeverityReached(0));
        $this->assertFalse((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->hasSeverityReached(0, true));
        $this->assertTrue((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->hasSeverityReached(-1));
        $this->assertTrue((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->hasSeverityReached(-1, true));
        $this->assertFalse((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->hasSeverityReached(1));
        $this->assertFalse((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->hasSeverityReached(1, true));

        $this->assertTrue((new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(0));
        $this->assertTrue((new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(0, true));
        $this->assertTrue((new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(-256));
        $this->assertTrue((new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(-256, true));
        $this->assertTrue((new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(777));
        $this->assertFalse((new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(777, true));
        $this->assertTrue((new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(776));
        $this->assertTrue((new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(776, true));
        $this->assertFalse((new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(778));
        $this->assertFalse((new Report())
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(778, true));

        $this->assertFalse((new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(0));
        $this->assertFalse((new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(0, true));
        $this->assertFalse((new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(777));
        $this->assertFalse((new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(777, true));
        $this->assertTrue((new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(-256));
        $this->assertFalse((new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(-256, true));
        $this->assertTrue((new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(-257));
        $this->assertTrue((new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(-257, true));
        $this->assertFalse((new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(-255));
        $this->assertFalse((new Report())
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(-255, true));

        $this->assertTrue((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(0));
        $this->assertTrue((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(0, true));
        $this->assertTrue((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(-256));
        $this->assertTrue((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(-256, true));
        $this->assertTrue((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(-255));
        $this->assertTrue((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(-255, true));
        $this->assertTrue((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(-257));
        $this->assertTrue((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(-257, true));
        $this->assertTrue((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(777));
        $this->assertFalse((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(777, true));
        $this->assertTrue((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(776));
        $this->assertTrue((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(776, true));
        $this->assertFalse((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(778));
        $this->assertFalse((new Report())
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
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
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(777));
        $this->assertFalse((new Report(true))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(777, true));
        $this->assertFalse((new Report(true))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(776));
        $this->assertFalse((new Report(true))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(776, true));
        $this->assertTrue((new Report(true))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(778));
        $this->assertTrue((new Report(true))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(778, true));

        $this->assertTrue((new Report(true))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(-256));
        $this->assertFalse((new Report(true))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(-256, true));
        $this->assertFalse((new Report(true))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(-257));
        $this->assertFalse((new Report(true))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(-257, true));
        $this->assertTrue((new Report(true))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(-255));
        $this->assertTrue((new Report(true))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->hasSeverityReached(-255, true));

        $this->assertTrue((new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(0));
        $this->assertTrue((new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(0, true));
        $this->assertTrue((new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(777));
        $this->assertTrue((new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(777, true));
        $this->assertTrue((new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(-256));
        $this->assertFalse((new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(-256, true));
        $this->assertTrue((new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(778));
        $this->assertTrue((new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(778, true));
        $this->assertTrue((new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(776));
        $this->assertTrue((new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(776, true));
        $this->assertTrue((new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(-255, true));
        $this->assertTrue((new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(-255, true));
        $this->assertFalse((new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(-257, true));
        $this->assertFalse((new Report(true))
            ->addLog(new Log(0, 'Hi!'))
            ->addLog(new Log(-256, 'Hi -256!'))
            ->addLog(new Log(777, 'Hi 777!'))
            ->hasSeverityReached(-257, true));

    }

    #[Test]
    public function testGetLogs(): void
    {

        $this->assertSame([], (new Report())->getLogs());
        $this->assertEquals(
            [new Log(0, 'Hi!')],
            (new Report())
                ->addLog(new Log(0, 'Hi!'))
                ->getLogs()
        );
        $this->assertEquals(
            [new Log(0, 'Hi!'), new Log(-256, 'Hi -256!'), new Log(777, 'Hi 777!')],
            (new Report())
                ->addLog(new Log(0, 'Hi!'))
                ->addLog(new Log(-256, 'Hi -256!'))
                ->addLog(new Log(777, 'Hi 777!'))
                ->getLogs()
        );
        $this->assertEquals(
            [new Log(0, 'Hi#1!'), new Log(-256, 'Hi -256!'), new Log(0, 'Hi#2!'), new Log(0, 'Hi#3!'), new Log(777, 'Hi 777!')],
            (new Report())
                ->addLog(new Log(0, 'Hi#1!'))
                ->addLog(new Log(-256, 'Hi -256!'))
                ->addLog(new Log(0, 'Hi#2!'))
                ->addLog(new Log(0, 'Hi#3!'))
                ->addLog(new Log(777, 'Hi 777!'))
                ->getLogs()
        );

        $report = new Report();

        $logs[] = $report->log(0, 'Hi#1!');
        $logs[] = $report->log(-256, 'Hi -256!');
        $logs[] = $report->log(0, 'Hi#2!');
        $logs[] = $report->log(0, 'Hi#3!');
        $logs[] = $report->log(777, 'Hi 777!');

        $this->assertSame($logs, $report->getLogs());

    }

    #[Test]
    public function testGetLogs_level(): void
    {

        $this->assertSame([], (new Report())->getLogs(1));
        $this->assertSame(
            [],
            (new Report())
                ->addLog(new Log(0, 'Hi!'))
                ->getLogs(1)
        );
        $this->assertEquals(
            [new Log(0, 'Hi!')],
            (new Report())
                ->addLog(new Log(0, 'Hi!'))
                ->getLogs(0)
        );
        $this->assertEquals(
            [new Log(-256, 'Hi -256!')],
            (new Report())
                ->addLog(new Log(0, 'Hi!'))
                ->addLog(new Log(-256, 'Hi -256!'))
                ->addLog(new Log(777, 'Hi 777!'))
                ->getLogs(-256)
        );
        $this->assertEquals(
            [new Log(0, 'Hi#1!'), new Log(0, 'Hi#2!'), new Log(0, 'Hi#3!')],
            (new Report())
                ->addLog(new Log(0, 'Hi#1!'))
                ->addLog(new Log(-256, 'Hi -256!'))
                ->addLog(new Log(0, 'Hi#2!'))
                ->addLog(new Log(0, 'Hi#3!'))
                ->addLog(new Log(777, 'Hi 777!'))
                ->getLogs(0)
        );
        $this->assertSame(
            [
                $l1 = new Log(0, 'Hi#1!'),
                $l2 = new Log(0, 'Hi#2!'),
                $l3 = new Log(0, 'Hi#3!'),
            ],
            (new Report())
                ->addLog($l1)
                ->addLog(new Log(-256, 'Hi -256!'))
                ->addLog($l2)
                ->addLog($l3)
                ->addLog(new Log(777, 'Hi 777!'))
                ->getLogs(0)
        );

        $this->assertEquals(
            [],
            (new Report())
                ->addLog(new Log(0, 'Hi#1!'))
                ->addLog(new Log(777, 'Hi 777!'))
                ->addLog(new Log(0, 'Hi#2!'))
                ->addLog(new Log(-256, 'Hi -256!'))
                ->addLog(new Log(0, 'Hi#3!'))
                ->addLog(new Log(777, 'Hi 777#2!'))
                ->getLogs([])
        );
        $this->assertEquals(
            [new Log(0, 'Hi#1!'), new Log(0, 'Hi#2!'), new Log(0, 'Hi#3!')],
            (new Report())
                ->addLog(new Log(0, 'Hi#1!'))
                ->addLog(new Log(777, 'Hi 777!'))
                ->addLog(new Log(0, 'Hi#2!'))
                ->addLog(new Log(-256, 'Hi -256!'))
                ->addLog(new Log(0, 'Hi#3!'))
                ->addLog(new Log(777, 'Hi 777#2!'))
                ->getLogs([0])
        );
        $this->assertEquals(
            [],
            (new Report())
                ->addLog(new Log(0, 'Hi#1!'))
                ->addLog(new Log(-256, 'Hi -256!'))
                ->addLog(new Log(0, 'Hi#2!'))
                ->addLog(new Log(0, 'Hi#3!'))
                ->addLog(new Log(777, 'Hi 777!'))
                ->getLogs([1, 2, 3])
        );
        $this->assertEquals(
            [new Log(0, 'Hi#1!'), new Log(0, 'Hi#2!'), new Log(0, 'Hi#3!')],
            (new Report())
                ->addLog(new Log(0, 'Hi#1!'))
                ->addLog(new Log(777, 'Hi 777!'))
                ->addLog(new Log(0, 'Hi#2!'))
                ->addLog(new Log(-256, 'Hi -256!'))
                ->addLog(new Log(0, 'Hi#3!'))
                ->addLog(new Log(777, 'Hi 777#2!'))
                ->getLogs([-1, 0, 1])
        );
        $this->assertEquals(
            [new Log(777, 'Hi 777!'), new Log(-256, 'Hi -256!'), new Log(777, 'Hi 777#2!')],
            (new Report())
                ->addLog(new Log(0, 'Hi#1!'))
                ->addLog(new Log(777, 'Hi 777!'))
                ->addLog(new Log(0, 'Hi#2!'))
                ->addLog(new Log(-256, 'Hi -256!'))
                ->addLog(new Log(0, 'Hi#3!'))
                ->addLog(new Log(777, 'Hi 777#2!'))
                ->getLogs([-256, 42, 777])
        );
        $this->assertSame(
            [
                $l1 = new Log(777, 'Hi 777!'),
                $l2 = new Log(-256, 'Hi -256!'),
                $l3 = new Log(777, 'Hi 777#2!'),
            ],
            (new Report())
                ->addLog(new Log(0, 'Hi#1!'))
                ->addLog($l1)
                ->addLog(new Log(0, 'Hi#2!'))
                ->addLog($l2)
                ->addLog(new Log(0, 'Hi#3!'))
                ->addLog($l3)
                ->getLogs([-256, 42, 777])
        );

        $report = new Report();

        $logs[1] = $report->log(0, 'Hi#1!');
        $logs[-256] = $report->log(-256, 'Hi -256!');
        $logs[2] = $report->log(0, 'Hi#2!');
        $logs[3] = $report->log(0, 'Hi#3!');
        $logs[777] = $report->log(777, 'Hi 777!');

        $this->assertSame([$logs[1], $logs[2], $logs[3]], $report->getLogs(0));
        $this->assertSame([$logs[-256], $logs[777]], $report->getLogs([777, 42, -256]));

    }

    #[Test]
    public function testIterateLogs(): void
    {

        $report = (new Report())
            ->addLog(new Log(-3, 'message#1@-3'))
            ->addLog(new Log(0, 'message#1@0'))
            ->addLog(new Log(-1, 'message#1@-1'))
            ->addLog(new Log(2, 'message#1@2'))
            ->addLog(new Log(0, 'message#2@0'))
            ->addLog(new Log(-1, 'message#2@-1'))
            ->addLog(new Log(1, 'message#1@1'))
            ->addLog(new Log(2, 'message#2@2'));

        $this->assertEquals([
            0 => new Log(-3, 'message#1@-3'),
            1 => new Log(0, 'message#1@0'),
            2 => new Log(-1, 'message#1@-1'),
            3 => new Log(2, 'message#1@2'),
            4 => new Log(0, 'message#2@0'),
            5 => new Log(-1, 'message#2@-1'),
            6 => new Log(1, 'message#1@1'),
            7 => new Log(2, 'message#2@2'),
        ], iterator_to_array($report->iterateLogs()));

        $this->assertEquals([
            0 => new Log(-3, 'message#1@-3'),
            2 => new Log(-1, 'message#1@-1'),
            5 => new Log(-1, 'message#2@-1'),
            1 => new Log(0, 'message#1@0'),
            4 => new Log(0, 'message#2@0'),
            6 => new Log(1, 'message#1@1'),
            3 => new Log(2, 'message#1@2'),
            7 => new Log(2, 'message#2@2'),
        ], iterator_to_array($report->iterateLogs(Report::SORT_SEVERITY_ASC)));

        $this->assertEquals([
            3 => new Log(2, 'message#1@2'),
            7 => new Log(2, 'message#2@2'),
            6 => new Log(1, 'message#1@1'),
            1 => new Log(0, 'message#1@0'),
            4 => new Log(0, 'message#2@0'),
            2 => new Log(-1, 'message#1@-1'),
            5 => new Log(-1, 'message#2@-1'),
            0 => new Log(-3, 'message#1@-3'),
        ], iterator_to_array($report->iterateLogs(Report::SORT_SEVERITY_DESC)));

        $this->assertEquals([], iterator_to_array($report->iterateLogs(above: 10)));

        $this->assertEquals([], iterator_to_array($report->iterateLogs(below: -10)));

        $this->assertEquals([], iterator_to_array($report->iterateLogs(above: 0, below: 0)));

        $this->assertEquals([], iterator_to_array($report->iterateLogs(above: -2, below: -2)));

        $this->assertEquals([
            0 => new Log(-3, 'message#1@-3'),
            1 => new Log(0, 'message#1@0'),
            2 => new Log(-1, 'message#1@-1'),
            3 => new Log(2, 'message#1@2'),
            4 => new Log(0, 'message#2@0'),
            5 => new Log(-1, 'message#2@-1'),
            6 => new Log(1, 'message#1@1'),
            7 => new Log(2, 'message#2@2'),
        ], iterator_to_array($report->iterateLogs(above: -10, below: 10)));

        $this->assertEquals([
            3 => new Log(2, 'message#1@2'),
            6 => new Log(1, 'message#1@1'),
            7 => new Log(2, 'message#2@2'),
        ], iterator_to_array($report->iterateLogs(above: 0)));

        $this->assertEquals([
            0 => new Log(-3, 'message#1@-3'),
            2 => new Log(-1, 'message#1@-1'),
            5 => new Log(-1, 'message#2@-1'),
        ], iterator_to_array($report->iterateLogs(below: 0)));

        $this->assertEquals([
            1 => new Log(0, 'message#1@0'),
            2 => new Log(-1, 'message#1@-1'),
            4 => new Log(0, 'message#2@0'),
            5 => new Log(-1, 'message#2@-1'),
            6 => new Log(1, 'message#1@1'),
        ], iterator_to_array($report->iterateLogs(above: -2, below: 2)));

        $this->assertEquals([
            0 => new Log(-3, 'message#1@-3'),
            3 => new Log(2, 'message#1@2'),
            7 => new Log(2, 'message#2@2'),
        ], iterator_to_array($report->iterateLogs(above: 1, below: -1)));

        $this->assertEquals([
            1 => new Log(0, 'message#1@0'),
            4 => new Log(0, 'message#2@0'),
            6 => new Log(1, 'message#1@1'),
        ], iterator_to_array($report->iterateLogs(above: -1, below: 2)));

        $this->assertEquals([
            1 => new Log(0, 'message#1@0'),
            4 => new Log(0, 'message#2@0'),
        ], iterator_to_array($report->iterateLogs(above: 0, in_above: true, below: 0, in_below: true)));

        $this->assertEquals([], iterator_to_array($report->iterateLogs(above: -2, in_above: true, below: -2, in_below: true)));

        $this->assertEquals([
            1 => new Log(0, 'message#1@0'),
            2 => new Log(-1, 'message#1@-1'),
            4 => new Log(0, 'message#2@0'),
            5 => new Log(-1, 'message#2@-1'),
            6 => new Log(1, 'message#1@1'),
        ], iterator_to_array($report->iterateLogs(above: -1, in_above: true, below: 2)));

        $this->assertEquals([
            1 => new Log(0, 'message#1@0'),
            3 => new Log(2, 'message#1@2'),
            4 => new Log(0, 'message#2@0'),
            6 => new Log(1, 'message#1@1'),
            7 => new Log(2, 'message#2@2'),
        ], iterator_to_array($report->iterateLogs(above: -1, below: 2, in_below: true)));

        $this->assertEquals([
            1 => new Log(0, 'message#1@0'),
            2 => new Log(-1, 'message#1@-1'),
            3 => new Log(2, 'message#1@2'),
            4 => new Log(0, 'message#2@0'),
            5 => new Log(-1, 'message#2@-1'),
            6 => new Log(1, 'message#1@1'),
            7 => new Log(2, 'message#2@2'),
        ], iterator_to_array($report->iterateLogs(above: -1, in_above: true, below: 2, in_below: true)));

        $report = (new Report(true))
            ->addLog(new Log(-3, 'message#1@-3'))
            ->addLog(new Log(0, 'message#1@0'))
            ->addLog(new Log(-1, 'message#1@-1'))
            ->addLog(new Log(2, 'message#1@2'))
            ->addLog(new Log(0, 'message#2@0'))
            ->addLog(new Log(-1, 'message#2@-1'))
            ->addLog(new Log(1, 'message#1@1'))
            ->addLog(new Log(2, 'message#2@2'));

        $this->assertEquals([
            3 => new Log(2, 'message#1@2'),
            7 => new Log(2, 'message#2@2'),
            6 => new Log(1, 'message#1@1'),
            1 => new Log(0, 'message#1@0'),
            4 => new Log(0, 'message#2@0'),
            2 => new Log(-1, 'message#1@-1'),
            5 => new Log(-1, 'message#2@-1'),
            0 => new Log(-3, 'message#1@-3'),
        ], iterator_to_array($report->iterateLogs(Report::SORT_SEVERITY_ASC)));

        $this->assertEquals([
            0 => new Log(-3, 'message#1@-3'),
            2 => new Log(-1, 'message#1@-1'),
            5 => new Log(-1, 'message#2@-1'),
            1 => new Log(0, 'message#1@0'),
            4 => new Log(0, 'message#2@0'),
            6 => new Log(1, 'message#1@1'),
            3 => new Log(2, 'message#1@2'),
            7 => new Log(2, 'message#2@2'),
        ], iterator_to_array($report->iterateLogs(Report::SORT_SEVERITY_DESC)));

        $this->assertEquals([
            0 => new Log(-3, 'message#1@-3'),
            2 => new Log(-1, 'message#1@-1'),
            5 => new Log(-1, 'message#2@-1'),
        ], iterator_to_array($report->iterateLogs(above: 0)));

        $this->assertEquals([
            3 => new Log(2, 'message#1@2'),
            6 => new Log(1, 'message#1@1'),
            7 => new Log(2, 'message#2@2'),
        ], iterator_to_array($report->iterateLogs(below: 0)));

        $this->assertEquals([
            0 => new Log(-3, 'message#1@-3'),
            3 => new Log(2, 'message#1@2'),
            7 => new Log(2, 'message#2@2'),
        ], iterator_to_array($report->iterateLogs(above: -2, below: 1)));

        $this->assertEquals([
            1 => new Log(0, 'message#1@0'),
            4 => new Log(0, 'message#2@0'),
            6 => new Log(1, 'message#1@1'),
        ], iterator_to_array($report->iterateLogs(above: 2, below: -1)));

        $this->assertEquals([
            1 => new Log(0, 'message#1@0'),
            3 => new Log(2, 'message#1@2'),
            4 => new Log(0, 'message#2@0'),
            6 => new Log(1, 'message#1@1'),
            7 => new Log(2, 'message#2@2'),
        ], iterator_to_array($report->iterateLogs(above: 2, in_above: true, below: -1)));

        $this->assertEquals([
            1 => new Log(0, 'message#1@0'),
            2 => new Log(-1, 'message#1@-1'),
            4 => new Log(0, 'message#2@0'),
            5 => new Log(-1, 'message#2@-1'),
            6 => new Log(1, 'message#1@1'),
        ], iterator_to_array($report->iterateLogs(above: 2, below: -1, in_below: true)));

        $this->assertEquals([
            1 => new Log(0, 'message#1@0'),
            2 => new Log(-1, 'message#1@-1'),
            3 => new Log(2, 'message#1@2'),
            4 => new Log(0, 'message#2@0'),
            5 => new Log(-1, 'message#2@-1'),
            6 => new Log(1, 'message#1@1'),
            7 => new Log(2, 'message#2@2'),
        ], iterator_to_array($report->iterateLogs(above: 2, in_above: true, below: -1, in_below: true)));

        $this->assertEquals([
            0 => new Log(-3, 'message#1@-3'),
            2 => new Log(-1, 'message#1@-1'),
            3 => new Log(2, 'message#1@2'),
            5 => new Log(-1, 'message#2@-1'),
            7 => new Log(2, 'message#2@2'),
        ], iterator_to_array($report->iterateLogs(above: -1, in_above: true, below: 2, in_below: true)));

        $report = (new Report(true))
            ->addLog($l_n3_1 = new CustomLog('message#1@-3', -3))
            ->addLog($l_p0_1 = new CustomLog('message#1@0', 0))
            ->addLog($l_n1_1 = new CustomLog('message#1@-1', -1))
            ->addLog($l_p2_1 = new CustomLog('message#1@2', 2))
            ->addLog($l_p0_2 = new CustomLog('message#2@0', 0))
            ->addLog($l_n1_2 = new CustomLog('message#2@-1', -1))
            ->addLog($l_p1_1 = new CustomLog('message#1@1', 1))
            ->addLog($l_p2_2 = new CustomLog('message#2@2', 2));

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
            ->addLog(new Log(2, 'message@3'))
            ->addLog(new Log(-2, 'message@-1'))
            ->addLog(new Log(0, 'message@0'));

        $this->assertTrue($report(-2));
        $this->assertFalse($report(-9000));
        $this->assertTrue($report(2));
        $this->assertFalse($report(-3));
        $this->assertTrue($report(1));

    }

    #[Test]
    public function testInvoke_customLogClass(): void
    {

        $report = new Report(true);

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
    public function testIterator(): void
    {

        $logs = [];

        $report = new Report();
        $logs[1] = $report->log(0, 'Message 1');
        $logs[2] = new Log(5, 'Message 2');
        $report->addLog($logs[2]);
        $logs[3] = new Log(-1, 'Message 3');
        $report->addLog($logs[3]);

        $iterated = [];

        foreach ($report as $log) {

            $iterated[$log->level] = [$log, $log->getPayload()];
        }

        $this->assertSame([
            0 => [$logs[1], 'Message 1'],
            5 => [$logs[2], 'Message 2'],
            -1 => [$logs[3], 'Message 3'],
        ], $iterated);

    }
}
