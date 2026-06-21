<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels\Platform;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Platform API — Agent Bots (installation-wide bot definitions).
 *
 * @see https://developers.chatwoot.com/api-reference/agent-bots
 */
final class PlatformAgentBots extends PlatformModel
{
    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function list(): Collection
    {
        return $this->httpGet($this->platformPath('agent_bots'));
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function get(int $agentBotId): Collection
    {
        return $this->httpGet($this->platformPath("agent_bots/{$agentBotId}"));
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'outgoing_url'=>…, 'account_id'=>…]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): Collection
    {
        return $this->httpPost($this->platformPath('agent_bots'), $attributes);
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $agentBotId, array $attributes): Collection
    {
        return $this->httpPatch($this->platformPath("agent_bots/{$agentBotId}"), $attributes);
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function delete(int $agentBotId): Collection
    {
        return $this->httpDelete($this->platformPath("agent_bots/{$agentBotId}"));
    }
}
