<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;

it('lists canned responses', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::cannedResponses()->list();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/canned_responses');
});

it('creates a canned response', function (): void {
    Http::fake(['*' => Http::response(['id' => 8], 200)]);

    ChatwootApi::cannedResponses()->create(['short_code' => 'hi', 'content' => 'Hello']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/canned_responses'
        && $request['short_code'] === 'hi');
});

it('updates a canned response via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 8], 200)]);

    ChatwootApi::cannedResponses()->update(8, ['content' => 'Hi there']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/canned_responses/8');
});

it('deletes a canned response', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::cannedResponses()->delete(8);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/canned_responses/8');
});
