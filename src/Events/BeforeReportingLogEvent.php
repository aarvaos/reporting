<?php

namespace Aarvaos\Reporting\Events;

/**
 * Event sent just before a log is recorded in a report.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
class BeforeReportingLogEvent extends ReportingLogEvent
{
    private bool $cancel = false;

    /** Tell the report firing this event to cancel the recording of the log. */
    final public function cancel(): void
    {

        $this->cancel = true;

    }

    /** Check if the recording of the log in the report have been told to be canceled. */
    final public function isCancelled(): bool
    {

        return $this->cancel;

    }
}
