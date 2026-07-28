<?php

namespace Aarvaos\Reporting;

use Aarvaos\Reporting\Logs\Log;
use Aarvaos\Reporting\Logs\LogEntryInstantiator;

/**
 * Collect reported messages at arbitrary levels.
 * 
 * @author Corentin FIEUX <aarvaos@gmail.com>
 */
class Report implements \Countable, \IteratorAggregate
{

    /**
     * Sort logs by stack order (default).
     */
    const SORT_LOGGED = 0;

    /**
     * Sort logs by ascending severity (from less severe to more severe).
     */
    const SORT_SEVERITY_ASC = 1;

    /**
     * Sort logs by descending severity (high severity first, low severity last).
     */
    const SORT_SEVERITY_DESC = -1;

    /**
     * @var Log[]
     * {@internal Would work with a LogEntryTrait}
     */
    private array $logs = [];

    /**
     * Logs indexed by level (for optimization purpose).
     * 
     * @var array<int, Log[]>
     */
    private array $index = [];

    /**
     * @param bool      $reverseSeverity    (optional) Whether severity is descending (severe at lowest level) instead of ascending (severe at highest level).
     * @param string    $logClass           (optional) The Log entries objects class for logging unexplicitly instantiated ones ; must implements LogEntryInstantiator.
     * @throws \DomainException if the log entry class is not valid Log.
     */
    public function __construct(
        protected bool $reverseSeverity = false,
        protected readonly string $logClass = Log::class,
    ) {

        if (!is_a($logClass, LogEntryInstantiator::class, true)) {

            throw new \DomainException(sprintf('Log entries class must be a `%s`!', LogEntryInstantiator::class));
        }
    }

    /**
     * Log a message at specific level.
     * 
     * @param int       $level      An arbitrary level to report the message.
     * @param string    $message    The message to log.
     * @param mixed     ...$extra   (optional) More data provided as parameters to instanciate the log class.
     * @return static Current report for chaining.
     * @see Report::logCustom()   For logging custom Log objects.
     */
    public function log(int $level, string $message, mixed ...$extra): static
    {

        return $this->logCustom($this->logClass::instantiate($level, $message, ...$extra));
    }

    /**
     * Register a log.
     * 
     * @param Log $log The log entry object to register.
     * @return static Current report for chaining.
     */
    public function logCustom(/* LogEntryTrait */Log $log): static
    {

        $this->doRecordLog($log);

        return $this;
    }

    protected final function doRecordLog(/* LogEntryTrait */Log $log): void
    {

        $this->index[$log->level][] = $this->logs[] = $log;
    }

    public function countLogs(): int
    {

        return count($this->logs);
    }

    public function count(): int
    {

        return $this->countLogs();
    }

    public function getMaxLevel(): ?int
    {

        return $this->index ? max(array_keys($this->index)) : null;
    }

    public function getMinLevel(): ?int
    {

        return $this->index ? min(array_keys($this->index)) : null;
    }

    /**
     * Get the highest (or lowest if reversed) level of logs.
     * 
     * @return int|null The severity as an integer or null if no log have been reported.
     */
    public function getSeverity(): ?int
    {

        return $this->reverseSeverity ? $this->getMinLevel() : $this->getMaxLevel();
    }

    /**
     * Check if the severity of the Report is at or beyond the threshold mark.
     * 
     * @param int   $threshold  The log level at which the report is considered to have reached the targeted severity.
     * @param bool  $excluded   (optional) Whether a Report exactly at the threshold should not be considered as reached (> instead of >=).
     * @return bool|null The severity has reached threshold, or null if no log have been reported.
     */
    public function hasSeverityReached(int $threshold, bool $excluded = false): ?bool
    {

        $severity = $this->getSeverity();

        if ($severity === null) {

            return null;
        }

        if ($this->reverseSeverity) {

            return $excluded ? $severity < $threshold : $severity <= $threshold;
        }

        return $excluded ? $severity > $threshold : $severity >= $threshold;
    }

    /**
     * Get the reported logs.
     * 
     * @param null|int|int[] $level Restrict the logs retrieved to the ones at the given level(s).
     * @return Log[]
     */
    public function getLogs(null|int|array $level = null): array
    {

        if ($level === null) {

            return $this->logs;
        }

        if (is_array($level)) {

            if (count($level) > 1) {

                $logs = array_intersect_key($this->index, array_flip($level));

                if (!$logs) {

                    return [];
                }

                if (count($logs) > 1) {

                    $logs = array_values(array_filter($this->logs, static fn(Log $log) => !empty($logs[$log->level]) && in_array($log, $logs[$log->level], true)));
                } else {

                    return reset($logs);
                }

                return $logs;
            } else if ($level) {

                $level = reset($level);
            } else {

                return [];
            }
        }

        return $this->index[$level] ?? [];
    }

    /**
     * Traverse the logs, possibly within given severity interval so that ]$above;$below[.
     * NOTE: "below" and "above" refer to the severity so if the Report has it reversed, "above" means lower level and "below" higher.
     * 
     * @param int       $sort       (optional) The order of providing logs ; use class' constants SORT_* (ultimately sorted by logging order).
     * @param int|null  $above      (optional) Provide only logs having severity over this threshold.
     * @param int|null  $below      (optional) Provide only logs having severity under this threshold ; if both $above and $below are indicated with $below < $above, providing interval will be [~;$below[U]$above;~].
     * @param bool      $in_above   (optional) logs being equals to the $above boundary will also be provided.
     * @param bool      $in_below   (optional) logs being equals to the $below boundary will also be provided.
     * @return \Generator<int, Log> Logs where index is its rank in the log entries chart.
     */
    public function iterateLogs(int $sort = self::SORT_LOGGED, ?int $above = null, ?int $below = null, bool $in_above = false, bool $in_below = false): \Generator
    {

        if ($this->reverseSeverity) {

            [$above, $in_above, $below, $in_below] = [$below, $in_below, $above, $in_above];
        }

        $inversedInterval = $above !== null && $below !== null && $above > $below;

        $logs = $this->logs;

        if ($sort) {

            if ($this->reverseSeverity ? $sort < 0 : $sort > 0) {

                uasort($logs, static fn(Log $a, Log $b) => $a->level <=> $b->level);
            } else {

                uasort($logs, static fn(Log $a, Log $b) => $b->level <=> $a->level);
            }
        }

        foreach ($logs as $rank => $log) {

            $isOverAbove = $above === null || ($in_above ? $log->level >= $above : $log->level > $above);
            $isUnderBelow = $below === null || ($in_below ? $log->level <= $below : $log->level < $below);

            if (
                $inversedInterval ?
                $isOverAbove || $isUnderBelow :
                $isOverAbove && $isUnderBelow
            ) {

                yield $rank => $log;
            }
        }
    }

    /** @return \Traversable<int, Log> */
    public function getIterator(): \Traversable
    {

        return $this->iterateLogs();
    }

    /**
     * Use a report as a callback to ease its use.
     * The operation depends on the number and type of parameters :
     * - 0: get current severity,
     * - 1 integer: check if severity reached passed parameter,
     * - 1 Log / 2: store the log data.
     * 
     * @param int|Log   $log_level  (optional) Either the severity to check reaching (as an integer) ; or the level of the log to store / directly the Log object.
     * @param string    $message    (optional) The message of the log to store.
     * @param mixed     ...$extra   (optional) Additionnal payload of the log to store. 
     * @return mixed Depending of the parameters:
     *               - 0: the current severity as an integer or null if no logging have been performed yet,
     *               - 1 integer: a boolean indicating whether the passed severity has been reached,
     *               - 1 Log / 2: the curent Report instance for chaining.
     * @throws \InvalidArgumentException on messing up the logging by passing both an instanciated log object and additionnal parameters.
     * @see Report::getSeverity()
     * @see Report::hasSeverityReached()
     * @see Report::log()
     * @see Report::logCustom()
     */
    public function __invoke(int|Log $log_level = 0, string $message = '', mixed ...$extra): mixed
    {

        if ($log_level instanceof Log && func_num_args() > 1) {

            throw new \InvalidArgumentException(sprintf('Either pass a single `%s` object or its parameters.', Log::class));
        }

        switch (func_num_args()) {
            case 0:
                return $this->getSeverity();
            case 1:
                return $log_level instanceof Log ? $this->logCustom($log_level) : $this->hasSeverityReached($log_level);
            case 2:
            default:
                return $this->log($log_level, $message, ...$extra);
        }
    }
}
