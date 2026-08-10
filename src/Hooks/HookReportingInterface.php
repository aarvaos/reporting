<?php

namespace Aarvaos\Reporting\Hooks;

use Aarvaos\Reporting\HookableReport;
use Aarvaos\Reporting\Log;

/**
 * Interface of any hook registered at a (hookable) report.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
interface HookReportingInterface
{
    /**
     * The function to check if the hook's methods should be applied in the context.
     *
     * @param Log<mixed>        $log                The log being added to the report.
     * @param int               $currentSeverity    The current severity of the report before addition of the log.
     * @param int               $nextSeverity       The expected severity of the report after addition of the log.
     * @param HookableReport    $report             The report to which the log should be added.
     * @return bool Whether or not to call the before/after handlers on addition of the log to the report in the given context.
     */
    public function shouldHook(Log $log, ?int $currentSeverity, int $nextSeverity, HookableReport $report): bool;

    /**
     * Handler called before the log is actually added to the report.
     *
     * @param Log<mixed>        $log            The log being added to the report.
     * @param int               $severityAfter  The anticipated severity of the report after addition of the log.
     * @param HookableReport    $report         The report to which the log is added.
     */
    public function beforeReporting(Log $log, int $severityAfter, HookableReport $report): void;

    /**
     * Handler called after the log is actually added to the report.
     *
     * @param Log<mixed>        $log            The log being added to the report.
     * @param int               $severityBefore The previous severity of the report before addition of the log.
     * @param HookableReport    $report         The report to which the log is added.
     */
    public function afterReporting(Log $log, ?int $severityBefore, HookableReport $report): void;
}
