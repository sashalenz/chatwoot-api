<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;
use Sashalenz\ChatwootApi\Data\InboxData;

it('lists inboxes from a payload envelope as typed DTOs', function (): void {
    Http::fake(['*' => Http::response([
        'payload' => [['id' => 1, 'name' => 'API', 'channel_type' => 'Channel::Api']],
    ], 200)]);

    $result = ChatwootApi::inboxes()->list();

    expect($result->count())->toBe(1)
        ->and($result->payload[0])->toBeInstanceOf(InboxData::class)
        ->and($result->payload[0]->name)->toBe('API')
        ->and($result->payload[0]->channelType)->toBe('Channel::Api');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/inboxes');
});

it('fetches a single inbox as a DTO', function (): void {
    Http::fake(['*' => Http::response(['id' => 1, 'name' => 'API'], 200)]);

    $result = ChatwootApi::inboxes()->get(1);

    expect($result)->toBeInstanceOf(InboxData::class)
        ->and($result->id)->toBe(1);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/inboxes/1');
});

it('creates an inbox', function (): void {
    Http::fake(['*' => Http::response(['id' => 9], 200)]);

    ChatwootApi::inboxes()->create(['name' => 'API', 'channel' => ['type' => 'api']]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/inboxes'
        && $request['name'] === 'API'
        && data_get($request->data(), 'channel.type') === 'api');
});

it('updates an inbox via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 1], 200)]);

    ChatwootApi::inboxes()->update(1, ['name' => 'Renamed']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/inboxes/1'
        && $request['name'] === 'Renamed');
});

it('shows the inbox agent-bot', function (): void {
    Http::fake(['*' => Http::response(['id' => 3], 200)]);

    ChatwootApi::inboxes()->agentBot(1);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/inboxes/1/agent_bot');
});

it('sets the inbox agent-bot', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::inboxes()->setAgentBot(1, 3);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/inboxes/1/set_agent_bot'
        && $request['agent_bot'] === 3);
});

it('lists inbox members', function (): void {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    ChatwootApi::inboxes()->members(1);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/inbox_members/1');
});

it('adds inbox members', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::inboxes()->addMembers(1, [5, 6]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/inbox_members'
        && $request['inbox_id'] === 1
        && $request['user_ids'] === [5, 6]);
});

it('updates inbox members via PATCH', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::inboxes()->updateMembers(1, [5]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/inbox_members'
        && $request['user_ids'] === [5]);
});

it('removes inbox members via DELETE', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::inboxes()->removeMembers(1, [6]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/inbox_members'
        && $request['user_ids'] === [6]);
});
