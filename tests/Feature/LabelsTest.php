<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;
use Sashalenz\ChatwootApi\Data\LabelData;

it('lists labels', function (): void {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    ChatwootApi::labels()->list();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/labels');
});

it('fetches a label', function (): void {
    Http::fake(['*' => Http::response(['id' => 4], 200)]);

    ChatwootApi::labels()->get(4);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/labels/4');
});

it('creates a label', function (): void {
    Http::fake(['*' => Http::response(['id' => 4], 200)]);

    $result = ChatwootApi::labels()->create(['title' => 'vip', 'color' => '#FF0000']);

    expect($result)->toBeInstanceOf(LabelData::class)
        ->and($result->id)->toBe(4);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/labels'
        && $request['title'] === 'vip');
});

it('updates a label via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 4], 200)]);

    ChatwootApi::labels()->update(4, ['color' => '#00FF00']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/labels/4');
});

it('deletes a label', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::labels()->delete(4);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/labels/4');
});
