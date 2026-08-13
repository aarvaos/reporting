<?php

namespace Aarvaos\Reporting\Hooks;

use Aarvaos\Reporting\Events\HookReportingLogEvent;

/**
 * Hook every reporting of log.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
class LogReportedHook extends AbstractCallbackHookReporting
{
    // #[\Override]
    public function shouldHook(HookReportingLogEvent $event): bool
    {

        return true;

    }
}
