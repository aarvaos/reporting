<?php

namespace Aarvaos\Reporting\Hooks;

use Aarvaos\Reporting\Events\AfterReportingLogEvent;
use Aarvaos\Reporting\Events\BeforeReportingLogEvent;

/**
 * Base implementation of a hook by passing callbacks.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
abstract class AbstractCallbackHookReporting implements ReportingHookInterface
{
    /**
     * @param (\Closure(BeforeReportingLogEvent): void)|null    $before (optional) Callback executed before actually reporting the log ; it receives the corresponding event as argument.
     * @param (\Closure(AfterReportingLogEvent): void)|null     $after  (optional) Callback executed after actually reporting the log ; it receives the corresponding event as argument.
     * @see ReportingHookInterface::beforeReporting()
     * @see ReportingHookInterface::afterReporting()
     */
    public function __construct(
        protected readonly ?\Closure $before = null,
        protected readonly ?\Closure  $after = null,
    ) {
    }

    // #[\Override]
    public function beforeReporting(BeforeReportingLogEvent $event): void
    {

        if ($this->before) {

            ($this->before)($event);

        }

    }

    // #[\Override]
    public function afterReporting(AfterReportingLogEvent $event): void
    {

        if ($this->after) {

            ($this->after)($event);

        }

    }
}
