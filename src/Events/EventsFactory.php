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
    public static function beforeFromHookReportingLog(HookReportingLogEvent $event): BeforeReportingLogEvent
    {

        return new BeforeReportingLogEvent($event->log, $event->initialSeverity, $event->finalSeverity, $event->getHookableReport());

    }

    public static function afterFromHookReportingLog(HookReportingLogEvent $event): AfterReportingLogEvent
    {

        return new AfterReportingLogEvent($event->log, $event->initialSeverity, $event->finalSeverity, $event->getHookableReport());

    }
}
