<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi;

use Sashalenz\ChatwootApi\ApiModels\Account;
use Sashalenz\ChatwootApi\ApiModels\AgentBots;
use Sashalenz\ChatwootApi\ApiModels\Agents;
use Sashalenz\ChatwootApi\ApiModels\AutomationRules;
use Sashalenz\ChatwootApi\ApiModels\CannedResponses;
use Sashalenz\ChatwootApi\ApiModels\Contacts;
use Sashalenz\ChatwootApi\ApiModels\Conversations;
use Sashalenz\ChatwootApi\ApiModels\CustomAttributeDefinitions;
use Sashalenz\ChatwootApi\ApiModels\CustomFilters;
use Sashalenz\ChatwootApi\ApiModels\HelpCenter;
use Sashalenz\ChatwootApi\ApiModels\Inboxes;
use Sashalenz\ChatwootApi\ApiModels\Integrations;
use Sashalenz\ChatwootApi\ApiModels\Labels;
use Sashalenz\ChatwootApi\ApiModels\Messages;
use Sashalenz\ChatwootApi\ApiModels\Profile;
use Sashalenz\ChatwootApi\ApiModels\PublicClient;
use Sashalenz\ChatwootApi\ApiModels\Reports;
use Sashalenz\ChatwootApi\ApiModels\Teams;
use Sashalenz\ChatwootApi\ApiModels\Webhooks;

/**
 * Static entrypoint, mirroring `MonobankApi`/`ViberBotApi`.
 *
 *   ChatwootApi::contacts()->create(['name' => 'Petro', 'inbox_id' => 1]);
 *   ChatwootApi::conversations()->create($sourceId, $inboxId);
 *   ChatwootApi::messages()->create($conversationId, 'Hello', 'incoming');
 *
 * Multi-account / multi-token consumers override per call:
 *   ChatwootApi::messages()->accountId(2)->token($t)->create(…);
 */
class ChatwootApi
{
    public static function contacts(): Contacts
    {
        return new Contacts;
    }

    public static function conversations(): Conversations
    {
        return new Conversations;
    }

    public static function messages(): Messages
    {
        return new Messages;
    }

    public static function inboxes(): Inboxes
    {
        return new Inboxes;
    }

    public static function agents(): Agents
    {
        return new Agents;
    }

    public static function agentBots(): AgentBots
    {
        return new AgentBots;
    }

    public static function teams(): Teams
    {
        return new Teams;
    }

    public static function labels(): Labels
    {
        return new Labels;
    }

    public static function cannedResponses(): CannedResponses
    {
        return new CannedResponses;
    }

    public static function customAttributeDefinitions(): CustomAttributeDefinitions
    {
        return new CustomAttributeDefinitions;
    }

    public static function customFilters(): CustomFilters
    {
        return new CustomFilters;
    }

    public static function account(): Account
    {
        return new Account;
    }

    public static function profile(): Profile
    {
        return new Profile;
    }

    public static function automationRules(): AutomationRules
    {
        return new AutomationRules;
    }

    public static function webhooks(): Webhooks
    {
        return new Webhooks;
    }

    public static function integrations(): Integrations
    {
        return new Integrations;
    }

    public static function reports(): Reports
    {
        return new Reports;
    }

    public static function helpCenter(): HelpCenter
    {
        return new HelpCenter;
    }

    /**
     * Client API (API-channel) — the inbound bridge surface. Auth is the inbox
     * identifier (config `chatwoot-api.identifier`), no agent token.
     */
    public static function client(?string $identifier = null): PublicClient
    {
        return new PublicClient($identifier);
    }
}
