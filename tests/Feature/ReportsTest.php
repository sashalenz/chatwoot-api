<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;

it('fetches the account report at the v2 path with query params', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::reports()->account(['metric' => 'conversations_count', 'type' => 'account']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && str_starts_with($request->url(), 'https://chatwoot.test/api/v2/accounts/1/reports?')
        && $request['metric'] === 'conversations_count');
});

it('fetches the report summary', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::reports()->summary();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v2/accounts/1/reports/summary');
});

it('fetches conversation metrics', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::reports()->conversations();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v2/accounts/1/reports/conversations');
});

it('fetches first response time distribution', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::reports()->firstResponseTimeDistribution();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v2/accounts/1/reports/first_response_time_distribution');
});

it('fetches the inbox-label matrix', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::reports()->inboxLabelMatrix();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v2/accounts/1/reports/inbox_label_matrix');
});

it('fetches outgoing messages count grouped', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::reports()->outgoingMessagesCount(['group_by' => 'agent']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && str_starts_with($request->url(), 'https://chatwoot.test/api/v2/accounts/1/reports/outgoing_messages_count?')
        && $request['group_by'] === 'agent');
});
