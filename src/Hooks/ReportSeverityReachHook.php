<?php

namespace Aarvaos\Reporting\Hooks;

use Aarvaos\Reporting\Events\ReportingLogEvent;

/**
 * Hook, once, a report having its severity going over the given threshold for the first time.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
class ReportSeverityReachHook extends AbstractCallbackHookReporting
{
    /**
     * @param int   $threshold  The report's severity threshold to reach to trigger the hook.
     * @param bool  $excluded   (optional) Hook only if the severity is strictly above the threshold (instead of equal or above).
     */
    public function __construct(
        public readonly int $threshold,
        ?\Closure $before = null,
        ?\Closure  $after = null,
        public readonly bool $excluded = false,
    ) {

        parent::__construct($before, $after);

    }

    public function shouldHook(ReportingLogEvent $event): bool
    {

        return false;

    }
}
