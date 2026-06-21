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
];
