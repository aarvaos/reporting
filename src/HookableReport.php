<?php

namespace Aarvaos\Reporting;

use Aarvaos\Reporting\Events\EventsFactory;
use Aarvaos\Reporting\Events\HookReportingLogEvent;
use Aarvaos\Reporting\Hooks\ReportingHookInterface;

/**
 * Hookable version of a report allowing to handle logs reporting.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
class HookableReport extends Report
{
    /** @var ReportingHookInterface[] */
    private array $hooks = [];

    /**
     * Register a hook handling some event implying log recorded by this report.
     *
     * @return static Current Report for chaining.
     */
    public function registerHook(ReportingHookInterface $hook): static
    {

        $this->hooks[] = $hook;

        return $this;

    }

    /**
     * Calculate the theorical severity after reporting a log without actually adding it.
     *
     * @param Log<mixed> $log The log about to be added after which the severity will be calculated.
     * @return int
     */
    protected function simulateSeverityAfter(Log $log): int
    {

        if ($this->isSeverityReversed()) {

            $severity = $this->getMinLevel();

            return $severity === null
                ? $log->level
                : min($log->level, $severity);

        }

        return max($log->level, $this->getMaxLevel());

    }

    /** @param Log<mixed> $log */
    // #[\Override]
    protected function doReportLog(Log $log): void
    {

        $event = new HookReportingLogEvent($log, $this->getSeverity(), $this->simulateSeverityAfter($log), $this);

        $hooks = array_filter($this->hooks, function (ReportingHookInterface $hook) use ($event): bool {

            return $hook->shouldHook($event);

        });

        $beforeEvent = EventsFactory::beforeFromHookReportingLog($event);

        foreach ($hooks as $hook) {

            $hook->beforeReporting($beforeEvent);

        }

        if ($beforeEvent->isCancelled()) {

            return;

        }

        parent::doReportLog($log);

        $afterEvent = EventsFactory::afterFromHookReportingLog($event);

        foreach ($hooks as $hook) {

            $hook->afterReporting($afterEvent);

        }

    }
}
