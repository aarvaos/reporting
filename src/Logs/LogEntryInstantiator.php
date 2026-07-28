<?php

namespace Aarvaos\Reporting\Logs;

/**
 * Methods allowing creation of a log entry object.
 */
interface LogEntryInstantiator
{

    /**
     * Create an instance of the log.
     * 
     * {@internal This method is called by a Report object record a log by parameters.}
     * @see Report::log()
     */
    public static function instantiate(int $level, string $message): /* LogEntryTrait */ Log;
}
