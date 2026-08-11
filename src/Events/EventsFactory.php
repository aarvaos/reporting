<?php

namespace Aarvaos\Reporting\Events;

/**
 * Factory to ease creation of log reporting events.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 * @internal
 */
class EventsFactory
{
    public static function beforeFromReportingLog(ReportingLogEvent $event): BeforeReportingLogEvent
    {

        return new BeforeReportingLogEvent($event->log, $event->initialSeverity, $event->finalSeverity, $event->report);

    }

    public static function afterFromReportingLog(ReportingLogEvent $event): AfterReportingLogEvent
    {

        return new AfterReportingLogEvent($event->log, $event->initialSeverity, $event->finalSeverity, $event->report);

    }
}
