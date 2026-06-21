<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Data\AgentBotData;
use Sashalenz\ChatwootApi\Data\AgentData;
use Sashalenz\ChatwootApi\Data\InboxData;
use Sashalenz\ChatwootApi\Data\Paginated;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Inboxes (and their agent members / agent-bot binding).
 *
 * @see https://developers.chatwoot.com/api-reference/inboxes
 */
final class Inboxes extends BaseModel
{
    /**
     * List inboxes in the account.
     *
     * @return Paginated<InboxData>
     *
     * @throws ChatwootApiException
     */
    public function list(): Paginated
    {
        return Paginated::fromResponse($this->httpGet($this->accountPath('inboxes'))->all(), InboxData::class);
    }

    /**
     * Fetch a single inbox.
     *
     * @throws ChatwootApiException
     */
    public function get(int $inboxId): InboxData
    {
        return InboxData::from($this->httpGet($this->accountPath("inboxes/{$inboxId}"))->all());
    }

    /**
     * Create an inbox.
     *
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'channel'=>['type'=>'api', 'webhook_url'=>…]]
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): InboxData
    {
        return InboxData::from($this->httpPost($this->accountPath('inboxes'), $attributes)->all());
    }

    /**
     * Update an inbox.
     *
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function update(int $inboxId, array $attributes): InboxData
    {
        return InboxData::from($this->httpPatch($this->accountPath("inboxes/{$inboxId}"), $attributes)->all());
    }

    /**
     * Show the agent-bot currently assigned to the inbox.
     *
     * @throws ChatwootApiException
     */
    public function agentBot(int $inboxId): AgentBotData
    {
        return AgentBotData::from($this->httpGet($this->accountPath("inboxes/{$inboxId}/agent_bot"))->all());
    }

    /**
     * Assign (or, with `null`, detach) an agent-bot for the inbox.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function setAgentBot(int $inboxId, ?int $agentBotId): Collection
    {
        return $this->httpPost(
            $this->accountPath("inboxes/{$inboxId}/set_agent_bot"),
            ['agent_bot' => $agentBotId],
        );
    }

    /**
     * List the agents (members) assigned to the inbox.
     *
     * @return Paginated<AgentData>
     *
     * @throws ChatwootApiException
     */
    public function members(int $inboxId): Paginated
    {
        return Paginated::fromResponse($this->httpGet($this->accountPath("inbox_members/{$inboxId}"))->all(), AgentData::class);
    }

    /**
     * Add agents to the inbox.
     *
     * @param  array<int,int>  $userIds
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function addMembers(int $inboxId, array $userIds): Collection
    {
        return $this->httpPost($this->accountPath('inbox_members'), [
            'inbox_id' => $inboxId,
            'user_ids' => $userIds,
        ]);
    }

    /**
     * Replace the inbox's agent set with the given user ids.
     *
     * @param  array<int,int>  $userIds
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function updateMembers(int $inboxId, array $userIds): Collection
    {
        return $this->httpPatch($this->accountPath('inbox_members'), [
            'inbox_id' => $inboxId,
            'user_ids' => $userIds,
        ]);
    }

    /**
     * Remove agents from the inbox. Returns true on success.
     *
     * @param  array<int,int>  $userIds
     *
     * @throws ChatwootApiException
     */
    public function removeMembers(int $inboxId, array $userIds): bool
    {
        $this->httpDelete($this->accountPath('inbox_members'), [
            'inbox_id' => $inboxId,
            'user_ids' => $userIds,
        ]);

        return true;
    }
}
