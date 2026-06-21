<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Sashalenz\ChatwootApi\Data\Paginated;
use Sashalenz\ChatwootApi\Data\WebhookData;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Webhooks (account-level event subscriptions).
 *
 * @see https://developers.chatwoot.com/api-reference/webhooks
 */
final class Webhooks extends BaseModel
{
    /**
     * @return Paginated<WebhookData>
     *
     * @throws ChatwootApiException
     */
    public function list(): Paginated
    {
        return Paginated::fromResponse($this->httpGet($this->accountPath('webhooks'))->all(), WebhookData::class);
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['url'=>…, 'subscriptions'=>['conversation_created', 'message_created']]
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): WebhookData
    {
        $resp = $this->httpPost($this->accountPath('webhooks'), $attributes)->all();

        return WebhookData::from($resp['payload']['webhook'] ?? $resp['payload'] ?? $resp);
    }

    /**
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function update(int $webhookId, array $attributes): WebhookData
    {
        $resp = $this->httpPatch($this->accountPath("webhooks/{$webhookId}"), $attributes)->all();

        return WebhookData::from($resp['payload']['webhook'] ?? $resp['payload'] ?? $resp);
    }

    /**
     * Delete a webhook. Returns true on success.
     *
     * @throws ChatwootApiException
     */
    public function delete(int $webhookId): bool
    {
        $this->httpDelete($this->accountPath("webhooks/{$webhookId}"));

        return true;
    }
}
