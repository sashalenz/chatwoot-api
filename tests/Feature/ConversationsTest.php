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

it('toggles conversation priority', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::conversations()->togglePriority(99, 'high');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/conversations/99/toggle_priority'
        && $request['priority'] === 'high');
});

it('assigns a conversation to an agent', function (): void {
    Http::fake(['*' => Http::response(['id' => 5], 200)]);

    ChatwootApi::conversations()->assign(99, assigneeId: 5);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/conversations/99/assignments'
        && $request['assignee_id'] === 5
        && ! array_key_exists('team_id', $request->data()));
});

it('sets conversation custom attributes', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::conversations()->setCustomAttributes(99, ['order_id' => 'A-1']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/conversations/99/custom_attributes'
        && data_get($request->data(), 'custom_attributes.order_id') === 'A-1');
});

it('adds labels to a conversation', function (): void {
    Http::fake(['*' => Http::response(['payload' => ['vip']], 200)]);

    ChatwootApi::conversations()->addLabels(99, ['vip']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/conversations/99/labels'
        && $request['labels'] === ['vip']);
});

it('lists labels of a conversation', function (): void {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    ChatwootApi::conversations()->labels(99);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/conversations/99/labels');
});

it('toggles agent typing status', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::conversations()->toggleTyping(99, 'on');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/conversations/99/toggle_typing_status'
        && $request['typing_status'] === 'on');
});

it('fetches conversation counts via meta', function (): void {
    Http::fake(['*' => Http::response(['meta' => ['all_count' => 3]], 200)]);

    ChatwootApi::conversations()->meta(['status' => 'open']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && str_starts_with($request->url(), 'https://chatwoot.test/api/v1/accounts/1/conversations/meta?')
        && $request['status'] === 'open');
});

it('filters conversations via POST', function (): void {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    ChatwootApi::conversations()->filter(['payload' => []]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/conversations/filter');
});
