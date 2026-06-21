<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;

it('creates an incoming message (inbound mirror)', function (): void {
    Http::fake(['*' => Http::response(['id' => 555, 'message_type' => 0], 200)]);

    $result = ChatwootApi::messages()->create(99, 'Привіт', 'incoming');

    expect($result->get('id'))->toBe(555);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/conversations/99/messages'
        && $request['content'] === 'Привіт'
        && $request['message_type'] === 'incoming');
});

it('creates an outgoing private note', function (): void {
    Http::fake(['*' => Http::response(['id' => 556], 200)]);

    ChatwootApi::messages()->create(99, 'AI suggestion', 'outgoing', ['private' => true]);

    Http::assertSent(fn (Request $request): bool => $request['message_type'] === 'outgoing'
        && $request['private'] === true);
});

it('lists messages of a conversation', function (): void {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    ChatwootApi::messages()->list(99, ['before' => 1000]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && str_starts_with($request->url(), 'https://chatwoot.test/api/v1/accounts/1/conversations/99/messages?')
        && $request['before'] === 1000);
});

it('deletes a message', function (): void {
    Http::fake(['*' => Http::response(['id' => 555, 'content' => 'deleted'], 200)]);

    ChatwootApi::messages()->delete(99, 555);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/conversations/99/messages/555');
});
