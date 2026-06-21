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

# Platform API (installation provisioning) — only if you use platform* resources
CHATWOOT_PLATFORM_TOKEN=your-platform-app-token
```

| Key | Env | Description |
|-----|-----|-------------|
| `base_url` | `CHATWOOT_BASE_URL` | Base URL of your Chatwoot installation. Do **not** include the `/api/v1` prefix — the transport adds it. |
| `account_id` | `CHATWOOT_ACCOUNT_ID` | Default account id for the **Application API** path. Can be overridden per call. |
| `token` | `CHATWOOT_API_TOKEN` | **Application API** access token sent in the `api_access_token` header. Can be overridden per call. |
| `identifier` | `CHATWOOT_INBOX_IDENTIFIER` | **Client API** inbox identifier (from the API-channel inbox settings). Auth for the public bridge surface — no agent token needed. |
| `hmac_key` | `CHATWOOT_HMAC_KEY` | Optional HMAC key for Client API identity validation. |
| `platform_token` | `CHATWOOT_PLATFORM_TOKEN` | **Platform API** app token. Sent in the same `api_access_token` header, but a different token than the Application one. Can be overridden per call. |

### Three API families

- **Application API** (`contacts()`, `conversations()`, `messages()`, …) — the agent/system side. Authenticates with an agent/user **access token** and acts within an account.
- **Client API** (`client()`) — the public **API-channel** surface. Authenticates with the **inbox identifier** (no agent token) and is the canonical path for an integration that pushes a customer's messages *into* Chatwoot as `incoming`.
- **Platform API** (`platformAccounts()`, `platformAgentBots()`, `platformUsers()`) — installation-level provisioning of accounts, users and bots. Authenticates with the **platform app token** and is not account-scoped.

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
| | `list(array $query = [])` | List contacts. |
| | `get(int $contactId)` | Fetch a single contact. |
| | `update(int $contactId, array $attributes)` | Update a contact. |
| | `delete(int $contactId)` | Delete a contact. |
| | `search(string $q, array $query = [])` | Free-text search (name/email/phone/identifier). |
| | `filter(array $payload)` | Advanced contact filtering (query-builder payload). |
| | `createInbox(int $contactId, int $inboxId, ?string $sourceId = null)` | Associate an existing contact with an inbox and get a `source_id`. |
| | `contactableInboxes(int $contactId)` | Inboxes the contact can be reached on. |
| | `conversations(int $contactId)` | List the contact's conversations. |
| | `labels(int $contactId)` / `addLabels(int $contactId, array $labels)` | List / set the contact's labels. |
| `conversations()` | `create(string $sourceId, int $inboxId, array $extra = [])` | Create a conversation for a contact-inbox. |
| | `list(array $filters = [])` | List conversations (filters: `status`, `assignee_type`, `inbox_id`, `team_id`, `labels`, `q`, `page`). |
| | `show(int $conversationId)` | Fetch a single conversation. |
| | `update(int $conversationId, array $attributes)` | Update a conversation (`priority`, `additional_attributes`, …). |
| | `meta(array $query = [])` | Conversation counts (mine / unassigned / assigned / all). |
| | `filter(array $payload)` | Advanced conversation filtering (query-builder payload). |
| | `toggleStatus(int $conversationId, string $status)` | Set status: `open`, `pending`, `resolved` or `snoozed`. |
| | `togglePriority(int $conversationId, string $priority)` | Set priority: `urgent`/`high`/`medium`/`low`/`none`. |
| | `assign(int $conversationId, ?int $assigneeId, ?int $teamId)` | Assign to an agent and/or team. |
| | `setCustomAttributes(int $conversationId, array $customAttributes)` | Set conversation custom attributes. |
| | `labels(int $conversationId)` / `addLabels(int $conversationId, array $labels)` | List / set conversation labels. |
| | `toggleTyping(int $conversationId, string $typingStatus)` | Toggle agent typing indicator (`on`/`off`). |
| `inboxes()` | `list()` / `get(int $inboxId)` | List inboxes / fetch one. |
| | `create(array $attributes)` / `update(int $inboxId, array $attributes)` | Create / update an inbox. |
| | `agentBot(int $inboxId)` / `setAgentBot(int $inboxId, ?int $agentBotId)` | Show / assign (or detach) the inbox agent-bot. |
| | `members(int $inboxId)` | List inbox agent members. |
| | `addMembers` / `updateMembers` / `removeMembers (int $inboxId, array $userIds)` | Manage inbox agent members. |
| `messages()` | `create(int $conversationId, string $content, string $messageType = 'incoming', array $extra = [])` | Post a message (`incoming` or `outgoing`). |
| | `list(int $conversationId, array $query = [])` | List messages of a conversation. |
| | `delete(int $conversationId, int $messageId)` | Delete a message. |
| `agents()` | `list` / `create` / `update` / `delete` | Manage account agents. |
| `agentBots()` | `list` / `get` / `create` / `update` / `delete` | Manage agent bots. |
| `teams()` | `list` / `get` / `create` / `update` / `delete` | Manage teams. |
| | `members` / `addMembers` / `updateMembers` / `removeMembers` | Manage team agents. |
| `labels()` | `list` / `get` / `create` / `update` / `delete` | Manage the label catalogue. |
| `cannedResponses()` | `list` / `create` / `update` / `delete` | Manage canned responses. |
| `customAttributeDefinitions()` | `list` / `get` / `create` / `update` / `delete` | Manage custom attribute definitions. |
| `customFilters()` | `list` / `get` / `create` / `update` / `delete` | Manage saved custom filters. |
| `account()` | `get()` / `update(array $attributes)` | Read / update the current account. |
| `profile()` | `get()` / `update(array $profile)` | Read / update the token owner's profile. |
| `automationRules()` | `list` / `get` / `create` / `update` / `delete` | Manage automation rules. |
| `webhooks()` | `list` / `create` / `update` / `delete` | Manage account webhooks. |
| `integrations()` | `apps()` / `createHook` / `updateHook` / `deleteHook` | List integration apps and manage hooks. |
| `reports()` | `account` / `summary` / `conversations` / `firstResponseTimeDistribution` / `inboxLabelMatrix` / `outgoingMessagesCount` | Metrics & reports (v2 endpoints). |
| `helpCenter()` | `listPortals` / `createPortal` / `updatePortal` / `createCategory` / `createArticle` | Manage Help Center portals, categories & articles. |
| `platformAccounts()` | `create` / `get` / `update` / `delete` / `users` / `createUser` / `deleteUser` | **Platform API** — provision accounts and account-user links. |
| `platformAgentBots()` | `list` / `get` / `create` / `update` / `delete` | **Platform API** — installation-wide agent bots. |
| `platformUsers()` | `create` / `get` / `update` / `delete` / `login` | **Platform API** — provision users; `login` returns an SSO link. |
| `client()` | `inbox()` | Read inbox info (health check). |
| | `createContact(array $attributes)` | Upsert a contact → returns `source_id`. |
| | `getContact(string $sourceId)` | Fetch a contact by `source_id`. |
| | `updateContact(string $sourceId, array $attributes)` | Update a contact (name / custom attributes). |
| | `createConversation(string $sourceId, array $extra = [])` | Open a conversation for the contact. |
| | `listConversations(string $sourceId)` | List the contact's conversations. |
| | `getConversation(string $sourceId, int $conversationId)` | Fetch one conversation of the contact. |
| | `createMessage(string $sourceId, int $conversationId, string $content, array $extra = [])` | Push an `incoming` message. |
| | `createMessageWithAttachments(string $sourceId, int $conversationId, ?string $content, array $attachments)` | Push an `incoming` message with file attachments (multipart). |
| | `listMessages(string $sourceId, int $conversationId)` | List the messages of a conversation. |
| | `updateMessage(string $sourceId, int $conversationId, int $messageId, array $attributes)` | Update a message (e.g. CSAT response). |
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
