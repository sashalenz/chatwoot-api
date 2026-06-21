<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Automation Rules.
 *
 * @see https://developers.chatwoot.com/api-reference/automation-rule
 */
final class AutomationRules extends BaseModel
{
    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function list(): Collection
    {
        return $this->httpGet($this->accountPath('automation_rules'));
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function get(int $automationRuleId): Collection
    {
        return $this->httpGet($this->accountPath("automation_rules/{$automationRuleId}"));
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'event_name'=>'conversation_created', 'active'=>true, 'conditions'=>[…], 'actions'=>[…]]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): Collection
    {
        return $this->httpPost($this->accountPath('automation_rules'), $attributes);
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $automationRuleId, array $attributes): Collection
    {
        return $this->httpPatch($this->accountPath("automation_rules/{$automationRuleId}"), $attributes);
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function delete(int $automationRuleId): Collection
    {
        return $this->httpDelete($this->accountPath("automation_rules/{$automationRuleId}"));
    }
}
