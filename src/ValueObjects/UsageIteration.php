<?php

declare(strict_types=1);

namespace Prism\Prism\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Per-model token breakdown for a single inference within a multi-model
 * provider response (e.g. Anthropic's advisor tool, where the executor and
 * advisor models consume tokens independently).
 *
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
