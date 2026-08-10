<?php

namespace Aarvaos\Reporting\Hooks;

use Aarvaos\Reporting\HookableReport;
use Aarvaos\Reporting\Log;

/**
 * Hook all logs at a given level.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
class LogLevelHook extends AbstractCallbackHookReporting
{
    /** @param int $level The level of the logs that will only be hooked. */
    public function __construct(
        public readonly int $level,
        ?\Closure $before = null,
        ?\Closure  $after = null,
    ) {

        parent::__construct($before, $after);

    }

    public function shouldHook(Log $log, ?int $currentSeverity, int $nextSeverity, HookableReport $report): bool
    {

        return $log->level === $this->level;

    }
}
