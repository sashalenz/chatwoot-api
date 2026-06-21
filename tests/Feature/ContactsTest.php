<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;

it('creates a contact at the account-scoped path with the api_access_token header', function (): void {
    Http::fake([
        '*' => Http::response([
            'payload' => [
                'contact' => ['id' => 7, 'name' => 'Petro'],
                'contact_inbox' => ['source_id' => 'src-abc'],
            ],
        ], 200),
    ]);

    $result = ChatwootApi::contacts()->create([
        'name' => 'Petro',
        'phone_number' => '+380501234567',
        'inbox_id' => 1,
        'custom_attributes' => ['client_id' => 42],
    ]);

    expect($result->get('payload'))->toHaveKey('contact')
        ->and(data_get($result, 'payload.contact.id'))->toBe(7)
        ->and(data_get($result, 'payload.contact_inbox.source_id'))->toBe('src-abc');

    Http::assertSent(function (Request $request): bool {
        return $request->method() === 'POST'
            && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/contacts'
            && $request->hasHeader('api_access_token', 'test-token')
            && $request['name'] === 'Petro'
            && $request['inbox_id'] === 1
            && data_get($request->data(), 'custom_attributes.client_id') === 42;
    });
});

it('creates a contact inbox and exposes the source_id', function (): void {
    Http::fake([
        '*' => Http::response(['source_id' => 'src-xyz', 'inbox' => ['id' => 1]], 200),
    ]);

    $result = ChatwootApi::contacts()->createInbox(7, 1);

    expect($result->get('source_id'))->toBe('src-xyz');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/contacts/7/contact_inboxes'
        && $request['inbox_id'] === 1);
});

it('updates a contact via PUT', function (): void {
    Http::fake(['*' => Http::response(['payload' => ['id' => 7]], 200)]);

    ChatwootApi::contacts()->update(7, ['custom_attributes' => ['balance_uah' => 1500]]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PUT'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/contacts/7');
});
