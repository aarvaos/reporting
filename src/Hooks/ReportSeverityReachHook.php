<?php

namespace Aarvaos\Reporting\Hooks;

use Aarvaos\Reporting\Events\HookReportingLogEvent;

/**
 * Hook, once, a report having its severity going over the given threshold for the first time.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
class ReportSeverityReachHook extends AbstractCallbackHookReporting
{
    private bool $reached = false;

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

    // #[\Override]
    public function shouldHook(HookReportingLogEvent $event): bool
    {

        if ($this->reached) {

            return false;

        }

        if ($event->initialSeverity !== $event->finalSeverity) {

            if ($event->report->isSeverityReversed()) {

                $this->reached = $this->excluded
                    ? $event->finalSeverity < $this->threshold
                    : $event->finalSeverity <= $this->threshold;

            } else {

                $this->reached = $this->excluded
                    ? $event->finalSeverity > $this->threshold
                    : $event->finalSeverity >= $this->threshold;

            }

        }

        return $this->reached;


    }
}
