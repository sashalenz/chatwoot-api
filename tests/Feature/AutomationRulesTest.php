<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;
use Sashalenz\ChatwootApi\Data\AutomationRuleData;

it('lists automation rules', function (): void {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    ChatwootApi::automationRules()->list();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/automation_rules');
});

it('fetches an automation rule and unwraps the payload envelope', function (): void {
    Http::fake(['*' => Http::response([
        'payload' => ['id' => 3, 'name' => 'Auto assign', 'event_name' => 'conversation_created', 'active' => true],
    ], 200)]);

    $result = ChatwootApi::automationRules()->get(3);

    expect($result)->toBeInstanceOf(AutomationRuleData::class)
        ->and($result->id)->toBe(3)
        ->and($result->name)->toBe('Auto assign')
        ->and($result->eventName)->toBe('conversation_created');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/automation_rules/3');
});

it('creates an automation rule', function (): void {
    Http::fake(['*' => Http::response(['payload' => ['id' => 3, 'name' => 'Auto assign']], 200)]);

    $created = ChatwootApi::automationRules()->create([
        'name' => 'Auto assign',
        'event_name' => 'conversation_created',
        'active' => true,
        'conditions' => [],
        'actions' => [],
    ]);

    expect($created)->toBeInstanceOf(AutomationRuleData::class)
        ->and($created->id)->toBe(3);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/automation_rules'
        && $request['event_name'] === 'conversation_created');
});

it('updates an automation rule via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 3], 200)]);

    ChatwootApi::automationRules()->update(3, ['active' => false]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/automation_rules/3'
        && $request['active'] === false);
});

it('deletes an automation rule', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::automationRules()->delete(3);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/automation_rules/3');
});
