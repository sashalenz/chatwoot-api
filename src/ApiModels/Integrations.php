<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
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
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function apps(): Collection
    {
        return $this->httpGet($this->accountPath('integrations/apps'));
    }

    /**
     * Create an integration hook (connect an app, optionally to an inbox).
     *
     * @param  array<string,mixed>  $attributes  e.g. ['app_id'=>'dialogflow', 'inbox_id'=>1, 'settings'=>[…]]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function createHook(array $attributes): Collection
    {
        return $this->httpPost($this->accountPath('integrations/hooks'), $attributes);
    }

    /**
     * Update an integration hook.
     *
     * @param  array<string,mixed>  $attributes  e.g. ['settings'=>[…]]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function updateHook(int $hookId, array $attributes): Collection
    {
        return $this->httpPatch($this->accountPath("integrations/hooks/{$hookId}"), $attributes);
    }

    /**
     * Delete an integration hook.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function deleteHook(int $hookId): Collection
    {
        return $this->httpDelete($this->accountPath("integrations/hooks/{$hookId}"));
    }
}
