<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * A configured integration hook (an app connected to the account / an inbox).
 */
final class IntegrationHookData extends ChatwootData
{
    /**
     * @param  array<string, mixed>  $settings
     */
    public function __construct(
        public ?int $id = null,
        public ?int $accountId = null,
        public ?int $inboxId = null,
        public ?string $appId = null,
        public ?string $status = null,
        public ?string $hookType = null,
        public array $settings = [],
    ) {}
}
