<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;

it('lists custom filters', function (): void {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    ChatwootApi::customFilters()->list();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/custom_filters');
});

it('fetches a custom filter', function (): void {
    Http::fake(['*' => Http::response(['id' => 7], 200)]);

    ChatwootApi::customFilters()->get(7);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/custom_filters/7');
});

it('creates a custom filter', function (): void {
    Http::fake(['*' => Http::response(['id' => 7], 200)]);

    ChatwootApi::customFilters()->create(['name' => 'Urgent', 'type' => 'conversation', 'query' => []]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/custom_filters'
        && $request['name'] === 'Urgent');
});

it('updates a custom filter via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 7], 200)]);

    ChatwootApi::customFilters()->update(7, ['name' => 'Critical']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/custom_filters/7');
});

it('deletes a custom filter', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::customFilters()->delete(7);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/custom_filters/7');
});
