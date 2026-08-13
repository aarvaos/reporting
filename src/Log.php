<?php

declare(strict_types=1);

namespace Aarvaos\Reporting;

/**
 * Base log entry as an element at an arbitrary level.
 *
 * @author Corentin FIEUX <aarvaos@gmail.com>
 * @template-covariant T = mixed
 */
class Log
{
    /** @var T */
    private readonly mixed $payload;

    /** @param T $payload The embeded element's data (can be of any type). */
    public function __construct(
        public readonly int $level,
        mixed $payload,
    ) {

        $this->payload = $payload;

    }

    /** @return T The data of the element logged. */
    final public function getPayload(): mixed
    {
        return $this->payload;
    }
}
