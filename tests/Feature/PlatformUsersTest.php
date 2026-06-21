<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;

it('creates a user', function (): void {
    Http::fake(['*' => Http::response(['id' => 11], 200)]);

    ChatwootApi::platformUsers()->create(['name' => 'Petro', 'email' => 'p@x.com', 'password' => 'secret']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/users'
        && $request->hasHeader('api_access_token', 'platform-token')
        && $request['email'] === 'p@x.com');
});

it('fetches a user', function (): void {
    Http::fake(['*' => Http::response(['id' => 11], 200)]);

    ChatwootApi::platformUsers()->get(11);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/users/11');
});

it('updates a user via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 11], 200)]);

    ChatwootApi::platformUsers()->update(11, ['display_name' => 'P']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/users/11');
});

it('deletes a user', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::platformUsers()->delete(11);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/users/11');
});

it('gets an SSO login link for a user', function (): void {
    Http::fake(['*' => Http::response(['url' => 'https://chatwoot.test/sso?token=x'], 200)]);

    ChatwootApi::platformUsers()->login(11);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/users/11/login');
});
