<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Webhooks (account-level event subscriptions).
 *
 * @see https://developers.chatwoot.com/api-reference/webhooks
 */
final class Webhooks extends BaseModel
{
    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function list(): Collection
    {
        return $this->httpGet($this->accountPath('webhooks'));
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['url'=>…, 'subscriptions'=>['conversation_created', 'message_created']]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): Collection
    {
        return $this->httpPost($this->accountPath('webhooks'), $attributes);
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $webhookId, array $attributes): Collection
    {
        return $this->httpPatch($this->accountPath("webhooks/{$webhookId}"), $attributes);
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function delete(int $webhookId): Collection
    {
        return $this->httpDelete($this->accountPath("webhooks/{$webhookId}"));
    }
}
