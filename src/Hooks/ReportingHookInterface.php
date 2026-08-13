<?php

namespace Aarvaos\Reporting\Hooks;

use Aarvaos\Reporting\Events\AfterReportingLogEvent;
use Aarvaos\Reporting\Events\BeforeReportingLogEvent;
use Aarvaos\Reporting\Events\HookReportingLogEvent;

/**
 * Interface of any hook registered at a (hookable) report.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
interface ReportingHookInterface
{
    /**
     * The function to check if the hook's methods should be applied in the context.
     *
     * @param HookReportingLogEvent $event The event corresponding to the context of the registration of a log in a report.
     * @return bool Whether or not to call the before/after handlers on addition of the log to the report in the given context.
     * @see ReportingHook::beforeReporting()
     * @see ReportingHook::afterReporting()
     */
    public function shouldHook(HookReportingLogEvent $event): bool;

    /** Handler called before the log is actually added to the report. */
    public function beforeReporting(BeforeReportingLogEvent $event): void;

    /** Handler called after the log has been actually added to the report. */
    public function afterReporting(AfterReportingLogEvent $event): void;
}
