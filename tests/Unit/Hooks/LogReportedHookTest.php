<?php

namespace Aarvaos\Reporting\Tests\Unit\Hooks;

use Aarvaos\Reporting\HookableReport;
use Aarvaos\Reporting\Hooks\LogReportedHook;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LogReportedHook::class)]
class LogReportedHookTest extends TestCase
{
    #[Test]
    public function testLogReportedHook(): void
    {

        /** @var bool|null $before */
        $before = null;
        /** @var bool|null $after */
        $after = null;

        $report = new HookableReport();
        $report->registerHook(new LogReportedHook(
            static function () use (&$before): void {
                $before = true;
            },
            static function () use (&$after): void {
                $after = true;
            }
        ));

        $before = $after = false;

        $report(0, 'message@0');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

        $before = $after = false;

        $report(-1, 'message@-1');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);

        $before = $after = false;

        $report(10, 'message@10');

        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($before);
        /** @phpstan-ignore method.impossibleType */
        $this->assertTrue($after);
    }
}
