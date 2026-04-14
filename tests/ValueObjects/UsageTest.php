<?php

declare(strict_types=1);

use Prism\Prism\ValueObjects\Usage;
use Prism\Prism\ValueObjects\UsageIteration;

it('constructs with only required fields and leaves iterations null', function (): void {
    $usage = new Usage(promptTokens: 10, completionTokens: 20);

    expect($usage->promptTokens)->toBe(10);
    expect($usage->completionTokens)->toBe(20);
    expect($usage->iterations)->toBeNull();
    expect($usage->toArray()['iterations'])->toBeNull();
});

it('preserves existing optional token fields alongside iterations', function (): void {
    $usage = new Usage(
        promptTokens: 100,
        completionTokens: 50,
        cacheWriteInputTokens: 5,
        cacheReadInputTokens: 15,
        thoughtTokens: 7,
        iterations: [
            new UsageIteration(type: 'message', inputTokens: 80, outputTokens: 40),
        ],
    );

    expect($usage->cacheWriteInputTokens)->toBe(5);
    expect($usage->cacheReadInputTokens)->toBe(15);
    expect($usage->thoughtTokens)->toBe(7);
    expect($usage->iterations)->toHaveCount(1);
    expect($usage->iterations[0]->type)->toBe('message');
});

it('serializes iterations to arrays via toArray', function (): void {
    $usage = new Usage(
        promptTokens: 3500,
        completionTokens: 800,
        iterations: [
            new UsageIteration(
                type: 'message',
                inputTokens: 2800,
                outputTokens: 650,
                cacheReadInputTokens: 2000,
            ),
            new UsageIteration(
                type: 'advisor_message',
                inputTokens: 700,
                outputTokens: 150,
            ),
        ],
    );

    $array = $usage->toArray();

    expect($array['prompt_tokens'])->toBe(3500);
    expect($array['completion_tokens'])->toBe(800);
    expect($array['iterations'])->toBeArray();
    expect($array['iterations'])->toHaveCount(2);
    expect($array['iterations'][0])->toBe([
        'type' => 'message',
        'input_tokens' => 2800,
        'output_tokens' => 650,
        'cache_read_input_tokens' => 2000,
        'cache_write_input_tokens' => 0,
    ]);
    expect($array['iterations'][1]['type'])->toBe('advisor_message');
    expect($array['iterations'][1]['input_tokens'])->toBe(700);
});
