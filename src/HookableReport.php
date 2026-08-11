<?php

namespace Aarvaos\Reporting;

use Aarvaos\Reporting\Events\EventsFactory;
use Aarvaos\Reporting\Events\ReportingLogEvent;
use Aarvaos\Reporting\Hooks\HookReportingInterface;

/**
 * Hookable version of a report allowing to handle logs reporting.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
class HookableReport extends Report
{
    /** @var HookReportingInterface[] */
    private array $hooks = [];

    /**
     * Register a hook handling some event implying log recorded by this report.
     *
     * @return static Current Report for chaining.
     */
    public function registerHook(HookReportingInterface $hook): static
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

        if ($this->reverseSeverity) {

            $severity = $this->getMinLevel();

            return $severity === null
                ? $log->level
                : min($log->level, $severity);

        }

        return max($log->level, $this->getMaxLevel());

    }

    #[\Override]
    protected function doReportLog(Log $log): void
    {

        $event = new ReportingLogEvent($log, $this->getSeverity(), $this->simulateSeverityAfter($log), $this);

        $hooks = array_filter($this->hooks, function (HookReportingInterface $hook) use ($event): bool {

            return $hook->shouldHook($event);

        });

        $beforeEvent = EventsFactory::beforeFromReportingLog($event);

        foreach ($hooks as $hook) {

            $hook->beforeReporting($beforeEvent);

        }

        if ($beforeEvent->isCancelled()) {

            return;

        }

        parent::doReportLog($log);

        $afterEvent = EventsFactory::afterFromReportingLog($event);

        foreach ($hooks as $hook) {

            $hook->afterReporting($afterEvent);

        }

    }
}
