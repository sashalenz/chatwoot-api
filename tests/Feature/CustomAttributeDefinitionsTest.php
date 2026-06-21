<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;

it('lists custom attribute definitions', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::customAttributeDefinitions()->list();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/custom_attribute_definitions');
});

it('fetches a custom attribute definition', function (): void {
    Http::fake(['*' => Http::response(['id' => 1], 200)]);

    ChatwootApi::customAttributeDefinitions()->get(1);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/custom_attribute_definitions/1');
});

it('creates a custom attribute definition', function (): void {
    Http::fake(['*' => Http::response(['id' => 1], 200)]);

    ChatwootApi::customAttributeDefinitions()->create([
        'attribute_display_name' => 'Order ID',
        'attribute_key' => 'order_id',
        'attribute_model' => 'conversation_attribute',
        'attribute_display_type' => 'text',
    ]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/custom_attribute_definitions'
        && $request['attribute_key'] === 'order_id');
});

it('updates a custom attribute definition via PATCH', function (): void {
    Http::fake(['*' => Http::response(['id' => 1], 200)]);

    ChatwootApi::customAttributeDefinitions()->update(1, ['attribute_display_name' => 'Order #']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/custom_attribute_definitions/1');
});

it('deletes a custom attribute definition', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::customAttributeDefinitions()->delete(1);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/custom_attribute_definitions/1');
});
