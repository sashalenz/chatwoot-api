# Chatwoot API for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sashalenz/chatwoot-api.svg?style=flat-square)](https://packagist.org/packages/sashalenz/chatwoot-api)
[![Total Downloads](https://img.shields.io/packagist/dt/sashalenz/chatwoot-api.svg?style=flat-square)](https://packagist.org/packages/sashalenz/chatwoot-api)
[![License](https://img.shields.io/packagist/l/sashalenz/chatwoot-api.svg?style=flat-square)](LICENSE.md)

A thin, fluent Laravel SDK for the [Chatwoot](https://www.chatwoot.com/) **Application API**.
It wraps contacts, conversations and messages behind a small, expressive API so you can
integrate Chatwoot into any Laravel application without hand-rolling HTTP calls.

> **Authentication.** Chatwoot's Application API authenticates with the `api_access_token`
> header — a user/agent token **or** an agent-bot token — **not** `Authorization: Bearer`.
> This package handles that for you.

## Requirements

- PHP `^8.2`
- Laravel `11.x`, `12.x` or `13.x`
- A Chatwoot installation (cloud or self-hosted) and an Application API access token

## Installation

Install the package via Composer:

```bash
composer require sashalenz/chatwoot-api
```

Optionally publish the config file:

```bash
php artisan vendor:publish --tag="chatwoot-api-config"
```

## Configuration

Set the following variables in your `.env`:

```dotenv
CHATWOOT_BASE_URL=https://app.chatwoot.com

# Application API (agent/system side)
CHATWOOT_ACCOUNT_ID=1
CHATWOOT_API_TOKEN=your-api-access-token

# Client API (API-channel inbound bridge)
CHATWOOT_INBOX_IDENTIFIER=your-inbox-identifier
CHATWOOT_HMAC_KEY=your-hmac-key   # optional, only if the inbox enables identity validation
```

| Key | Env | Description |
|-----|-----|-------------|
| `base_url` | `CHATWOOT_BASE_URL` | Base URL of your Chatwoot installation. Do **not** include the `/api/v1` prefix — the transport adds it. |
| `account_id` | `CHATWOOT_ACCOUNT_ID` | Default account id for the **Application API** path. Can be overridden per call. |
| `token` | `CHATWOOT_API_TOKEN` | **Application API** access token sent in the `api_access_token` header. Can be overridden per call. |
| `identifier` | `CHATWOOT_INBOX_IDENTIFIER` | **Client API** inbox identifier (from the API-channel inbox settings). Auth for the public bridge surface — no agent token needed. |
| `hmac_key` | `CHATWOOT_HMAC_KEY` | Optional HMAC key for Client API identity validation. |

### Two API families

- **Application API** (`contacts()`, `conversations()`, `messages()`) — the agent/system side. Authenticates with an agent/user **access token** and acts within an account.
- **Client API** (`client()`) — the public **API-channel** surface. Authenticates with the **inbox identifier** (no agent token) and is the canonical path for an integration that pushes a customer's messages *into* Chatwoot as `incoming`.

## Usage

The package exposes a static entrypoint, `ChatwootApi`, with one method per resource.
Every call returns an `Illuminate\Support\Collection` of the decoded JSON response.

```php
use Sashalenz\ChatwootApi\ChatwootApi;

// Create a contact. When `inbox_id` is supplied, the payload also carries
// a contact_inbox.source_id — the stable contact ↔ inbox key.
$contact = ChatwootApi::contacts()->create([
    'name' => 'Jane Doe',
    'phone_number' => '+15551234567',
    'inbox_id' => 1,
    'custom_attributes' => ['plan' => 'pro'],
]);

$sourceId = data_get($contact, 'payload.contact_inbox.source_id');

// Open a conversation for that contact-inbox.
$conversation = ChatwootApi::conversations()->create($sourceId, inboxId: 1);

// Post a message into the conversation.
ChatwootApi::messages()->create(
    conversationId: (int) $conversation->get('id'),
    content: 'Hello there!',
    messageType: 'incoming',
);

// Hand the conversation off to a human agent.
ChatwootApi::conversations()->toggleStatus((int) $conversation->get('id'), 'open');
```

### Client API (API-channel inbound bridge)

Push a customer's messages into an API-channel inbox using only the inbox
identifier — no agent token. Messages are created as `incoming`.

```php
use Sashalenz\ChatwootApi\ChatwootApi;

// 1) Upsert the contact (pass `identifier` to keep it stable across sessions).
$contact = ChatwootApi::client()->createContact([
    'identifier' => 'viber:01234567890A=',
    'name' => 'Petro',
    'custom_attributes' => ['client_id' => 42],
]);
$sourceId = $contact->get('source_id');

// 2) Open a conversation, 3) push the incoming message.
$conversation = ChatwootApi::client()->createConversation($sourceId);
ChatwootApi::client()->createMessage($sourceId, (int) $conversation->get('id'), 'Привіт');

// Per-call inbox override + identity hash (when the inbox enables validation):
ChatwootApi::client('other-inbox')->createContact([
    'identifier' => 'viber:xyz',
    'identifier_hash' => ChatwootApi::client()->identifierHash('viber:xyz'),
]);
```

Agent replies flow back to you via the inbox **Webhook URL** (a `message_created`
event with `message_type: outgoing`) — handle that in your app and deliver to the
transport.

### Per-call account & token overrides

Useful for multi-account or multi-token setups. Any override wins over the config value:

```php
ChatwootApi::messages()
    ->accountId(2)
    ->token($otherToken)
    ->create($conversationId, 'Hi from account 2', 'outgoing');
```

## API surface

| Resource | Method | Description |
|----------|--------|-------------|
| `contacts()` | `create(array $attributes)` | Create a contact. |
| | `update(int $contactId, array $attributes)` | Update a contact. |
| | `createInbox(int $contactId, int $inboxId, ?string $sourceId = null)` | Associate an existing contact with an inbox and get a `source_id`. |
| `conversations()` | `create(string $sourceId, int $inboxId, array $extra = [])` | Create a conversation for a contact-inbox. |
| | `show(int $conversationId)` | Fetch a single conversation. |
| | `toggleStatus(int $conversationId, string $status)` | Set status: `open`, `pending`, `resolved` or `snoozed`. |
| `messages()` | `create(int $conversationId, string $content, string $messageType = 'incoming', array $extra = [])` | Post a message (`incoming` or `outgoing`). |
| `client()` | `inbox()` | Read inbox info (health check). |
| | `createContact(array $attributes)` | Upsert a contact → returns `source_id`. |
| | `updateContact(string $sourceId, array $attributes)` | Update a contact (name / custom attributes). |
| | `createConversation(string $sourceId, array $extra = [])` | Open a conversation for the contact. |
| | `createMessage(string $sourceId, int $conversationId, string $content, array $extra = [])` | Push an `incoming` message. |
| | `identifierHash(string $contactIdentifier)` | HMAC hash for identity validation. |

See the [Chatwoot Application API reference](https://developers.chatwoot.com/api-reference)
for the full set of accepted attributes.

## Error handling

Non-2xx responses and transport failures are wrapped in
`Sashalenz\ChatwootApi\Exceptions\ChatwootApiException`. Requests automatically
retry transient failures (2 retries, 200 ms apart) with a 15 s timeout.

```php
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

try {
    ChatwootApi::contacts()->create(['name' => 'Jane']);
} catch (ChatwootApiException $e) {
    report($e);
}
```

## Testing

```bash
composer test          # run the test suite (Pest)
composer test-coverage # run with coverage
composer analyse       # static analysis (PHPStan / Larastan)
composer format        # apply code style (Laravel Pint)
composer format-test   # check code style without writing
```

## Changelog

Please see the commit history for changes.

## Contributing

Pull requests are welcome. Please make sure `composer test`, `composer analyse`
and `composer format-test` pass before submitting.

## Security

If you discover a security issue, please email sashalenz@gmail.com instead of
using the issue tracker.

## Credits

- [Oleksandr Petrovskyi](https://github.com/sashalenz)

## License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.
