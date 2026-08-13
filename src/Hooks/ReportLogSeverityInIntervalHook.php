<?php

namespace Aarvaos\Reporting\Hooks;

use Aarvaos\Reporting\Events\HookReportingLogEvent;
use Closure;

/**
 * Hook all the logs, within an indicated severity interval, registering in a report.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
class ReportLogSeverityInIntervalHook extends AbstractCallbackHookReporting
{
    /**
     * @param bool  $in_over    (optional) Will include the over boundary (otherwise it will be tested strictly over).
     * @param bool  $in_under   (optional) Will include the under boundary (otherwise it will be tested strictly under).
     */
    public function __construct(
        public readonly ?int $over = null,
        public readonly bool $in_over = false,
        public readonly ?int $under = null,
        public readonly bool $in_under = false,
        ?Closure $before = null,
        ?Closure $after = null,
    ) {

        parent::__construct($before, $after);

    }

    // #[\Override]
    public function shouldHook(HookReportingLogEvent $event): bool
    {

        $over = $this->over;
        $in_over = $this->in_over;
        $under = $this->under;
        $in_under = $this->in_under;

        if ($event->report->isSeverityReversed()) {

            [$over, $in_over, $under, $in_under] = [$under, $in_under, $over, $in_over];

        }

        $disjoint = $over !== null && $under !== null && $over > $under;

        $log = $event->log;

        $isOver = $over === null
            || (
                $in_over
                    ? $log->level >= $over
                    : $log->level > $over
            );
        $isUnder = $under === null
            || (
                $in_under
                    ? $log->level <= $under
                    : $log->level < $under
            );

        return $disjoint
            ? $isOver || $isUnder
            : $isOver && $isUnder;

    }
}
