<?php

declare(strict_types=1);

return [
    /*
     | Base URL of the Chatwoot installation (self-hosted). The Application API
     | lives under `{base_url}/api/v1/accounts/{account_id}/…` — the `/api/v1`
     | prefix is added by the transport; do NOT include it here.
     */
    'base_url' => env('CHATWOOT_BASE_URL', 'https://chatwoot.a20.com.ua'),

    /*
     | Client API (API-channel) — `inbox_identifier` shown on the inbox settings
     | screen. This is the auth for the public Client API used by the inbound
     | bridge (push the customer's messages as `incoming`); NO agent token needed.
     | Override per call via ChatwootApi::client($identifier).
     */
    'identifier' => env('CHATWOOT_INBOX_IDENTIFIER'),

    /*
     | Optional HMAC key for Client API identity validation. Only needed when the
     | inbox has `identity_validation_enabled` = true; then we pass
     | identifier_hash = hash_hmac('sha256', <contact identifier>, hmac_key).
     */
    'hmac_key' => env('CHATWOOT_HMAC_KEY'),

    /*
     | Default account id used in the Application API path. Single-account is the
     | common case; override per call via ->accountId($id) on any ApiModel.
     */
    'account_id' => env('CHATWOOT_ACCOUNT_ID'),

    /*
     | Application API access token. Sent in the `api_access_token` header (NOT
     | `Authorization: Bearer`). May be a user/agent token or an agent-bot token.
     | Override per call via ->token($token).
     */
    'token' => env('CHATWOOT_API_TOKEN'),

    /*
     | Platform App access token. Used only by the Platform API resources
     | (`platformAccounts()`, `platformAgentBots()`, `platformUsers()`) which
     | provision accounts, users and agent bots at the installation level. Sent
     | in the same `api_access_token` header, but is a different token than the
     | Application API one above. Override per call via ->token($token).
     */
    'platform_token' => env('CHATWOOT_PLATFORM_TOKEN'),
];
