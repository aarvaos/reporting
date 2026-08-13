<?php

namespace Aarvaos\Reporting\Tests\Unit\Hooks;

use Aarvaos\Reporting\HookableReport;
use Aarvaos\Reporting\Hooks\LogLevelHook;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LogLevelHook::class)]
class LogLevelHookTest extends TestCase
{
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

        $report(7, 'message@7');

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
