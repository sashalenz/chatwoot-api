<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\Data;

/**
 * An account webhook subscription.
 */
final class WebhookData extends ChatwootData
{
    /**
     * @param  array<int, string>  $subscriptions
     */
    public function __construct(
        public ?int $id = null,
        public ?int $accountId = null,
        public ?string $url = null,
        public ?string $name = null,
        public ?string $secret = null,
        public array $subscriptions = [],
    ) {}
}
