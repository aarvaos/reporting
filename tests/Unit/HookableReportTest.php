<?php

namespace Aarvaos\Reporting\Tests\Unit;

use Aarvaos\Reporting\HookableReport;
use Aarvaos\Reporting\Hooks\AbstractCallbackHookReporting;
use Aarvaos\Reporting\Hooks\LogLevelHook;
use Aarvaos\Reporting\Log;
use Aarvaos\Reporting\Tests\Stubs\BasicHook;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(HookableReport::class)]
#[CoversClass(AbstractCallbackHookReporting::class)]
#[CoversClass(LogLevelHook::class)]
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
            function (Log $log, ?int $currentSeverity, int $nextSeverity, HookableReport $report) use (&$addedLog, &$expectedSeverityBefore, &$expectedSeverityAfter, &$hookableReport): bool {

                $this->assertSame($addedLog, $log);
                $this->assertSame($expectedSeverityBefore, $currentSeverity);
                $this->assertSame($expectedSeverityAfter, $nextSeverity);
                $this->assertSame($hookableReport, $report);

                return true;

            },
            function (Log $log, int $severityAfter, HookableReport $report) use (&$addedLog, &$expectedSeverityAfter, &$hookableReport): void {

                $this->assertSame($addedLog, $log);
                $this->assertSame($expectedSeverityAfter, $severityAfter);
                $this->assertSame($report, $hookableReport);

            },
            function (Log $log, ?int $severityBefore, HookableReport $report) use (&$addedLog, &$expectedSeverityBefore, &$hookableReport): void {

                $this->assertSame($addedLog, $log);
                $this->assertSame($expectedSeverityBefore, $severityBefore);
                $this->assertSame($report, $hookableReport);

            }
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
            function (Log $log, ?int $currentSeverity, int $nextSeverity, HookableReport $report) use (&$addedLog, &$expectedSeverityBefore, &$expectedSeverityAfter, &$hookableReport): bool {

                $this->assertSame($addedLog, $log);
                $this->assertSame($expectedSeverityBefore, $currentSeverity);
                $this->assertSame($expectedSeverityAfter, $nextSeverity);
                $this->assertSame($report, $hookableReport);

                return true;

            },
            function (Log $log, int $severityAfter, HookableReport $report) use (&$addedLog, &$expectedSeverityAfter, &$hookableReport): void {

                $this->assertSame($addedLog, $log);
                $this->assertSame($expectedSeverityAfter, $severityAfter);
                $this->assertSame($report, $hookableReport);

            },
            function (Log $log, ?int $severityBefore, HookableReport $report) use (&$addedLog, &$expectedSeverityBefore, &$hookableReport): void {

                $this->assertSame($addedLog, $log);
                $this->assertSame($expectedSeverityBefore, $severityBefore);
                $this->assertSame($report, $hookableReport);

            }
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
            static function () use (&$apply): bool {
                return $apply;
            },
            static function () use (&$before): void {
                $before = true;
            },
            static function () use (&$after): void {
                $after = true;
            }
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
    public function testLogLevelHook(): void
    {

        /** @var bool|null $before */
        $before = null;
        /** @var bool|null $after */
        $after = null;

        $report = new HookableReport();
        $report->registerHook(new LogLevelHook(
            7,
            static function () use (&$before): void {
                $before = true;
            },
            static function () use (&$after): void {
                $after = true;
            }
        ));

        $before = $after = false;

        $report(0, 'message@0');
        $report(6, 'message@6');
        $report(8, 'message@8');
        $report(-7, 'message@-7');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(7, 'message@0');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

        $before = $after = false;

        $report(6, 'message@6');
        $report(8, 'message@8');
        $report(-7, 'message@-7');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

    }
}
