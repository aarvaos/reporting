<?php

namespace Aarvaos\Reporting\Tests\Stubs;

use Aarvaos\Reporting\Log;

/** @extends Log<string> */
class CustomLog extends Log
{
    public function __construct(
        string $message,
        int $level,
        private readonly ?string $author = null,
        private readonly ?string $source = null,
        private readonly ?array $data = null,
    ) {
        parent::__construct($level, $message);
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getData(): ?array
    {
        return $this->data;
    }
}
