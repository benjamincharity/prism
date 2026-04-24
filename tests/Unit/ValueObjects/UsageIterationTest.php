<?php

declare(strict_types=1);

use Prism\Prism\ValueObjects\Usage;
use Prism\Prism\ValueObjects\UsageIteration;

it('constructs with required fields only', function (): void {
    $iteration = new UsageIteration(
        type: 'message',
        inputTokens: 100,
        outputTokens: 50,
    );

    expect($iteration->type)->toBe('message')
        ->and($iteration->inputTokens)->toBe(100)
        ->and($iteration->outputTokens)->toBe(50)
        ->and($iteration->cacheReadInputTokens)->toBe(0)
        ->and($iteration->cacheWriteInputTokens)->toBe(0);
});

it('constructs with all fields', function (): void {
    $iteration = new UsageIteration(
        type: 'advisor_message',
        inputTokens: 700,
        outputTokens: 150,
        cacheReadInputTokens: 200,
        cacheWriteInputTokens: 100,
    );

    expect($iteration->type)->toBe('advisor_message')
        ->and($iteration->inputTokens)->toBe(700)
        ->and($iteration->outputTokens)->toBe(150)
        ->and($iteration->cacheReadInputTokens)->toBe(200)
        ->and($iteration->cacheWriteInputTokens)->toBe(100);
});

it('fromArray parses Anthropic wire format', function (): void {
    $iteration = UsageIteration::fromArray([
        'type' => 'message',
        'input_tokens' => 2800,
        'output_tokens' => 650,
        'cache_read_input_tokens' => 2000,
        'cache_creation_input_tokens' => 100,
    ]);

    expect($iteration->type)->toBe('message')
        ->and($iteration->inputTokens)->toBe(2800)
        ->and($iteration->outputTokens)->toBe(650)
        ->and($iteration->cacheReadInputTokens)->toBe(2000)
        ->and($iteration->cacheWriteInputTokens)->toBe(100);
});

it('fromArray treats missing fields as zero and empty type', function (): void {
    $iteration = UsageIteration::fromArray([]);

    expect($iteration->type)->toBe('')
        ->and($iteration->inputTokens)->toBe(0)
        ->and($iteration->outputTokens)->toBe(0)
        ->and($iteration->cacheReadInputTokens)->toBe(0)
        ->and($iteration->cacheWriteInputTokens)->toBe(0);
});

it('fromIterationsArray parses a raw array of iteration entries', function (): void {
    $result = UsageIteration::fromIterationsArray([
        ['type' => 'message', 'input_tokens' => 2800, 'output_tokens' => 650, 'cache_read_input_tokens' => 2000],
        ['type' => 'advisor_message', 'input_tokens' => 700, 'output_tokens' => 150],
    ]);

    expect($result)->toHaveCount(2)
        ->and($result[0]->type)->toBe('message')
        ->and($result[0]->inputTokens)->toBe(2800)
        ->and($result[1]->type)->toBe('advisor_message')
        ->and($result[1]->inputTokens)->toBe(700);
});

it('fromIterationsArray returns null for null input', function (): void {
    expect(UsageIteration::fromIterationsArray(null))->toBeNull();
});

it('fromIterationsArray returns null for empty array', function (): void {
    expect(UsageIteration::fromIterationsArray([]))->toBeNull();
});

it('fromIterationsArray returns null for non-array input', function (): void {
    expect(UsageIteration::fromIterationsArray('string'))->toBeNull();
});

it('toArray serializes all fields', function (): void {
    $iteration = new UsageIteration(
        type: 'advisor_message',
        inputTokens: 700,
        outputTokens: 150,
        cacheReadInputTokens: 50,
        cacheWriteInputTokens: 25,
    );

    expect($iteration->toArray())->toBe([
        'type' => 'advisor_message',
        'input_tokens' => 700,
        'output_tokens' => 150,
        'cache_read_input_tokens' => 50,
        'cache_write_input_tokens' => 25,
    ]);
});

it('Usage iterations defaults to null', function (): void {
    $usage = new Usage(promptTokens: 10, completionTokens: 5);

    expect($usage->iterations)->toBeNull();
});

it('Usage stores an iterations array', function (): void {
    $iterations = [
        new UsageIteration('message', 2800, 650, 2000),
        new UsageIteration('advisor_message', 700, 150, 0),
    ];

    $usage = new Usage(
        promptTokens: 3500,
        completionTokens: 800,
        iterations: $iterations,
    );

    expect($usage->iterations)->toHaveCount(2)
        ->and($usage->iterations[0]->type)->toBe('message')
        ->and($usage->iterations[1]->type)->toBe('advisor_message');
});

it('Usage toArray serializes iterations when present', function (): void {
    $usage = new Usage(
        promptTokens: 3500,
        completionTokens: 800,
        cacheReadInputTokens: 2000,
        iterations: [
            new UsageIteration('message', 2800, 650, 2000),
            new UsageIteration('advisor_message', 700, 150, 0),
        ],
    );

    $array = $usage->toArray();

    expect($array)->toHaveKey('iterations')
        ->and($array['iterations'])->toHaveCount(2)
        ->and($array['iterations'][0])->toBe([
            'type' => 'message',
            'input_tokens' => 2800,
            'output_tokens' => 650,
            'cache_read_input_tokens' => 2000,
            'cache_write_input_tokens' => 0,
        ])
        ->and($array['iterations'][1])->toBe([
            'type' => 'advisor_message',
            'input_tokens' => 700,
            'output_tokens' => 150,
            'cache_read_input_tokens' => 0,
            'cache_write_input_tokens' => 0,
        ]);
});

it('Usage toArray serializes iterations as null when absent', function (): void {
    $usage = new Usage(promptTokens: 10, completionTokens: 5);

    expect($usage->toArray()['iterations'])->toBeNull();
});
