<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Agents (account users with an agent/administrator role).
 *
 * @see https://developers.chatwoot.com/api-reference/agents
 */
final class Agents extends BaseModel
{
    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function list(): Collection
    {
        return $this->httpGet($this->accountPath('agents'));
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['user_id'=>…, 'role'=>'agent|administrator', 'availability'=>…]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): Collection
    {
        return $this->httpPost($this->accountPath('agents'), $attributes);
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $agentId, array $attributes): Collection
    {
        return $this->httpPatch($this->accountPath("agents/{$agentId}"), $attributes);
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function delete(int $agentId): Collection
    {
        return $this->httpDelete($this->accountPath("agents/{$agentId}"));
    }
}
