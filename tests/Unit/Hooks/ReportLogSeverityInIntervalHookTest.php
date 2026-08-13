<?php

namespace Aarvaos\Reporting\Tests\Unit\Hooks;

use Aarvaos\Reporting\Events\AfterReportingLogEvent;
use Aarvaos\Reporting\Events\BeforeReportingLogEvent;
use Aarvaos\Reporting\HookableReport;
use Aarvaos\Reporting\Hooks\ReportLogSeverityInIntervalHook;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReportLogSeverityInIntervalHook::class)]
class ReportLogSeverityInIntervalHookTest extends TestCase
{
    #[Test]
    public function testHookSeverity_over(): void
    {

        /** @var bool|null $before */
        $before = null;
        /** @var bool|null $after */
        $after = null;

        $report = new HookableReport();
        $report->registerHook(new ReportLogSeverityInIntervalHook(
            5,
            before: function (BeforeReportingLogEvent $event) use (&$before): void {
                $this->assertGreaterThan(5, $event->log->level);
                $before = true;
            },
            after: function (AfterReportingLogEvent $event) use (&$after): void {
                $this->assertGreaterThan(5, $event->log->level);
                $after = true;
            },
        ));

        $before = $after = false;

        $report(0, 'message@0');
        $report(-5, 'message@-5');
        $report(-10, 'message@-10');
        $report(5, 'message@5');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(6, 'message@6');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

        $before = $after = false;

        $report(5, 'message@5');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(6, 'message@6');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

        $before = $after = false;

        $report(7, 'message@7');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

    }

    #[Test]
    public function testHookSeverity_overIncluded(): void
    {

        /** @var bool|null $before */
        $before = null;
        /** @var bool|null $after */
        $after = null;

        $report = new HookableReport();
        $report->registerHook(new ReportLogSeverityInIntervalHook(
            5,
            true,
            before: function (BeforeReportingLogEvent $event) use (&$before): void {
                $this->assertGreaterThanOrEqual(5, $event->log->level);
                $before = true;
            },
            after: function (AfterReportingLogEvent $event) use (&$after): void {
                $this->assertGreaterThanOrEqual(5, $event->log->level);
                $after = true;
            },
        ));

        $before = $after = false;

        $report(0, 'message@0');
        $report(-5, 'message@-5');
        $report(-10, 'message@-10');
        $report(4, 'message@4');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(5, 'message@5');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

        $before = $after = false;

        $report(4, 'message@4');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(5, 'message@5');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

        $before = $after = false;

        $report(6, 'message@6');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

    }

    #[Test]
    public function testHookSeverity_under(): void
    {

        /** @var bool|null $before */
        $before = null;
        /** @var bool|null $after */
        $after = null;

        $report = new HookableReport();
        $report->registerHook(new ReportLogSeverityInIntervalHook(
            under: 5,
            before: function (BeforeReportingLogEvent $event) use (&$before): void {
                $this->assertLessThan(5, $event->log->level);
                $before = true;
            },
            after: function (AfterReportingLogEvent $event) use (&$after): void {
                $this->assertLessThan(5, $event->log->level);
                $after = true;
            },
        ));

        $before = $after = false;

        $report(100, 'message@100');
        $report(5, 'message@5');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(4, 'message@4');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

        $before = $after = false;

        $report(5, 'message@5');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(4, 'message@4');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

        $before = $after = false;

        $report(0, 'message@0');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

        $before = $after = false;

        $report(-5, 'message@5');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

    }

    #[Test]
    public function testHookSeverity_underIncluded(): void
    {

        /** @var bool|null $before */
        $before = null;
        /** @var bool|null $after */
        $after = null;

        $report = new HookableReport();
        $report->registerHook(new ReportLogSeverityInIntervalHook(
            under: 5,
            in_under: true,
            before: function (BeforeReportingLogEvent $event) use (&$before): void {
                $this->assertLessThanOrEqual(5, $event->log->level);
                $before = true;
            },
            after: function (AfterReportingLogEvent $event) use (&$after): void {
                $this->assertLessThanOrEqual(5, $event->log->level);
                $after = true;
            },
        ));

        $before = $after = false;

        $report(100, 'message@100');
        $report(6, 'message@6');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(5, 'message@5');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

        $before = $after = false;

        $report(6, 'message@6');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(5, 'message@5');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

    }

    #[Test]
    public function testHookSeverity_within(): void
    {

        /** @var bool|null $applied */
        $applied = null;

        $report = new HookableReport();
        $report->registerHook(new ReportLogSeverityInIntervalHook(
            over: 2,
            under: 6,
            before: function (BeforeReportingLogEvent $event) use (&$applied): void {
                $this->assertGreaterThan(2, $event->log->level);
                $this->assertLessThan(6, $event->log->level);
                $applied = true;
            },
        ));

        $applied = false;

        $report(0, 'message@0');
        $report(2, 'message@2');
        $report(6, 'message@6');
        $report(100, 'message@100');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($applied);

        $report(3, 'message@3');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($applied);

        $applied = false;

        $report(5, 'message@5');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($applied);

        $applied = false;

        $report(4, 'message@4');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($applied);

    }

    #[Test]
    public function testHookSeverity_withinIncluded(): void
    {

        /** @var bool|null $applied */
        $applied = null;

        $report = new HookableReport();
        $report->registerHook(new ReportLogSeverityInIntervalHook(
            over: -6,
            in_over: true,
            under: -2,
            in_under: true,
            after: function (AfterReportingLogEvent $event) use (&$applied): void {
                $this->assertGreaterThanOrEqual(-6, $event->log->level);
                $this->assertLessThanOrEqual(-2, $event->log->level);
                $applied = true;
            },
        ));

        $applied = false;

        $report(-10, 'message@-10');
        $report(-7, 'message@-7');
        $report(-1, 'message@-1');
        $report(0, 'message@0');
        $report(1, 'message@1');
        $report(7, 'message@7');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($applied);

        $report(-6, 'message@-6');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($applied);

        $applied = false;

        $report(-2, 'message@-2');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($applied);

    }

    #[Test]
    public function testHookSeverity_disjoint(): void
    {

        /** @var bool|null $applied */
        $applied = null;

        $report = new HookableReport();
        $report->registerHook(new ReportLogSeverityInIntervalHook(
            over: 3,
            under: -2,
            in_under: true,
            after: function () use (&$applied): void {
                $applied = true;
            },
        ));

        $applied = false;

        $report(-1, 'message@-1');
        $report(0, 'message@0');
        $report(3, 'message@3');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($applied);

        $report(4, 'message@4');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($applied);

        $applied = false;

        $report(-2, 'message@-2');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($applied);

    }

    #[Test]
    public function testHookSeverity_reverse(): void
    {

        /** @var bool|null $applied */
        $applied = null;

        $report = new HookableReport(true);
        $report->registerHook(new ReportLogSeverityInIntervalHook(
            over: 6,
            in_over: true,
            after: function () use (&$applied): void {
                $applied = true;
            },
        ));

        $applied = false;

        $report(7, 'message@7');
        $report(8, 'message@8');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($applied);

        $report(6, 'message@6');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($applied);

        $applied = false;

        $report(5, 'message@5');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($applied);

        $report = new HookableReport(true);
        $report->registerHook(new ReportLogSeverityInIntervalHook(
            under: -4,
            after: function () use (&$applied): void {
                $applied = true;
            },
        ));

        $applied = false;

        $report(-4, 'message@-4');
        $report(-5, 'message@-5');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($applied);

        $report(-3, 'message@-3');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($applied);

        $applied = false;

        $report(4, 'message@4');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($applied);

        $report = new HookableReport(true);
        $report->registerHook(new ReportLogSeverityInIntervalHook(
            over: 6,
            in_over: true,
            under: -4,
            after: function () use (&$applied): void {
                $applied = true;
            },
        ));

        $applied = false;

        $report(7, 'message@7');
        $report(-4, 'message@-4');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($applied);

        $report(0, 'message@0');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($applied);

        $report = new HookableReport(true);
        $report->registerHook(new ReportLogSeverityInIntervalHook(
            over: -6,
            under: 4,
            in_under: true,
            after: function () use (&$applied): void {
                $applied = true;
            },
        ));

        $applied = false;

        $report(0, 'message@0');
        $report(3, 'message@3');
        $report(-6, 'message@-6');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($applied);

        $report(-7, 'message@-7');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($applied);

        $applied = false;

        $report(4, 'message@4');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($applied);

    }
}
