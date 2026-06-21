<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;
use Sashalenz\ChatwootApi\Data\WebhookData;

it('lists webhooks', function (): void {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    ChatwootApi::webhooks()->list();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/webhooks');
});

it('creates a webhook', function (): void {
    Http::fake(['*' => Http::response(['id' => 12], 200)]);

    $result = ChatwootApi::webhooks()->create([
        'url' => 'https://x/hook',
        'subscriptions' => ['message_created'],
    ]);

    expect($result)->toBeInstanceOf(WebhookData::class)
        ->and($result->id)->toBe(12);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/webhooks'
        && $request['url'] === 'https://x/hook'
        && $request['subscriptions'] === ['message_created']);
});

it('updates a webhook via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 12], 200)]);

    ChatwootApi::webhooks()->update(12, ['subscriptions' => ['conversation_created']]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/webhooks/12'
        && $request['subscriptions'] === ['conversation_created']);
});

it('deletes a webhook', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::webhooks()->delete(12);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/webhooks/12');
});
