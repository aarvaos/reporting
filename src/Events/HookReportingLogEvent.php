<?php

namespace Aarvaos\Reporting\Events;

use Aarvaos\Reporting\HookableReport;
use Aarvaos\Reporting\Log;

/**
 * Event of a log being reported is also hooked in the process.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 * @extends ReportingLogEvent<HookableReport>
 */
class HookReportingLogEvent extends ReportingLogEvent
{
    public function __construct(Log $log, ?int $initialSeverity, int $finalSeverity, HookableReport $report)
    {
        parent::__construct($log, $initialSeverity, $finalSeverity, $report);
    }

    public function getHookableReport(): HookableReport
    {

        return $this->report;

    }
}
