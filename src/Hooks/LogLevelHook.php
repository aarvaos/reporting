<?php

declare(strict_types=1);

namespace Aarvaos\Reporting\Hooks;

use Aarvaos\Reporting\Events\ReportingLogEvent;

/**
 * Hook all logs at a given level.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
class LogLevelHook extends AbstractCallbackHookReporting
{
    /** @param int $level The level of the logs that will only be hooked. */
    public function __construct(
        public readonly int $level,
        ?\Closure $before = null,
        ?\Closure  $after = null,
    ) {

        parent::__construct($before, $after);

    }

    // #[\Override]
    public function shouldHook(ReportingLogEvent $event): bool
    {

        return $event->log->level === $this->level;

    }
}
