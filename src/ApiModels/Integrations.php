<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Sashalenz\ChatwootApi\Data\IntegrationAppData;
use Sashalenz\ChatwootApi\Data\IntegrationHookData;
use Sashalenz\ChatwootApi\Data\Paginated;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Integrations (available apps) and their configured hooks.
 *
 * @see https://developers.chatwoot.com/api-reference/integrations
 */
final class Integrations extends BaseModel
{
    /**
     * List the integration apps available in the account.
     *
     * @return Paginated<IntegrationAppData>
     *
     * @throws ChatwootApiException
     */
    public function apps(): Paginated
    {
        return Paginated::fromResponse($this->httpGet($this->accountPath('integrations/apps'))->all(), IntegrationAppData::class);
    }

    /**
     * Create an integration hook (connect an app, optionally to an inbox).
     *
     * @param  array<string,mixed>  $attributes  e.g. ['app_id'=>'dialogflow', 'inbox_id'=>1, 'settings'=>[…]]
     *
     * @throws ChatwootApiException
     */
    public function createHook(array $attributes): IntegrationHookData
    {
        return IntegrationHookData::from($this->httpPost($this->accountPath('integrations/hooks'), $attributes)->all());
    }

    /**
     * Update an integration hook.
     *
     * @param  array<string,mixed>  $attributes  e.g. ['settings'=>[…]]
     *
     * @throws ChatwootApiException
     */
    public function updateHook(int $hookId, array $attributes): IntegrationHookData
    {
        return IntegrationHookData::from($this->httpPatch($this->accountPath("integrations/hooks/{$hookId}"), $attributes)->all());
    }

    /**
     * Delete an integration hook. Returns true on success.
     *
     * @throws ChatwootApiException
     */
    public function deleteHook(int $hookId): bool
    {
        $this->httpDelete($this->accountPath("integrations/hooks/{$hookId}"));

        return true;
    }
}
