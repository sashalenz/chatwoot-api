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
