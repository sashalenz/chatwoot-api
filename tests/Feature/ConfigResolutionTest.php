<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

it('throws when the token is not configured', function (): void {
    config()->set('chatwoot-api.token', null);

    ChatwootApi::contacts()->create(['name' => 'x']);
})->throws(ChatwootApiException::class, 'token is not configured');

it('throws when the account id is not configured', function (): void {
    config()->set('chatwoot-api.account_id', null);

    ChatwootApi::contacts()->create(['name' => 'x']);
})->throws(ChatwootApiException::class, 'account id is not configured');

it('lets a per-call override win over config (token + account id)', function (): void {
    Http::fake(['*' => Http::response(['ok' => true], 200)]);

    ChatwootApi::messages()->accountId(2)->token('override-token')->create(99, 'hi');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://chatwoot.test/api/v1/accounts/2/conversations/99/messages'
        && $request->hasHeader('api_access_token', 'override-token'));
});

it('wraps transport errors in ChatwootApiException', function (): void {
    Http::fake(['*' => Http::response(['error' => 'nope'], 422)]);

    ChatwootApi::contacts()->create(['name' => 'x']);
})->throws(ChatwootApiException::class, 'transport error');
