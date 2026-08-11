<?php

namespace Aarvaos\Reporting\Tests\Stubs;

use Aarvaos\Reporting\Events\ReportingLogEvent;
use Aarvaos\Reporting\Hooks\AbstractCallbackHookReporting;

class BasicHook extends AbstractCallbackHookReporting
{
    /** @param \Closure(ReportingLogEvent): bool $apply */
    public function __construct(
        public \Closure $apply,
        ?\Closure $before = null,
        ?\Closure  $after = null,
    ) {

        parent::__construct($before, $after);

    }

    public function shouldHook(ReportingLogEvent $event): bool
    {

        return ($this->apply)($event);

    }
}
