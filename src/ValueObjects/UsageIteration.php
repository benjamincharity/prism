<?php

declare(strict_types=1);

namespace Prism\Prism\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class UsageIteration implements Arrayable
{
    public function __construct(
        public string $type,
        public int $inputTokens,
        public int $outputTokens,
        public int $cacheReadInputTokens = 0,
        public int $cacheWriteInputTokens = 0,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: (string) ($data['type'] ?? ''),
            inputTokens: (int) ($data['input_tokens'] ?? 0),
            outputTokens: (int) ($data['output_tokens'] ?? 0),
            cacheReadInputTokens: (int) ($data['cache_read_input_tokens'] ?? 0),
            cacheWriteInputTokens: (int) ($data['cache_creation_input_tokens'] ?? 0),
        );
    }

    /**
     * @return array<int, self>|null
     */
    public static function fromIterationsArray(mixed $rawIterations): ?array
    {
        if (! is_array($rawIterations) || $rawIterations === []) {
            return null;
        }

        $iterations = [];
        foreach ($rawIterations as $entry) {
            if (! is_array($entry)) {
                continue;
            }
            $iterations[] = self::fromArray($entry);
        }

        return $iterations === [] ? null : $iterations;
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'input_tokens' => $this->inputTokens,
            'output_tokens' => $this->outputTokens,
            'cache_read_input_tokens' => $this->cacheReadInputTokens,
            'cache_write_input_tokens' => $this->cacheWriteInputTokens,
        ];
    }
}
