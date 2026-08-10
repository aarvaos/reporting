<?php

namespace Aarvaos\Reporting\Hooks;

use Aarvaos\Reporting\HookableReport;
use Aarvaos\Reporting\Log;

/**
 * Base implementation of a hook by passing callbacks.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
abstract class AbstractCallbackHookReporting implements HookReportingInterface
{
    /**
     * @param \Closure(Log<mixed>, int, HookableReport): void|null     $before (optional) Callback executed before actually reporting the log.
     *                                                                          The callback receives the following parameters:
     *                                                                          - the log being reported;
     *                                                                          - the expected severity of the report after addition of the log;
     *                                                                          - the report where the log will be added to;
     * @param \Closure(Log<mixed>, ?int, HookableReport): void|null    $after  (optional) Callback executed after actually reporting the log.
     *                                                                          The callback receives the following parameters:
     *                                                                          - the log being reported;
     *                                                                          - the previous severity of the report before addition of the log;
     *                                                                          - the report where the log has been added to;
     * @see HookReportingInterface::beforeReporting()
     * @see HookReportingInterface::afterReporting()
     */
    public function __construct(
        protected readonly ?\Closure $before = null,
        protected readonly ?\Closure  $after = null,
    ) {
    }

    public function beforeReporting(Log $log, int $severityAfter, HookableReport $report): void
    {

        if ($this->before) {

            ($this->before)($log, $severityAfter, $report);

        }

    }

    public function afterReporting(Log $log, ?int $severityBefore, HookableReport $report): void
    {

        if ($this->after) {

            ($this->after)($log, $severityBefore, $report);

        }

    }
}
