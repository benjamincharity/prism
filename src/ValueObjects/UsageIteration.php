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

    /**
     * @param  array<int, UsageIteration>|null  $a
     * @param  array<int, UsageIteration>|null  $b
     * @return array<int, UsageIteration>|null
     */
    public static function concat(?array $a, ?array $b): ?array
    {
        return [...($a ?? []), ...($b ?? [])] ?: null;
    }
}
