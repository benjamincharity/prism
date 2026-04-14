<?php

declare(strict_types=1);

namespace Prism\Prism\Providers\Anthropic\Concerns;

use Prism\Prism\ValueObjects\UsageIteration;

trait ExtractsIterations
{
    /**
     * @return array<int, UsageIteration>|null
     */
    protected function extractIterations(mixed $rawIterations): ?array
    {
        if (! is_array($rawIterations) || $rawIterations === []) {
            return null;
        }

        $iterations = [];
        foreach ($rawIterations as $entry) {
            if (! is_array($entry)) {
                continue;
            }
            if (! isset($entry['type'])) {
                continue;
            }
            $iterations[] = new UsageIteration(
                type: (string) $entry['type'],
                inputTokens: (int) ($entry['input_tokens'] ?? 0),
                outputTokens: (int) ($entry['output_tokens'] ?? 0),
                cacheReadInputTokens: (int) ($entry['cache_read_input_tokens'] ?? 0),
                cacheWriteInputTokens: (int) ($entry['cache_creation_input_tokens'] ?? 0), // Anthropic uses 'cache_creation_input_tokens'
            );
        }

        return $iterations === [] ? null : $iterations;
    }
}
