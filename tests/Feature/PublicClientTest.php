<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\ChatwootApi\ChatwootApi;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

it('reads inbox info via the Client API (no auth header)', function (): void {
    Http::fake(['*' => Http::response(['identifier' => 'inbox-ident', 'name' => 'A20 Viber'], 200)]);

    $result = ChatwootApi::client()->inbox();

    expect($result->get('name'))->toBe('A20 Viber');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/public/api/v1/inboxes/inbox-ident'
        && ! $request->hasHeader('api_access_token'));
});

it('creates a contact and exposes the source_id', function (): void {
    Http::fake(['*' => Http::response(['source_id' => 'src-1', 'pubsub_token' => 'pt'], 200)]);

    $result = ChatwootApi::client()->createContact([
        'identifier' => 'viber:abc',
        'name' => 'Petro',
        'custom_attributes' => ['client_id' => 42],
    ]);

    expect($result->get('source_id'))->toBe('src-1');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/public/api/v1/inboxes/inbox-ident/contacts'
        && $request['identifier'] === 'viber:abc'
        && data_get($request->data(), 'custom_attributes.client_id') === 42);
});

it('opens a conversation for a source_id', function (): void {
    Http::fake(['*' => Http::response(['id' => 9], 200)]);

    $result = ChatwootApi::client()->createConversation('src-1');

    expect($result->get('id'))->toBe(9);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/public/api/v1/inboxes/inbox-ident/contacts/src-1/conversations');
});

it('pushes an incoming message into a conversation', function (): void {
    Http::fake(['*' => Http::response(['id' => 77], 200)]);

    ChatwootApi::client()->createMessage('src-1', 9, 'Привіт');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://chatwoot.test/public/api/v1/inboxes/inbox-ident/contacts/src-1/conversations/9/messages'
        && $request['content'] === 'Привіт');
});

it('pushes an incoming message with a file attachment as multipart', function (): void {
    Http::fake(['*' => Http::response(['id' => 88], 200)]);

    ChatwootApi::client()->createMessageWithAttachments('src-1', 9, 'фото клієнта', [
        ['contents' => 'RAWIMAGEBYTES', 'filename' => 'photo.jpg'],
    ]);

    Http::assertSent(function (Request $request): bool {
        $body = (string) $request->body();

        return $request->method() === 'POST'
            && $request->url() === 'https://chatwoot.test/public/api/v1/inboxes/inbox-ident/contacts/src-1/conversations/9/messages'
            && $request->isMultipart()
            && str_contains($body, 'name="attachments[]"')
            && str_contains($body, 'filename="photo.jpg"')
            && str_contains($body, 'RAWIMAGEBYTES')
            && str_contains($body, 'фото клієнта');
    });
});

it('sends an attachment with no caption (content omitted)', function (): void {
    Http::fake(['*' => Http::response(['id' => 89], 200)]);

    ChatwootApi::client()->createMessageWithAttachments('src-1', 9, null, [
        ['contents' => 'BYTES', 'filename' => 'doc.pdf'],
    ]);

    Http::assertSent(function (Request $request): bool {
        $body = (string) $request->body();

        return $request->isMultipart()
            && str_contains($body, 'filename="doc.pdf"')
            && ! str_contains($body, 'name="content"');
    });
});

it('fetches a contact by source_id', function (): void {
    Http::fake(['*' => Http::response(['source_id' => 'src-1', 'name' => 'Petro'], 200)]);

    $result = ChatwootApi::client()->getContact('src-1');

    expect($result->get('name'))->toBe('Petro');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/public/api/v1/inboxes/inbox-ident/contacts/src-1');
});

it('lists the conversations of a contact', function (): void {
    Http::fake(['*' => Http::response([['id' => 9]], 200)]);

    ChatwootApi::client()->listConversations('src-1');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/public/api/v1/inboxes/inbox-ident/contacts/src-1/conversations');
});

it('fetches a single conversation of a contact', function (): void {
    Http::fake(['*' => Http::response(['id' => 9], 200)]);

    ChatwootApi::client()->getConversation('src-1', 9);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/public/api/v1/inboxes/inbox-ident/contacts/src-1/conversations/9');
});

it('lists the messages of a conversation', function (): void {
    Http::fake(['*' => Http::response([['id' => 77]], 200)]);

    ChatwootApi::client()->listMessages('src-1', 9);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://chatwoot.test/public/api/v1/inboxes/inbox-ident/contacts/src-1/conversations/9/messages');
});

it('updates a message (CSAT response)', function (): void {
    Http::fake(['*' => Http::response(['id' => 77], 200)]);

    ChatwootApi::client()->updateMessage('src-1', 9, 77, [
        'submitted_values' => ['csat_survey_response' => ['rating' => 5]],
    ]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/public/api/v1/inboxes/inbox-ident/contacts/src-1/conversations/9/messages/77'
        && data_get($request->data(), 'submitted_values.csat_survey_response.rating') === 5);
});

it('updates a contact via PATCH', function (): void {
    Http::fake(['*' => Http::response(['source_id' => 'src-1'], 200)]);

    ChatwootApi::client()->updateContact('src-1', ['custom_attributes' => ['balance_uah' => 1500]]);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://chatwoot.test/public/api/v1/inboxes/inbox-ident/contacts/src-1');
});

it('computes an identifier hash from the configured hmac key', function (): void {
    $hash = ChatwootApi::client()->identifierHash('viber:abc');

    expect($hash)->toBe(hash_hmac('sha256', 'viber:abc', 'hmac-secret'));
});

it('throws when the inbox identifier is not configured', function (): void {
    config()->set('chatwoot-api.identifier', null);

    ChatwootApi::client()->createContact(['name' => 'x']);
})->throws(ChatwootApiException::class, 'identifier is not configured');

it('lets a per-call identifier override config', function (): void {
    Http::fake(['*' => Http::response(['source_id' => 's'], 200)]);

    ChatwootApi::client('other-inbox')->createContact(['name' => 'x']);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://chatwoot.test/public/api/v1/inboxes/other-inbox/contacts');
});
