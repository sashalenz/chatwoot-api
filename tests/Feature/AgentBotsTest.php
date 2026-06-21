<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;
use Sashalenz\ChatwootApi\Data\AgentBotData;

it('lists agent bots', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::agentBots()->list();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/agent_bots');
});

it('fetches an agent bot', function (): void {
    Http::fake(['*' => Http::response(['id' => 3], 200)]);

    $result = ChatwootApi::agentBots()->get(3);

    expect($result)->toBeInstanceOf(AgentBotData::class)
        ->and($result->id)->toBe(3);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/agent_bots/3');
});

it('creates an agent bot', function (): void {
    Http::fake(['*' => Http::response(['id' => 3], 200)]);

    ChatwootApi::agentBots()->create(['name' => 'Router', 'outgoing_url' => 'https://x/webhook']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/agent_bots'
        && $request['name'] === 'Router');
});

it('updates an agent bot via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 3], 200)]);

    ChatwootApi::agentBots()->update(3, ['name' => 'Router 2']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/agent_bots/3');
});

it('deletes an agent bot', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::agentBots()->delete(3);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/agent_bots/3');
});
