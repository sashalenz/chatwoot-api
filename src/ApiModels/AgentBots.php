<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Agent Bots (account-level bot definitions).
 *
 * @see https://developers.chatwoot.com/api-reference/agent-bots
 */
final class AgentBots extends BaseModel
{
    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function list(): Collection
    {
        return $this->httpGet($this->accountPath('agent_bots'));
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function get(int $agentBotId): Collection
    {
        return $this->httpGet($this->accountPath("agent_bots/{$agentBotId}"));
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'description'=>…, 'outgoing_url'=>…]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): Collection
    {
        return $this->httpPost($this->accountPath('agent_bots'), $attributes);
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $agentBotId, array $attributes): Collection
    {
        return $this->httpPatch($this->accountPath("agent_bots/{$agentBotId}"), $attributes);
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function delete(int $agentBotId): Collection
    {
        return $this->httpDelete($this->accountPath("agent_bots/{$agentBotId}"));
    }
}
