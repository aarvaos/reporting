<?php

namespace Aarvaos\Reporting\Logs;

/**
 * Characteristics of a log entry being a message at an arbitrary level.
 */
trait LogEntryTrait
{

    public readonly int $level;
    public readonly string $message;
}
