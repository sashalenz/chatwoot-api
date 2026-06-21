<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;
use Sashalenz\ChatwootApi\Data\PortalData;

it('lists portals as typed DTOs', function (): void {
    Http::fake(['*' => Http::response([
        'payload' => [['id' => 1, 'name' => 'Docs', 'slug' => 'docs']],
    ], 200)]);

    $result = ChatwootApi::helpCenter()->listPortals();

    expect($result->count())->toBe(1)
        ->and($result->payload[0])->toBeInstanceOf(PortalData::class)
        ->and($result->payload[0]->slug)->toBe('docs');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/portals');
});

it('creates a portal', function (): void {
    Http::fake(['*' => Http::response(['id' => 1], 200)]);

    ChatwootApi::helpCenter()->createPortal(['name' => 'Docs', 'slug' => 'docs']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/portals'
        && $request['slug'] === 'docs');
});

it('updates a portal via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 1], 200)]);

    ChatwootApi::helpCenter()->updatePortal(1, ['name' => 'Help']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/portals/1');
});

it('creates a category in a portal', function (): void {
    Http::fake(['*' => Http::response(['id' => 5], 200)]);

    ChatwootApi::helpCenter()->createCategory(1, ['name' => 'Billing', 'slug' => 'billing', 'locale' => 'en']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/portals/1/categories'
        && $request['name'] === 'Billing');
});

it('creates an article in a portal', function (): void {
    Http::fake(['*' => Http::response(['id' => 9], 200)]);

    ChatwootApi::helpCenter()->createArticle(1, ['title' => 'Refunds', 'content' => '…', 'category_id' => 5, 'author_id' => 11]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/portals/1/articles'
        && $request['title'] === 'Refunds');
});
