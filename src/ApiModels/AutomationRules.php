<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Sashalenz\ChatwootApi\Data\AutomationRuleData;
use Sashalenz\ChatwootApi\Data\Paginated;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Automation Rules.
 *
 * @see https://developers.chatwoot.com/api-reference/automation-rule
 */
final class AutomationRules extends BaseModel
{
    /**
     * @return Paginated<AutomationRuleData>
     *
     * @throws ChatwootApiException
     */
    public function list(): Paginated
    {
        return Paginated::fromResponse($this->httpGet($this->accountPath('automation_rules'))->all(), AutomationRuleData::class);
    }

    /**
     * @throws ChatwootApiException
     */
    public function get(int $automationRuleId): AutomationRuleData
    {
        $resp = $this->httpGet($this->accountPath("automation_rules/{$automationRuleId}"))->all();

        return AutomationRuleData::from($resp['payload'] ?? $resp);
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'event_name'=>'conversation_created', 'active'=>true, 'conditions'=>[…], 'actions'=>[…]]
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): AutomationRuleData
    {
        $resp = $this->httpPost($this->accountPath('automation_rules'), $attributes)->all();

        return AutomationRuleData::from($resp['payload'] ?? $resp);
    }

    /**
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function update(int $automationRuleId, array $attributes): AutomationRuleData
    {
        $resp = $this->httpPatch($this->accountPath("automation_rules/{$automationRuleId}"), $attributes)->all();

        return AutomationRuleData::from($resp['payload'] ?? $resp);
    }

    /**
     * Delete an automation rule. Returns true on success.
     *
     * @throws ChatwootApiException
     */
    public function delete(int $automationRuleId): bool
    {
        $this->httpDelete($this->accountPath("automation_rules/{$automationRuleId}"));

        return true;
    }
}
