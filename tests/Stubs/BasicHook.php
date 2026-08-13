<?php

namespace Aarvaos\Reporting\Tests\Stubs;

use Aarvaos\Reporting\Events\ReportingLogEvent;
use Aarvaos\Reporting\Hooks\AbstractCallbackHookReporting;

class BasicHook extends AbstractCallbackHookReporting
{
    /** @param bool|\Closure(ReportingLogEvent): bool $apply */
    public function __construct(
        ?\Closure $before = null,
        ?\Closure  $after = null,
        public bool|\Closure $apply = true,
    ) {

        parent::__construct($before, $after);

    }

    public function shouldHook(ReportingLogEvent $event): bool
    {

        return is_callable($this->apply) ? call_user_func($this->apply, $event) : $this->apply;

    }
}
