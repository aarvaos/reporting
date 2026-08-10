<?php

namespace Aarvaos\Reporting;

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

    /** Register a hook handling some event implying log recorded by this report. */
    public function registerHook(HookReportingInterface $hook): void
    {

        $this->hooks[] = $hook;

    }

    #[\Override]
    protected function doReportLog(Log $log): void
    {

        $currentSeverity = $this->getSeverity();
        $nextSeverity = $this->simulateSeverityAfter($log);

        $hooks = array_filter($this->hooks, function (HookReportingInterface $hook) use ($log, $currentSeverity, $nextSeverity): bool {

            return $hook->shouldHook($log, $currentSeverity, $nextSeverity, $this);

        });

        foreach ($hooks as $hook) {

            $hook->beforeReporting($log, $nextSeverity, $this);

        }

        parent::doReportLog($log);

        foreach ($hooks as $hook) {

            $hook->afterReporting($log, $currentSeverity, $this);

        }

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
}
