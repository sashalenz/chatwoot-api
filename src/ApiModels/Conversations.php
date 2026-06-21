<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Conversations.
 *
 * @see https://developers.chatwoot.com/api-reference/conversations-api
 */
final class Conversations extends BaseModel
{
    /**
     * Create a conversation for a contact-inbox `source_id`.
     *
     * @param  array<string,mixed>  $extra  optional: ['contact_id'=>…, 'status'=>'open', 'additional_attributes'=>[…]]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(string $sourceId, int $inboxId, array $extra = []): Collection
    {
        return $this->post($this->accountPath('conversations'), [
            'source_id' => $sourceId,
            'inbox_id' => $inboxId,
            ...$extra,
        ]);
    }

    /**
     * Fetch a single conversation (id, status, meta).
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function show(int $conversationId): Collection
    {
        return $this->get($this->accountPath("conversations/{$conversationId}"));
    }

    /**
     * Toggle conversation status — `open` (hand off to human) / `pending`
     * (back to bot) / `resolved` / `snoozed`. Auto-reopens on customer reply.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function toggleStatus(int $conversationId, string $status): Collection
    {
        return $this->post(
            $this->accountPath("conversations/{$conversationId}/toggle_status"),
            ['status' => $status],
        );
    }
}
