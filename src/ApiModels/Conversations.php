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
        return $this->httpPost($this->accountPath('conversations'), [
            'source_id' => $sourceId,
            'inbox_id' => $inboxId,
            ...$extra,
        ]);
    }

    /**
     * List conversations in the account.
     *
     * @param  array<string,mixed>  $filters  optional query: ['assignee_type'=>'me|unassigned|assigned|all', 'status'=>'open|resolved|pending|snoozed|all', 'inbox_id'=>…, 'team_id'=>…, 'labels'=>[…], 'q'=>…, 'page'=>1]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function list(array $filters = []): Collection
    {
        return $this->httpGet($this->accountPath('conversations'), $filters);
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
        return $this->httpGet($this->accountPath("conversations/{$conversationId}"));
    }

    /**
     * Update a conversation (e.g. `priority`, `additional_attributes`,
     * `snoozed_until`).
     *
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $conversationId, array $attributes): Collection
    {
        return $this->httpPatch($this->accountPath("conversations/{$conversationId}"), $attributes);
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
        return $this->httpPost(
            $this->accountPath("conversations/{$conversationId}/toggle_status"),
            ['status' => $status],
        );
    }
}
