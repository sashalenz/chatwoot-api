<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;
use Sashalenz\ChatwootApi\Data\AccountData;

it('fetches the account at the un-suffixed account path', function (): void {
    Http::fake(['*' => Http::response(['id' => 1, 'name' => 'A20'], 200)]);

    $result = ChatwootApi::account()->get();

    expect($result)->toBeInstanceOf(AccountData::class)
        ->and($result->name)->toBe('A20');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1');
});

it('updates the account via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 1], 200)]);

    ChatwootApi::account()->update(['name' => 'A20 Inc', 'locale' => 'uk']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1'
        && $request['locale'] === 'uk');
});
