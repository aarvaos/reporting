<?php

namespace Aarvaos\Reporting\Logs;

/**
 * Base log entry as a message at an arbitrary level.
 */
class Log implements LogEntryInstantiator, \Stringable
{

    use LogEntryTrait;

    public function __construct(int $level, string $message)
    {

        $this->level = $level;
        $this->message = $message;
    }

    public function __toString(): string
    {

        return $this->message;
    }

    public static function instantiate(int $level, string $message): static
    {

        return new static($level, $message);
    }
}
