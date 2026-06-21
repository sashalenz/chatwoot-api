<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels\Platform;

use Sashalenz\ChatwootApi\Data\AgentBotData;
use Sashalenz\ChatwootApi\Data\Paginated;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Platform API — Agent Bots (installation-wide bot definitions).
 *
 * @see https://developers.chatwoot.com/api-reference/agent-bots
 */
final class PlatformAgentBots extends PlatformModel
{
    /**
     * @return Paginated<AgentBotData>
     *
     * @throws ChatwootApiException
     */
    public function list(): Paginated
    {
        return Paginated::fromResponse($this->httpGet($this->platformPath('agent_bots'))->all(), AgentBotData::class);
    }

    /**
     * @throws ChatwootApiException
     */
    public function get(int $agentBotId): AgentBotData
    {
        return AgentBotData::from($this->httpGet($this->platformPath("agent_bots/{$agentBotId}"))->all());
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'outgoing_url'=>…, 'account_id'=>…]
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): AgentBotData
    {
        return AgentBotData::from($this->httpPost($this->platformPath('agent_bots'), $attributes)->all());
    }

    /**
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function update(int $agentBotId, array $attributes): AgentBotData
    {
        return AgentBotData::from($this->httpPatch($this->platformPath("agent_bots/{$agentBotId}"), $attributes)->all());
    }

    /**
     * Delete an agent bot. Returns true on success.
     *
     * @throws ChatwootApiException
     */
    public function delete(int $agentBotId): bool
    {
        $this->httpDelete($this->platformPath("agent_bots/{$agentBotId}"));

        return true;
    }
}
