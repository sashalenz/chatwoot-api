<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
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
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function list(): Collection
    {
        return $this->httpGet($this->accountPath('inboxes'));
    }

    /**
     * Fetch a single inbox.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function get(int $inboxId): Collection
    {
        return $this->httpGet($this->accountPath("inboxes/{$inboxId}"));
    }

    /**
     * Create an inbox.
     *
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'channel'=>['type'=>'api', 'webhook_url'=>…]]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): Collection
    {
        return $this->httpPost($this->accountPath('inboxes'), $attributes);
    }

    /**
     * Update an inbox.
     *
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $inboxId, array $attributes): Collection
    {
        return $this->httpPatch($this->accountPath("inboxes/{$inboxId}"), $attributes);
    }

    /**
     * Show the agent-bot currently assigned to the inbox.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function agentBot(int $inboxId): Collection
    {
        return $this->httpGet($this->accountPath("inboxes/{$inboxId}/agent_bot"));
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
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function members(int $inboxId): Collection
    {
        return $this->httpGet($this->accountPath("inbox_members/{$inboxId}"));
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
     * Remove agents from the inbox.
     *
     * @param  array<int,int>  $userIds
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function removeMembers(int $inboxId, array $userIds): Collection
    {
        return $this->httpDelete($this->accountPath('inbox_members'), [
            'inbox_id' => $inboxId,
            'user_ids' => $userIds,
        ]);
    }
}
