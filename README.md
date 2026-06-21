# Chatwoot API (Laravel)

Thin Laravel SDK for the [Chatwoot](https://www.chatwoot.com/) **Application API** —
contacts, conversations and messages. Built for the A20 API-channel bridge
(Telegram/Viber/avto.pro ↔ Chatwoot). Mirrors the `sashalenz/*` package
conventions (`monobank-api`, `viber-bot-api`).

> Auth is the `api_access_token` header (a user/agent token **or** an agent-bot
> token), **not** `Authorization: Bearer`.

## Install

```bash
composer require sashalenz/chatwoot-api
```

```dotenv
CHATWOOT_BASE_URL=https://chatwoot.example.com
CHATWOOT_ACCOUNT_ID=1
CHATWOOT_API_TOKEN=xxxxxxxx
```

## Usage

```php
use Sashalenz\ChatwootApi\ChatwootApi;

// Inbound mirror (customer → Chatwoot)
$contact = ChatwootApi::contacts()->create([
    'name' => 'Petro',
    'phone_number' => '+380501234567',
    'inbox_id' => 1,
    'custom_attributes' => ['client_id' => 42, 'balance_uah' => 1500],
]);

$sourceId = data_get($contact, 'payload.contact_inbox.source_id');

$conversation = ChatwootApi::conversations()->create($sourceId, inboxId: 1);

ChatwootApi::messages()->create(
    conversationId: (int) $conversation->get('id'),
    content: 'Привіт',
    messageType: 'incoming',
);

// Handoff (bot → human)
ChatwootApi::conversations()->toggleStatus($conversationId, 'open');
```

Multi-account / multi-token override per call:

```php
ChatwootApi::messages()->accountId(2)->token($otherToken)->create(...);
```

## Surface (current)

| Resource | Methods |
|----------|---------|
| `contacts()` | `create`, `update`, `createInbox` |
| `conversations()` | `create`, `show`, `toggleStatus` |
| `messages()` | `create` |

Attachments (multipart), webhook DTOs and AgentBot helpers are planned as the
bridge grows beyond the inbound-only slice.

## Testing

```bash
composer test
composer analyse
composer format-test
```

## License

MIT.
