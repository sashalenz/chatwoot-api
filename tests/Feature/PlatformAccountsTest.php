<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

it('creates an account with the platform token (not the app token)', function (): void {
    Http::fake(['*' => Http::response(['id' => 7], 200)]);

    ChatwootApi::platformAccounts()->create(['name' => 'New Co']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/accounts'
        && $request->hasHeader('api_access_token', 'platform-token')
        && $request['name'] === 'New Co');
});

it('fetches an account', function (): void {
    Http::fake(['*' => Http::response(['id' => 7], 200)]);

    ChatwootApi::platformAccounts()->get(7);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/accounts/7');
});

it('updates an account via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 7], 200)]);

    ChatwootApi::platformAccounts()->update(7, ['name' => 'Renamed']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/accounts/7');
});

it('deletes an account', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::platformAccounts()->delete(7);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/accounts/7');
});

it('lists account users', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::platformAccounts()->users(7);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/accounts/7/account_users');
});

it('links a user to an account with a role', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::platformAccounts()->createUser(7, 11, 'administrator');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/accounts/7/account_users'
        && $request['user_id'] === 11
        && $request['role'] === 'administrator');
});

it('unlinks a user from an account via DELETE body', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::platformAccounts()->deleteUser(7, 11);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/platform/api/v1/accounts/7/account_users'
        && $request['user_id'] === 11);
});

it('throws when the platform token is not configured', function (): void {
    config()->set('chatwoot-api.platform_token', null);

    ChatwootApi::platformAccounts()->create(['name' => 'x']);
})->throws(ChatwootApiException::class, 'config chatwoot-api.platform_token');
