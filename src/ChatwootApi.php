<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi;

use Sashalenz\ChatwootApi\ApiModels\Contacts;
use Sashalenz\ChatwootApi\ApiModels\Conversations;
use Sashalenz\ChatwootApi\ApiModels\Messages;

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
}
