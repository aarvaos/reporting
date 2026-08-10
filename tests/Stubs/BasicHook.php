<?php

namespace Aarvaos\Reporting\Tests\Stubs;

use Aarvaos\Reporting\HookableReport;
use Aarvaos\Reporting\Hooks\AbstractCallbackHookReporting;
use Aarvaos\Reporting\Log;

class BasicHook extends AbstractCallbackHookReporting
{
    /** @param \Closure(Log<mixed>, ?int, int, HookableReport): bool $apply */
    public function __construct(
        public \Closure $apply,
        ?\Closure $before = null,
        ?\Closure  $after = null,
    ) {

        parent::__construct($before, $after);

    }

    public function shouldHook(Log $log, ?int $currentSeverity, int $nextSeverity, HookableReport $report): bool
    {

        return ($this->apply)($log, $currentSeverity, $nextSeverity, $report);

    }
}
