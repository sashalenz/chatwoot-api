<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi;

use Sashalenz\ChatwootApi\ApiModels\Contacts;
use Sashalenz\ChatwootApi\ApiModels\Conversations;
use Sashalenz\ChatwootApi\ApiModels\Inboxes;
use Sashalenz\ChatwootApi\ApiModels\Messages;
use Sashalenz\ChatwootApi\ApiModels\PublicClient;

/**
 * Static entrypoint, mirroring `MonobankApi`/`ViberBotApi`.
 *
 *   ChatwootApi::contacts()->create(['name' => 'Petro', 'inbox_id' => 1]);
 *   ChatwootApi::conversations()->create($sourceId, $inboxId);
 *   ChatwootApi::messages()->create($conversationId, 'Hello', 'incoming');
 *
 * Multi-account / multi-token consumers override per call:
 *   ChatwootApi::messages()->accountId(2)->token($t)->create(…);
 */
class ChatwootApi
{
    public static function contacts(): Contacts
    {
        return new Contacts;
    }

    public static function conversations(): Conversations
    {
        return new Conversations;
    }

    public static function messages(): Messages
    {
        return new Messages;
    }

    public static function inboxes(): Inboxes
    {
        return new Inboxes;
    }

    /**
     * Client API (API-channel) — the inbound bridge surface. Auth is the inbox
     * identifier (config `chatwoot-api.identifier`), no agent token.
     */
    public static function client(?string $identifier = null): PublicClient
    {
        return new PublicClient($identifier);
    }
}
