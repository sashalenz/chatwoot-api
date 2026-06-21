<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;
use Sashalenz\ChatwootApi\Data\ContactData;

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

    expect($result->id)->toBe(7)
        ->and($result->name)->toBe('Petro')
        ->and($result->sourceId)->toBe('src-abc');

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

    expect($result->sourceId)->toBe('src-xyz');

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

it('lists contacts as a paginated set of typed DTOs', function (): void {
    Http::fake(['*' => Http::response([
        'meta' => ['count' => 1, 'current_page' => 2],
        'payload' => [['id' => 7, 'name' => 'Petro', 'phone_number' => '+380501234567']],
    ], 200)]);

    $result = ChatwootApi::contacts()->list(['page' => 2]);

    expect($result->count())->toBe(1)
        ->and($result->currentPage())->toBe(2)
        ->and($result->totalCount())->toBe(1)
        ->and($result->payload[0])->toBeInstanceOf(ContactData::class)
        ->and($result->payload[0]->name)->toBe('Petro')
        ->and($result->payload[0]->phoneNumber)->toBe('+380501234567');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && str_starts_with($request->url(), 'https://chatwoot.test/api/v1/accounts/1/contacts?')
        && $request['page'] === 2);
});

it('fetches a single contact as a DTO', function (): void {
    Http::fake(['*' => Http::response(['payload' => ['id' => 7, 'name' => 'Petro']], 200)]);

    $result = ChatwootApi::contacts()->get(7);

    expect($result)->toBeInstanceOf(ContactData::class)
        ->and($result->id)->toBe(7)
        ->and($result->name)->toBe('Petro');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/contacts/7');
});

it('searches contacts by query', function (): void {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    ChatwootApi::contacts()->search('petro');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && str_starts_with($request->url(), 'https://chatwoot.test/api/v1/accounts/1/contacts/search?')
        && $request['q'] === 'petro');
});

it('filters contacts via POST', function (): void {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    ChatwootApi::contacts()->filter(['payload' => [['attribute_key' => 'email', 'filter_operator' => 'contains', 'values' => ['@a20']]]]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/contacts/filter');
});

it('deletes a contact and returns true', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    expect(ChatwootApi::contacts()->delete(7))->toBeTrue();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/contacts/7');
});

it('lists contactable inboxes', function (): void {
    Http::fake(['*' => Http::response([], 200)]);

    ChatwootApi::contacts()->contactableInboxes(7);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/contacts/7/contactable_inboxes');
});

it('lists a contact conversations', function (): void {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    ChatwootApi::contacts()->conversations(7);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/contacts/7/conversations');
});

it('adds labels to a contact', function (): void {
    Http::fake(['*' => Http::response(['payload' => ['vip']], 200)]);

    ChatwootApi::contacts()->addLabels(7, ['vip', 'wholesale']);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/contacts/7/labels'
        && $request['labels'] === ['vip', 'wholesale']);
});

it('lists labels of a contact', function (): void {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    ChatwootApi::contacts()->labels(7);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/api/v1/accounts/1/contacts/7/labels');
});
