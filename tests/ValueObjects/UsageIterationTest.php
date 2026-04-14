<?php

declare(strict_types=1);

use Prism\Prism\ValueObjects\UsageIteration;

it('constructs with required parameters and defaults', function (): void {
    $iteration = new UsageIteration(
        type: 'message',
        inputTokens: 2800,
        outputTokens: 650,
    );

    expect($iteration->type)->toBe('message')
        ->and($iteration->inputTokens)->toBe(2800)
        ->and($iteration->outputTokens)->toBe(650)
        ->and($iteration->cacheReadInputTokens)->toBe(0)
        ->and($iteration->cacheWriteInputTokens)->toBe(0);
});

it('constructs with all parameters', function (): void {
    $iteration = new UsageIteration(
        type: 'advisor_message',
        inputTokens: 700,
        outputTokens: 150,
        cacheReadInputTokens: 500,
        cacheWriteInputTokens: 200,
    );

    expect($iteration->type)->toBe('advisor_message')
        ->and($iteration->inputTokens)->toBe(700)
        ->and($iteration->outputTokens)->toBe(150)
        ->and($iteration->cacheReadInputTokens)->toBe(500)
        ->and($iteration->cacheWriteInputTokens)->toBe(200);
});

it('converts to array with snake_case keys', function (): void {
    $iteration = new UsageIteration(
        type: 'message',
        inputTokens: 2800,
        outputTokens: 650,
        cacheReadInputTokens: 2000,
    );

    expect($iteration->toArray())->toBe([
        'type' => 'message',
        'input_tokens' => 2800,
        'output_tokens' => 650,
        'cache_read_input_tokens' => 2000,
        'cache_write_input_tokens' => 0,
    ]);
});

it('returns null when concatenating two null arrays', function (): void {
    expect(UsageIteration::concat(null, null))->toBeNull();
});

it('returns items when concatenating null with an array', function (): void {
    $items = [
        new UsageIteration(type: 'message', inputTokens: 100, outputTokens: 50),
    ];

    expect(UsageIteration::concat(null, $items))->toHaveCount(1)
        ->and(UsageIteration::concat(null, $items)[0]->type)->toBe('message');
});

it('returns items when concatenating an array with null', function (): void {
    $items = [
        new UsageIteration(type: 'advisor_message', inputTokens: 200, outputTokens: 75),
    ];

    expect(UsageIteration::concat($items, null))->toHaveCount(1)
        ->and(UsageIteration::concat($items, null)[0]->type)->toBe('advisor_message');
});

it('merges two arrays when concatenating', function (): void {
    $a = [new UsageIteration(type: 'message', inputTokens: 100, outputTokens: 50)];
    $b = [new UsageIteration(type: 'advisor_message', inputTokens: 200, outputTokens: 75)];

    $result = UsageIteration::concat($a, $b);

    expect($result)->toHaveCount(2)
        ->and($result[0]->type)->toBe('message')
        ->and($result[1]->type)->toBe('advisor_message');
});
