<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;

it('lists platform agent bots', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::platformAgentBots()->list();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/agent_bots'
        && $request->hasHeader('api_access_token', 'platform-token'));
});

it('fetches a platform agent bot', function (): void {
    Http::fake(['*' => Http::response(['id' => 3], 200)]);

    ChatwootApi::platformAgentBots()->get(3);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/agent_bots/3');
});

it('creates a platform agent bot', function (): void {
    Http::fake(['*' => Http::response(['id' => 3], 200)]);

    ChatwootApi::platformAgentBots()->create(['name' => 'Bot', 'outgoing_url' => 'https://x/webhook']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/agent_bots'
        && $request['name'] === 'Bot');
});

it('updates a platform agent bot via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 3], 200)]);

    ChatwootApi::platformAgentBots()->update(3, ['name' => 'Bot 2']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/agent_bots/3');
});

it('deletes a platform agent bot', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::platformAgentBots()->delete(3);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/agent_bots/3');
});
