<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;

it('lists agents', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::agents()->list();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/agents');
});

it('creates an agent', function (): void {
    Http::fake(['*' => Http::response(['id' => 5], 200)]);

    ChatwootApi::agents()->create(['user_id' => 5, 'role' => 'agent']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/agents'
        && $request['role'] === 'agent');
});

it('updates an agent via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 5], 200)]);

    ChatwootApi::agents()->update(5, ['role' => 'administrator']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/agents/5');
});

it('deletes an agent', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::agents()->delete(5);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/agents/5');
});
