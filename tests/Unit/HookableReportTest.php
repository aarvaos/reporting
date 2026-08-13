<?php

namespace Aarvaos\Reporting\Tests\Unit;

use Aarvaos\Reporting\Events\AfterReportingLogEvent;
use Aarvaos\Reporting\Events\BeforeReportingLogEvent;
use Aarvaos\Reporting\Events\EventsFactory;
use Aarvaos\Reporting\Events\HookReportingLogEvent;
use Aarvaos\Reporting\Events\ReportingLogEvent;
use Aarvaos\Reporting\HookableReport;
use Aarvaos\Reporting\Hooks\AbstractCallbackHookReporting;
use Aarvaos\Reporting\Log;
use Aarvaos\Reporting\Report;
use Aarvaos\Reporting\Tests\Stubs\BasicHook;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(HookableReport::class)]
#[CoversClass(Report::class)]
#[CoversClass(ReportingLogEvent::class)]
#[CoversClass(HookReportingLogEvent::class)]
#[CoversClass(BeforeReportingLogEvent::class)]
#[CoversClass(AfterReportingLogEvent::class)]
#[CoversClass(EventsFactory::class)]
#[CoversClass(AbstractCallbackHookReporting::class)]
class HookableReportTest extends TestCase
{
    #[Test]
    public function testHook(): void
    {

        $hookableReport = new HookableReport();
        $addedLog = new Log(1, 'message');

        /** @var int|null $expectedSeverityBefore */
        $expectedSeverityBefore = null;
        /** @var int|null $expectedSeverityAfter */
        $expectedSeverityAfter = null;

        $hookableReport->registerHook(new BasicHook(
            function (BeforeReportingLogEvent $event) use (&$addedLog, &$expectedSeverityAfter, &$hookableReport): void {

                $this->assertSame($addedLog, $event->log);
                $this->assertSame($expectedSeverityAfter, $event->finalSeverity);
                $this->assertSame($hookableReport, $event->report);

            },
            function (AfterReportingLogEvent $event) use (&$addedLog, &$expectedSeverityBefore, &$hookableReport): void {

                $this->assertSame($addedLog, $event->log);
                $this->assertSame($expectedSeverityBefore, $event->initialSeverity);
                $this->assertSame($hookableReport, $event->report);

            },
            function (ReportingLogEvent $event) use (&$addedLog, &$expectedSeverityBefore, &$expectedSeverityAfter, &$hookableReport): bool {

                $this->assertSame($addedLog, $event->log);
                $this->assertSame($expectedSeverityBefore, $event->initialSeverity);
                $this->assertSame($expectedSeverityAfter, $event->finalSeverity);
                $this->assertSame($hookableReport, $event->report);

                return true;

            },
        ));

        $expectedSeverityBefore = null;
        $expectedSeverityAfter = 1;
        $hookableReport->addLog($addedLog);

        $addedLog = new Log(3, 'message');

        $expectedSeverityBefore = 1;
        $expectedSeverityAfter = 3;
        $hookableReport->addLog($addedLog);

        $addedLog = new Log(2, 'message');

        $expectedSeverityBefore = 3;
        $expectedSeverityAfter = 3;
        $hookableReport->addLog($addedLog);

    }

    #[Test]
    public function testHook_reverse(): void
    {

        $hookableReport = new HookableReport(true);
        $addedLog = new Log(2, 'message');

        /** @var int|null $expectedSeverityBefore */
        $expectedSeverityBefore = null;
        /** @var int|null $expectedSeverityAfter */
        $expectedSeverityAfter = null;

        $hookableReport->registerHook(new BasicHook(
            function (BeforeReportingLogEvent $event) use (&$addedLog, &$expectedSeverityAfter, &$hookableReport): void {

                $this->assertSame($addedLog, $event->log);
                $this->assertSame($expectedSeverityAfter, $event->finalSeverity);
                $this->assertSame($hookableReport, $event->report);

            },
            function (AfterReportingLogEvent $event) use (&$addedLog, &$expectedSeverityBefore, &$hookableReport): void {

                $this->assertSame($addedLog, $event->log);
                $this->assertSame($expectedSeverityBefore, $event->initialSeverity);
                $this->assertSame($hookableReport, $event->report);

            },
            function (ReportingLogEvent $event) use (&$addedLog, &$expectedSeverityBefore, &$expectedSeverityAfter, &$hookableReport): bool {

                $this->assertSame($addedLog, $event->log);
                $this->assertSame($expectedSeverityBefore, $event->initialSeverity);
                $this->assertSame($expectedSeverityAfter, $event->finalSeverity);
                $this->assertSame($hookableReport, $event->report);

                return true;

            },
        ));

        $expectedSeverityBefore = null;
        $expectedSeverityAfter = 2;
        $hookableReport->addLog($addedLog);

        $addedLog = new Log(1, 'message');

        $expectedSeverityBefore = 2;
        $expectedSeverityAfter = 1;
        $hookableReport->addLog($addedLog);

        $addedLog = new Log(3, 'message');

        $expectedSeverityBefore = 1;
        $expectedSeverityAfter = 1;
        $hookableReport->addLog($addedLog);

    }

    #[Test]
    public function testShouldHook(): void
    {

        /** @var bool|null $before */
        $before = null;
        /** @var bool|null $after */
        $after = null;

        $report = new HookableReport();
        $apply = false;

        $report->registerHook(new BasicHook(
            static function () use (&$before): void {
                $before = true;
            },
            static function () use (&$after): void {
                $after = true;
            },
            static function () use (&$apply): bool {
                return $apply;
            },
        ));

        $before = $after = false;

        $report->log(1, 'message#1');

        $this->assertSame(1, $report->countLogs());
        $this->assertEquals([new Log(1, "message#1")], $report->getLogs());
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $apply = true;

        $report->log(1, 'message#2');

        $this->assertSame(2, $report->countLogs());
        $this->assertEquals([new Log(1, "message#1"), new Log(1, "message#2")], $report->getLogs());
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

    }

    #[Test]
    public function testHookCanceled(): void
    {

        $after = $before = 0;

        $report = (new HookableReport())
            ->registerHook(new BasicHook(
                static function (BeforeReportingLogEvent $event) use (&$before): void {
                    ++$before;
                    $event->cancel();
                },
                static function () use (&$after): void {
                    ++$after;
                },
            ))
            ->registerHook(new BasicHook(
                function (BeforeReportingLogEvent $event) use (&$before): void {
                    ++$before;
                    $this->assertTrue($event->isCancelled());
                },
                static function () use (&$after): void {
                    ++$after;
                },
            ));

        $report->log(0, 'message');

        $this->assertSame(0, $report->countLogs());
        $this->assertSame([], $report->getLogs());
        $this->assertSame(2, $before);
        $this->assertSame(0, $after);

    }
}
