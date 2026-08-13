<?php

namespace Aarvaos\Reporting\Tests\Unit\Hooks;

use Aarvaos\Reporting\Events\AfterReportingLogEvent;
use Aarvaos\Reporting\HookableReport;
use Aarvaos\Reporting\Hooks\ReportSeverityReachHook;
use Aarvaos\Reporting\Log;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReportSeverityReachHook::class)]
class ReportSeverityReachHookTest extends TestCase
{
    #[Test]
    public function testReportSeverityReachHook(): void
    {

        /** @var bool|null $before */
        $before = null;
        /** @var bool|null $after */
        $after = null;

        $report = (new HookableReport())
        ->registerHook(new ReportSeverityReachHook(
            7,
            static function () use (&$before): void {
                $before = true;
            },
            static function () use (&$after): void {
                $after = true;
            }
        ));

        $before = $after = false;

        $report(-1, 'message@-1');
        $report(0, 'message@0');
        $report(6, 'message@6');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(7, 'message@7');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

        $before = $after = false;

        $report(0, 'message@0');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(7, 'message@7');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(8, 'message@8');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

    }

    #[Test]
    public function testReportSeverityReachHook_multiple(): void
    {

        /** @var bool|null $beforeA */
        $beforeA = null;
        /** @var bool|null $afterA */
        $afterA = null;
        /** @var bool|null $beforeB */
        $beforeB = null;
        /** @var bool|null $afterB */
        $afterB = null;

        $report = (new HookableReport())
        ->registerHook(new ReportSeverityReachHook(
            7,
            static function () use (&$beforeA): void {
                $beforeA = true;
            },
            static function () use (&$afterA): void {
                $afterA = true;
            }
        ))
        ->registerHook(new ReportSeverityReachHook(
            7,
            static function () use (&$beforeB): void {
                $beforeB = true;
            },
            static function () use (&$afterB): void {
                $afterB = true;
            }
        ))
        ;

        $beforeA = $afterA = $beforeB = $afterB = false;

        $report(-1, 'message@-1');
        $report(0, 'message@0');
        $report(6, 'message@6');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($beforeA);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($afterA);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($beforeB);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($afterB);

        $report(7, 'message@7');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($beforeA);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($afterA);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($beforeB);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($afterB);

        $beforeA = $afterA = $beforeB = $afterB = false;

        $report(6, 'message@6');
        $report(7, 'message@7');
        $report(8, 'message@8');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($beforeA);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($afterA);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($beforeB);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($afterB);

    }

    #[Test]
    public function testReportSeverityReachHook_multipleDifferentsThresholds(): void
    {

        /** @var bool|null $beforeA */
        $beforeA = null;
        /** @var bool|null $afterA */
        $afterA = null;
        /** @var bool|null $beforeB */
        $beforeB = null;
        /** @var bool|null $afterB */
        $afterB = null;

        $report = (new HookableReport())
        ->registerHook(new ReportSeverityReachHook(
            7,
            static function () use (&$beforeA): void {
                $beforeA = true;
            },
            static function () use (&$afterA): void {
                $afterA = true;
            }
        ))
        ->registerHook(new ReportSeverityReachHook(
            8,
            static function () use (&$beforeB): void {
                $beforeB = true;
            },
            static function () use (&$afterB): void {
                $afterB = true;
            }
        ));

        $beforeA = $afterA = $beforeB = $afterB = false;

        $report(-1, 'message@-1');
        $report(0, 'message@0');
        $report(6, 'message@6');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($beforeA);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($afterA);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($beforeB);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($afterB);

        $report(7, 'message@7');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($beforeA);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($afterA);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($beforeB);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($afterB);

        $beforeA = $afterA = false;

        $report(8, 'message@8');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($beforeA);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($afterA);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($beforeB);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($afterB);

        $report = (new HookableReport())
              ->registerHook(new ReportSeverityReachHook(
                  7,
                  static function () use (&$beforeA): void {
                      $beforeA = true;
                  },
                  static function () use (&$afterA): void {
                      $afterA = true;
                  }
              ))
              ->registerHook(new ReportSeverityReachHook(
                  8,
                  static function () use (&$beforeB): void {
                      $beforeB = true;
                  },
                  static function () use (&$afterB): void {
                      $afterB = true;
                  }
              ));

        $beforeA = $afterA = $beforeB = $afterB = false;

        $report(6, 'message@8');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($beforeA);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($afterA);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($beforeB);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($afterB);

        $report(8, 'message@8');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($beforeA);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($afterA);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($beforeB);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($afterB);

    }

    #[Test]
    public function testReportSeverityReachHook_excluded(): void
    {

        /** @var bool|null $before */
        $before = null;
        /** @var bool|null $after */
        $after = null;

        $report = (new HookableReport())
        ->registerHook(new ReportSeverityReachHook(
            7,
            static function () use (&$before): void {
                $before = true;
            },
            excluded: true
        ));

        $before = $after = false;

        $report(7, 'message@7');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(8, 'message@8');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

        $before = $after = false;

        $report(9, 'message@9');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

    }

    #[Test]
    public function testReportSeverityReachHook_reverse(): void
    {

        /** @var bool|null $before */
        $before = null;
        /** @var bool|null $after */
        $after = null;

        $report = (new HookableReport(true))
        ->registerHook(new ReportSeverityReachHook(
            7,
            static function () use (&$before): void {
                $before = true;
            },
            excluded: true
        ));

        $before = $after = false;

        $report(100, 'message@100');
        $report(8, 'message@8');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(7, 'message@7');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

        $report = (new HookableReport(true))
                ->registerHook(new ReportSeverityReachHook(
                    7,
                    static function () use (&$before): void {
                        $before = true;
                    },
                    excluded: true
                ));

        $before = $after = false;

        $report(9, 'message@9');

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($before);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertFalse($after);

        $report(6, 'message@6');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

    }

    #[Test]
    public function testReportSeverityReachHook_exception(): void
    {

        $error_message = 'Severity threshold reached!';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage($error_message);

        $report = (new HookableReport())
        ->registerHook(new ReportSeverityReachHook(10, after: static function (AfterReportingLogEvent $event): never {
            /** @var Log<string> $log */
            $log = $event->log;
            throw new \RuntimeException($log->getPayload(), $log->level);
        }));

        $report(new Log(10, $error_message));

    }
}
