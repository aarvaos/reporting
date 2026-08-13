<?php

namespace Aarvaos\Reporting\Hooks;

use Aarvaos\Reporting\Events\HookReportingLogEvent;

class LogReportedHook extends AbstractCallbackHookReporting
{
    // #[\Override]
    public function shouldHook(HookReportingLogEvent $event): bool
    {

        return true;

    }
}
