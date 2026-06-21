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

    /**
     * Toggle conversation priority. Valid values: `urgent`, `high`, `medium`,
     * `low`, or `none` (clears the priority).
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function togglePriority(int $conversationId, string $priority): Collection
    {
        return $this->httpPost(
            $this->accountPath("conversations/{$conversationId}/toggle_priority"),
            ['priority' => $priority],
        );
    }

    /**
     * Assign the conversation to an agent and/or a team. Pass `null` for either
     * to unassign.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function assign(int $conversationId, ?int $assigneeId = null, ?int $teamId = null): Collection
    {
        return $this->httpPost(
            $this->accountPath("conversations/{$conversationId}/assignments"),
            array_filter([
                'assignee_id' => $assigneeId,
                'team_id' => $teamId,
            ], static fn ($v): bool => $v !== null),
        );
    }

    /**
     * Set conversation-level custom attributes.
     *
     * @param  array<string,mixed>  $customAttributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function setCustomAttributes(int $conversationId, array $customAttributes): Collection
    {
        return $this->httpPost(
            $this->accountPath("conversations/{$conversationId}/custom_attributes"),
            ['custom_attributes' => $customAttributes],
        );
    }

    /**
     * List labels on the conversation.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function labels(int $conversationId): Collection
    {
        return $this->httpGet($this->accountPath("conversations/{$conversationId}/labels"));
    }

    /**
     * Replace the conversation's labels with the given set.
     *
     * @param  array<int,string>  $labels
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function addLabels(int $conversationId, array $labels): Collection
    {
        return $this->httpPost(
            $this->accountPath("conversations/{$conversationId}/labels"),
            ['labels' => $labels],
        );
    }

    /**
     * Toggle the agent typing indicator — `on` / `off`.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function toggleTyping(int $conversationId, string $typingStatus): Collection
    {
        return $this->httpPost(
            $this->accountPath("conversations/{$conversationId}/toggle_typing_status"),
            ['typing_status' => $typingStatus],
        );
    }

    /**
     * Conversation counts (mine / unassigned / assigned / all).
     *
     * @param  array<string,mixed>  $query  optional: ['status'=>…, 'inbox_id'=>…, 'team_id'=>…, 'labels'=>[…], 'q'=>…]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function meta(array $query = []): Collection
    {
        return $this->httpGet($this->accountPath('conversations/meta'), $query);
    }

    /**
     * Advanced conversation filtering (query-builder payload).
     *
     * @param  array<string,mixed>  $payload
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function filter(array $payload): Collection
    {
        return $this->httpPost($this->accountPath('conversations/filter'), $payload);
    }
}
