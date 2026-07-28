<?php

namespace Aarvaos\Reporting\Tests\Stubs;

class CustomLog extends ExtendedLog
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

    public static function instantiate(int $level, string $message, mixed ...$extra): static
    {
        return new static($message, $level, ...$extra);
    }
}
