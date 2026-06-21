<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Data\ConversationData;
use Sashalenz\ChatwootApi\Data\Paginated;
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
     *
     * @throws ChatwootApiException
     */
    public function create(string $sourceId, int $inboxId, array $extra = []): ConversationData
    {
        $resp = $this->httpPost($this->accountPath('conversations'), [
            'source_id' => $sourceId,
            'inbox_id' => $inboxId,
            ...$extra,
        ])->all();

        return ConversationData::from($resp);
    }

    /**
     * List conversations in the account.
     *
     * @param  array<string,mixed>  $filters  optional query: ['assignee_type'=>'me|unassigned|assigned|all', 'status'=>'open|resolved|pending|snoozed|all', 'inbox_id'=>…, 'team_id'=>…, 'labels'=>[…], 'q'=>…, 'page'=>1]
     * @return Paginated<ConversationData>
     *
     * @throws ChatwootApiException
     */
    public function list(array $filters = []): Paginated
    {
        /** @var array<string,mixed> $data */
        $data = $this->httpGet($this->accountPath('conversations'), $filters)->get('data', []);

        return Paginated::fromResponse($data, ConversationData::class);
    }

    /**
     * Fetch a single conversation (id, status, meta, embedded messages).
     *
     * @throws ChatwootApiException
     */
    public function show(int $conversationId): ConversationData
    {
        return ConversationData::from(
            $this->httpGet($this->accountPath("conversations/{$conversationId}"))->all(),
        );
    }

    /**
     * Update a conversation (e.g. `priority`, `additional_attributes`,
     * `snoozed_until`).
     *
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function update(int $conversationId, array $attributes): ConversationData
    {
        return ConversationData::from(
            $this->httpPatch($this->accountPath("conversations/{$conversationId}"), $attributes)->all(),
        );
    }

    /**
     * Advanced conversation filtering (query-builder payload).
     *
     * @param  array<string,mixed>  $payload
     * @return Paginated<ConversationData>
     *
     * @throws ChatwootApiException
     */
    public function filter(array $payload): Paginated
    {
        /** @var array<string,mixed> $data */
        $data = $this->httpPost($this->accountPath('conversations/filter'), $payload)->get('data', []);

        return Paginated::fromResponse($data, ConversationData::class);
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
     * @return array<int,string>
     *
     * @throws ChatwootApiException
     */
    public function labels(int $conversationId): array
    {
        /** @var array<int,string> $labels */
        $labels = $this->httpGet($this->accountPath("conversations/{$conversationId}/labels"))->get('payload', []);

        return $labels;
    }

    /**
     * Replace the conversation's labels with the given set; returns the new set.
     *
     * @param  array<int,string>  $labels
     * @return array<int,string>
     *
     * @throws ChatwootApiException
     */
    public function addLabels(int $conversationId, array $labels): array
    {
        /** @var array<int,string> $result */
        $result = $this->httpPost(
            $this->accountPath("conversations/{$conversationId}/labels"),
            ['labels' => $labels],
        )->get('payload', []);

        return $result;
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
}
