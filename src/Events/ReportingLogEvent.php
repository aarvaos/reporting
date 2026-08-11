<?php

declare(strict_types=1);

namespace Aarvaos\Reporting\Events;

use Aarvaos\Reporting\Log;
use Aarvaos\Reporting\Report;

/**
 * Base event when a log is reported.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
class ReportingLogEvent
{
    /**
     * @param Log<mixed>    $log                The log being reported.
     * @param int           $initialSeverity    The current severity of the report before registration of the log.
     * @param int           $finalSeverity      The expected severity of the report after registration of the log.
     * @param Report        $report             The report in which the log is being recorded.
     */
    public function __construct(
        public readonly Log $log,
        public readonly ?int $initialSeverity,
        public readonly int $finalSeverity,
        public readonly Report $report,
    ) {
    }
}
