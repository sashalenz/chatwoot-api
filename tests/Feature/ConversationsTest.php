<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;

it('creates a conversation from a source_id and inbox_id', function (): void {
    Http::fake(['*' => Http::response(['id' => 99, 'status' => 'open'], 200)]);

    $result = ChatwootApi::conversations()->create('src-abc', 1, ['status' => 'open']);

    expect($result->get('id'))->toBe(99);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/conversations'
        && $request['source_id'] === 'src-abc'
        && $request['inbox_id'] === 1
        && $request['status'] === 'open');
});

it('lists conversations with filters as query params', function (): void {
    Http::fake(['*' => Http::response(['data' => ['payload' => []]], 200)]);

    ChatwootApi::conversations()->list(['status' => 'open', 'assignee_type' => 'me', 'page' => 2]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && str_starts_with($request->url(), 'https://chatwoot.test/api/v1/accounts/1/conversations?')
        && $request['status'] === 'open'
        && $request['assignee_type'] === 'me'
        && $request['page'] === 2);
});

it('updates a conversation via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 99, 'priority' => 'high'], 200)]);

    ChatwootApi::conversations()->update(99, ['priority' => 'high']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/conversations/99'
        && $request['priority'] === 'high');
});

it('fetches a single conversation via GET', function (): void {
    Http::fake(['*' => Http::response(['id' => 99, 'status' => 'open'], 200)]);

    ChatwootApi::conversations()->show(99);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/conversations/99');
});

it('toggles conversation status for handoff', function (): void {
    Http::fake(['*' => Http::response(['payload' => ['current_status' => 'open']], 200)]);

    ChatwootApi::conversations()->toggleStatus(99, 'open');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/conversations/99/toggle_status'
        && $request['status'] === 'open');
});
