<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Sashalenz\ChatwootApi\Data\AgentData;
use Sashalenz\ChatwootApi\Data\Paginated;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Agents (account users with an agent/administrator role).
 *
 * @see https://developers.chatwoot.com/api-reference/agents
 */
final class Agents extends BaseModel
{
    /**
     * @return Paginated<AgentData>
     *
     * @throws ChatwootApiException
     */
    public function list(): Paginated
    {
        return Paginated::fromResponse($this->httpGet($this->accountPath('agents'))->all(), AgentData::class);
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['user_id'=>…, 'role'=>'agent|administrator', 'availability'=>…]
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): AgentData
    {
        return AgentData::from($this->httpPost($this->accountPath('agents'), $attributes)->all());
    }

    /**
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function update(int $agentId, array $attributes): AgentData
    {
        return AgentData::from($this->httpPatch($this->accountPath("agents/{$agentId}"), $attributes)->all());
    }

    /**
     * Remove an agent from the account. Returns true on success.
     *
     * @throws ChatwootApiException
     */
    public function delete(int $agentId): bool
    {
        $this->httpDelete($this->accountPath("agents/{$agentId}"));

        return true;
    }
}
